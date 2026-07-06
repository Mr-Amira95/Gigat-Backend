<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Freelancer\ChangePasswordRequest;
use App\Models\Country;
use App\Models\Freelancer;
use App\Models\FreelancerCateogry;
use App\Models\FreelancerCertificate;
use App\Models\Language;
use App\Models\Profession;
use App\Models\User;
use App\Models\UserLanguage;
use App\Services\CategoryService;
use App\Services\CountryService;
use App\Services\FreelancerService;
use App\Services\LanguageService;
use App\Services\MetaConversionsApiService;
use App\Services\ProfessionService;
use App\Utilities\FileManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{

    protected $freelancerService;
    protected $professionService;
    protected $countryService;
    protected $languageService;
    protected $categoryService;

    public function __construct(FreelancerService $freelancerService, ProfessionService $professionService, CountryService $countryService, LanguageService $languageService, CategoryService $categoryService)
    {
        $this->freelancerService = $freelancerService;
        $this->professionService = $professionService;
        $this->countryService = $countryService;
        $this->languageService = $languageService;
        $this->categoryService = $categoryService;
    }


    public function show()
    {
        $user = Auth::user()->load([
            'country',
            'profession.translations',
            'freelancer',
            'languages.language',
        ]);

        return view('pages-freelancer.profile.show', compact('user'));
    }


    public function edit($id)
    {

        $freelancer = User::findOrFail($id);
        $professions = $this->professionService->getAllActive();
        $countries = $this->countryService->getAllActive();
        $languages = $this->languageService->getAllActive();
        $categories = $this->categoryService->getAllActive();
        return view('pages-freelancer.profile.edit', compact('freelancer', 'professions', 'countries', 'languages', 'categories'));
    }
    public function update(Request $request, $id)
    {
        // 1. تحقق من صحة البيانات
        $validated = $request->validate([
            'avatar' => 'nullable|image|max:2048', // صورة الأفتار اختيارية
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'prefix' => 'required|string|max:10',
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->where(function ($query) use ($request) {
                    return $query->where('prefix', $request->prefix);
                })->ignore($id),
            ],
            'gender' => 'required|in:male,female,prefer_not_say',
            'profession_id' => 'required|exists:professions,id',
            'bio' => 'required|string|max:2000',
            'country_id' => 'required|exists:countries,id',
            'languages' => 'required|array',
            'languages.*' => 'exists:languages,id',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'file.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:4096',  // الشهادات المرفوعة الجديدة
            'description.*' => 'nullable|string|max:255',
            'old_description.*' => 'nullable|string|max:255',

        ]);

        // 2. استدعاء الفريلانسر
        $freelancer = User::with('freelancer', 'certificates')->findOrFail($id);

        // 3. تحديث بيانات الفريلانسر (حقل User الأساسي مثلا)
        $freelancer->username = $validated['username'];
        $freelancer->email = $validated['email'];
        $freelancer->prefix = $validated['prefix'];
        $freelancer->phone = $validated['phone'];
        $freelancer->gender = $validated['gender'];
        $freelancer->profession_id = $validated['profession_id'];
        $freelancer->country_id = $validated['country_id'];

        // رفع صورة الأفتار إذا أرسلها المستخدم
        if ($request->hasFile('avatar')) {
            // حذف الصورة القديمة إذا موجودة
            if ($freelancer->avatar && file_exists(public_path($freelancer->avatar))) {
                unlink(public_path($freelancer->avatar));
            }

            $avatarPath = FileManager::upload('avatars', $request->file('avatar'));
            $freelancer->avatar = $avatarPath;
        }

        $freelancer->save();

        // 4. تحديث البيانات الخاصة بـ freelancer relation (bio مثلاً)
        if ($freelancer->freelancer) {
            $translator = new \App\Utilities\GoogleTranslator();
            $translations = $translator->translateForStorage($validated['bio']);

            foreach ($translations as $lang => $text) {
                $freelancer->freelancer->translations()->updateOrCreate(
                    ['language' => $lang],
                    ['bio' => $text]
                );
            }
        }

        // 5. تحديث اللغات والفئات (علاقات many-to-many)
        // حذف اللغات القديمة
        UserLanguage::where('user_id', $freelancer->id)->delete();

        // إدخال اللغات الجديدة
        if (!empty($request->languages)) {
            foreach ($request->languages as $languageId) {
                UserLanguage::create([
                    'user_id' => $freelancer->id,
                    'language_id' => $languageId,
                ]);
            }
        }


        FreelancerCateogry::where('user_id', $freelancer->id)->delete();

        if (!empty($request->category_ids)) {
            foreach ($request->category_ids as $categoryId) {
                FreelancerCateogry::create([
                    'user_id' => $freelancer->id,
                    'category_id' => $categoryId,
                ]);
            }
        }

        // 7. تحديث أو إضافة وصف للشهادات القديمة (حسب old_description)
        if ($request->has('old_description')) {
            foreach ($request->old_description as $certId => $desc) {
                $certificate = $freelancer->certificates()->find($certId);
                if ($certificate) {
                    if (!empty($desc)) {
                        $translator = new \App\Utilities\GoogleTranslator();
                        $translations = $translator->translateForStorage($desc);

                        foreach (['en', 'ar'] as $locale) {
                            $certificate->translations()->updateOrCreate(
                                ['language' => $locale],
                                ['description' => $translations[$locale]]
                            );
                        }
                    }
                }
            }
        }

        // 8. رفع شهادات جديدة (file[] و description[])
        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $index => $file) {
                if ($file) {
                    $description = $request->description[$index] ?? null;

                    $path = FileManager::upload('certificates', $file);
                    $fileName = pathinfo(strip_tags($file->getClientOriginalName()), PATHINFO_FILENAME);
                    $certificate = $freelancer->certificates()->create([
                        'file_name' => trim($fileName),
                        'file_path' => $path,
                    ]);

                    // Handle translations
                    if (!empty($description)) {
                        $translator = new \App\Utilities\GoogleTranslator();
                        $translations = $translator->translateForStorage($description);

                        foreach (['en', 'ar'] as $locale) {
                            $certificate->translations()->create([
                                'language'    => $locale,
                                'description' => $translations[$locale],
                            ]);
                        }
                    }
                }
            }
        }

        return redirect()->route('freelancer.profile.show')
            ->with('success', __('profile_updated_successfully'));
    }
    public function deleteCertificate($id)
    {
        $certificate = FreelancerCertificate::findOrFail($id);

        // حذف الملف من السيرفر إذا موجود
        if ($certificate->file_path && file_exists(public_path($certificate->file_path))) {
            unlink(public_path($certificate->file_path));
        }

        $certificate->delete();

        return response()->json(['message' => 'تم الحذف بنجاح']);
    }


    // Update Password
    public function updatePassword(ChangePasswordRequest $request)
    {

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            // return back()->withErrors(['current_password' => __('incorrect_current_password')]);
            return redirect()->back()->with('error',  __('incorrect_current_password'));
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', __('password_changed_successfully'));
    }

    // Submit Verification Document
    public function verify(Request $request)
    {
        $request->validate([
            'verification_document' => 'required|mimes:jpeg,png,pdf|max:4096',
        ]);

        $user = Auth::user();

        if ($request->hasFile('verification_document')) {
            if ($user->verification_document && file_exists(public_path($user->verification_document))) {
                unlink(public_path($user->verification_document));
            }

            $path = FileManager::upload('verifications', $request->file('verification_document'));

            $user->freelancer->file = $path;
            // $user->is_verified = false; // pending
            $user->save();

            app(MetaConversionsApiService::class)->dispatchEvent($user, $request, 'Freelancer Submit Account Verification Request');
        }

        return back()->with('success', __('verification_document_submitted'));
    }
}
