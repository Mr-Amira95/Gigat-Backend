<?php

namespace App\Repositories\Eloquents;

use App\Enums\FreelancerStatusEnum;
use App\Models\User;
use App\Models\Freelancer;
use App\Models\FreelancerCateogry;
use App\Models\Service;
use App\Models\UserLanguage;
use App\Utilities\FileManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Interfaces\FreelancerRepositoryInterface;
use App\Utilities\GoogleTranslator;

class FreelancerRepository implements FreelancerRepositoryInterface
{
    protected $model;
    protected $freelancer;
    protected $UserLanguage;
    protected $googleTranslator;

    public function __construct(User $user, Freelancer $freelancer, UserLanguage $UserLanguage,  GoogleTranslator $googleTranslator)
    {
        $this->model = $user;
        $this->freelancer = $freelancer;
        $this->UserLanguage = $UserLanguage;
        $this->googleTranslator = $googleTranslator;
    }

    public function index($params, ?int $perPage = 50)
    {
        $query = $this->model
            ->whereHas('freelancer')
            ->with(['freelancer', 'profession', 'country'])
            ->orderByDesc('id');
        if (!empty($params['username'])) {
            $query->where('username', 'like', '%' . $params['username'] . '%');
        }
        if (!empty($params['email'])) {
            $query->where('email', 'like', '%' . $params['email'] . '%');
        }
        if (!empty($params['phone'])) {
            $query->where('phone', 'like', '%' . $params['phone'] . '%');
        }
        if (!empty($params['gender'])) {
            $query->where('gender', $params['gender']);
        }
        if (!empty($params['profession_id'])) {
            $query->where('profession_id', $params['profession_id']);
        }
        if (!empty($params['country_id'])) {
            $query->where('country_id', $params['country_id']);
        }
        // P2-06/PERF-02: paginate by default; pass null to get all (e.g. for exports)
        return $perPage ? $query->paginate($perPage) : $query->get();
    }
    public function store($data)
    {
        return DB::transaction(
            function () use ($data) {
                $user = User::create([
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'prefix' => $data['prefix'] ?? null, 
                    'phone' => $data['phone'] ?? null,
                    'gender' => $data['gender'] ?? null,
                    'profession_id' => $data['profession_id'] ?? null,
                    'country_id' => $data['country_id'] ?? null,
                    'password' => Hash::make($data['password']),
                    'avatar' => isset($data['avatar']) ? FileManager::upload('users', $data['avatar']) : null,
                    'verified_at' => Auth::guard('admin')->check() ? now() : ''
                ]);
                $freelancer = Freelancer::create([
                    'user_id' => $user->id,
                    // 'status' => Auth::guard('admin')->check() ? FreelancerStatusEnum::VERIFIED->value : FreelancerStatusEnum::UNVERIFIED->value,
                    // 'bio' => $data['bio'] ?? null,
                ]);
                if (!empty($data['bio'])) {
                    $translations = $this->googleTranslator->translateForStorage($data['bio']);

                    foreach ($translations as $lang => $text) {
                        $freelancer->translations()->create([
                            'language' => $lang,
                            'bio'      => $text,
                        ]);
                    }
                }
                if (!empty($data['languages'])) {
                    foreach ($data['languages'] as $languageId) {
                        UserLanguage::create([
                            'user_id' => $user->id,
                            'language_id' => $languageId,
                        ]);
                    }
                };

                if (!empty($data['category_ids'])) {
                    foreach ($data['category_ids'] as $categoryId) {
                        FreelancerCateogry::create([
                            'user_id' => $user->id,
                            'category_id' => $categoryId,
                        ]);
                    }
                }
                if (!empty($data['file']) && is_array($data['file'])) {
                    foreach ($data['file'] as $index => $file) {
                        $filePath = FileManager::upload('freelancers', $file);
                        $fileName = pathinfo(strip_tags($file->getClientOriginalName()), PATHINFO_FILENAME);
                        $certificate = $user->certificates()->create([
                            'file_name' => trim($fileName),
                            'file_path' => $filePath,
                        ]);

                        if (!empty($data['description'][$index])) {
                            $translations = $this->googleTranslator->translateForStorage($data['description'][$index]);

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
        );
    }
    public function find($id)
    {
        return $this->model
            ->with([
                'languages.language',
                'profession',
                'country'
            ])
            ->findOrFail($id);
    }
    public function delete($id)
    {
        $client = $this->find($id);
        return $client->delete();
    }
    public function updateActivation($id)
    {
        $client = $this->find($id);
        $client->is_active = !$client->is_active;
        $client->save();
        return $client;
    }

    public function updateVerification($id)
    {
        $freelancer = Freelancer::findOrFail($id);
        $freelancer->status = $freelancer->status == 'verified' ? 'unverified' : 'verified';
        $freelancer->save();

        return $freelancer;
    }
    public function getUserProfile($id)
    {
        return $this->model->with('freelancer', 'languages.language', 'profession', 'country', 'certificates', 'categories.translation')->find($id);
    }


    public function completeProfile(array $data)
    {
        $userId = Auth::id() ?? $data['user_id'];

        $user = $this->model->findOrFail($userId);

        // Upload and update avatar if provided
        if (!empty($data['avatar'])) {
            $user->update([
                'avatar' => FileManager::upload('users', $data['avatar']),
            ]);
        }

        // Create or update freelancer profile
        if ($user->freelancer) {
            $user->freelancer->update([
                'status' => $user->freelancer->status ?? 'unverified',
            ]);

            if (!empty($data['bio'])) {
                $translations = $this->googleTranslator->translateForStorage($data['bio']);

                foreach ($translations as $lang => $text) {
                    $user->freelancer->translations()->updateOrCreate(
                        ['language' => $lang],
                        ['bio' => $text]
                    );
                }
            }
        } else {
            $freelancer =  $user->freelancer()->create([
                'status' => 'unverified',
            ]);

            if (!empty($data['bio'])) {
                $translations = $this->googleTranslator->translateForStorage($data['bio']);

                foreach ($translations as $lang => $text) {
                    $freelancer->translations()->create([
                        'language' => $lang,
                        'bio' => $text,
                    ]);
                }
            }
        }

        // Sync freelancer categories (first delete old ones)
        if (!empty($data['category_ids'])) {
            FreelancerCateogry::where('user_id', $userId)->delete();
            foreach ($data['category_ids'] as $categoryId) {
                FreelancerCateogry::create([
                    'user_id' => $userId,
                    'category_id' => $categoryId,
                ]);
            }
        }

        // Upload certificates
        if (!empty($data['file']) && is_array($data['file'])) {
            foreach ($data['file'] as $index => $file) {
                $filePath = FileManager::upload('freelancers', $file);
                $fileName = pathinfo(strip_tags($file->getClientOriginalName()), PATHINFO_FILENAME);

                $certificate = $user->certificates()->create([
                    'file_name' => trim($fileName),
                    'file_path' => $filePath,
                ]);
                if (!empty($data['description'][$index])) {
                    $translations = $this->googleTranslator->translateForStorage($data['description'][$index]);

                    foreach (['en', 'ar'] as $locale) {
                        $certificate->translations()->create([
                            'language' => $locale,
                            'description' => $translations[$locale],
                        ]);
                    }
                }
            }
        }

        return $this->getUserProfile($userId);
    }

    public function updateProfile($id, array $data)
    {
        $user = $this->model->findOrFail($id);
        $user->fill([
            'username' => $data['username'] ?? $user->username,
            'gender' => $data['gender'] ?? $user->gender,
            'profession_id' => $data['profession_id'] ?? $user->profession_id,
            'country_id' => $data['country_id'] ?? $user->country_id,
        ]);

        // Only update email if the user does NOT have a google_id
        if (isset($data['email']) && empty($user->google_id)) {
            $user->email = $data['email'];
        }

        if (isset($data['avatar'])) {
            $avatarPath = isset($data['avatar']) ? FileManager::upload('users', $data['avatar']) : null;
            $user->avatar = $avatarPath;
        }
        $user->save();
        if ($user->freelancer) {
            $user->freelancer->update([
                'nick_name' => $data['nick_name'] ?? $user->freelancer->nick_name,
                'description' => $data['description'] ?? $user->freelancer->description,
            ]);

            // ✅ Update languages
            if (isset($data['languages'])) {
                // remove old
                $user->languages()->delete();

                // insert new
                foreach ($data['languages'] as $languageId) {
                    $user->languages()->create([
                        'language_id' => $languageId,
                    ]);
                }
            }
        }
        return $this->getUserProfile($id);
    }
    public function getArchived()
    {
        return $this->model
            ->onlyTrashed()
            ->whereHas('freelancer')
            ->with(['freelancer', 'profession', 'country'])
            ->orderByDesc('id')
            ->get();
    }
    public function restore($id)
    {
        return $this->model->withTrashed()->findOrFail($id)->restore();
    }

    public function getByAuthUser()
    {
        $services = Service::where('user_id', auth()->id())->get();

        return response()->json([
            'status' => true,
            'data' => [
                'services' => $services
            ]
        ]);
    }
}
