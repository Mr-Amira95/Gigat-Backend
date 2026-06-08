<?php

namespace App\Repositories\Eloquents;

use App\Models\Company;
use App\Models\Freelancer;
use App\Models\FreelancerCateogry;
use App\Models\PlayerId;
use App\Models\UserLanguage;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use App\Services\WhatsAppService;
use App\Utilities\FileManager;
use App\Utilities\GenerateCode;
use App\Utilities\GoogleTranslator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompanyRepository implements CompanyRepositoryInterface
{
    protected $model;
    protected $googleTranslator;

    public function __construct(Company $company, GoogleTranslator $googleTranslator)
    {
        $this->model = $company;
        $this->googleTranslator = $googleTranslator;
    }

    public function registerCompany($data)
    {
        return DB::transaction(function () use ($data) {

            /*
        |--------------------------------------------------------------------------
        | 1) CREATE OR RESTORE USER (Register client)
        |--------------------------------------------------------------------------
        */

            $localPhone = ltrim(preg_replace('/\D+/', '', $data['phone']), '0');
            $data['phone'] = $localPhone;

            // $code = GenerateCode::generate();
            $code = '123456';

            $user = \App\Models\User::withTrashed()
                ->where('email', $data['email'])
                ->orWhere(function ($q) use ($data) {
                    $q->where('prefix', $data['prefix'])
                        ->whereIn('phone', [$data['phone'], '0' . $data['phone']]);
                })
                ->first();

            if ($user && $user->trashed()) {
                $user->restore();
                $user->update([
                    'is_active'     => true,
                    'username'      => $data['username'],
                    'email'         => $data['email'],
                    'prefix'        => $data['prefix'],
                    'phone'         => $data['phone'],
                    'gender'        => $data['gender'] ?? null,
                    'profession_id' => $data['profession_id'] ?? null,
                    'country_id'    => $data['country_id'] ?? null,
                    'password'      => Hash::make($data['password']),
                    'avatar'        => $data['avatar'] ?? null,
                    'google_id'     => $data['google_id'] ?? null,
                    'code'          => $code,
                    'verified_at'   => null,
                ]);

                if ($user->freelancer) {
                    $user->services()->withTrashed()->restore();
                    $user->portfolios()->withTrashed()->restore();
                }
            }

            if (!$user) {
                $user = \App\Models\User::create([
                    'username'      => $data['username'],
                    'email'         => $data['email'],
                    'prefix'        => $data['prefix'],
                    'phone'         => $data['phone'],
                    'gender'        => $data['gender'] ?? null,
                    'profession_id' => $data['profession_id'] ?? null,
                    'country_id'    => $data['country_id'],
                    'password'      => Hash::make($data['password']),
                    'avatar'        => $data['avatar'] ?? null,
                    'google_id'     => $data['google_id'] ?? null,
                    'is_active'     => true,
                    'code'          => $code,
                ]);
            }

            // Save languages
            if (!empty($data['languages'])) {
                UserLanguage::where('user_id', $user->id)->delete();
                foreach ($data['languages'] as $lang) {
                    UserLanguage::create([
                        'user_id' => $user->id,
                        'language_id' => $lang,
                    ]);
                }
            }

            // Save OneSignal Player ID
            if (!empty($data['player_id'])) {
                PlayerId::firstOrCreate([
                    'user_id'   => $user->id,
                    'player_id' => $data['player_id'],
                    'platform'  => $data['platform'],
                ]);
            }

            if (!empty($data['avatar'])) {
                $user->update([
                    'avatar' => FileManager::upload('users', $data['avatar']),
                ]);
            }

            // Send OTP
            $fullPhone = $data['prefix'] . $data['phone'];
            $whatsapp = new WhatsAppService();
            $whatsapp->sendTemplateMessage($fullPhone, $code);


            /*
        |--------------------------------------------------------------------------
        | 2) COMPLETE FREELANCER PROFILE
        |--------------------------------------------------------------------------
        */

            $freelancer = $user->freelancer ?: $user->freelancer()->create([
                'status' => 'unverified',
            ]);

            // Bio translation
            if (!empty($data['bio'])) {
                $translations = $this->googleTranslator->translateForStorage($data['bio']);
                foreach (['en', 'ar'] as $lang) {
                    $freelancer->translations()->updateOrCreate(
                        ['language' => $lang],
                        ['bio' => $translations[$lang]]
                    );
                }
            }

            // Category sync
            if (!empty($data['category_ids'])) {
                FreelancerCateogry::where('user_id', $user->id)->delete();
                foreach ($data['category_ids'] as $cat) {
                    FreelancerCateogry::create([
                        'user_id' => $user->id,
                        'category_id' => $cat,
                    ]);
                }
            }

            // Certificates upload
            if (!empty($data['file'])) {
                foreach ($data['file'] as $i => $file) {
                    $path = FileManager::upload('freelancers', $file);
                    $name = pathinfo(strip_tags($file->getClientOriginalName()), PATHINFO_FILENAME);

                    $cert = $user->certificates()->create([
                        'file_name' => $name,
                        'file_path' => $path,
                    ]);

                    if (!empty($data['description'][$i])) {
                        $desc = $this->googleTranslator->translateForStorage($data['description'][$i]);
                        foreach (['en', 'ar'] as $lang) {
                            $cert->translations()->create([
                                'language' => $lang,
                                'description' => $desc[$lang],
                            ]);
                        }
                    }
                }
            }


            /*
        |--------------------------------------------------------------------------
        | 3) CREATE COMPANY
        |--------------------------------------------------------------------------
        */

            $company = $this->model->create([
                'logo' => isset($data['logo']) ? FileManager::upload('companies', $data['logo']) : null,
                'country_of_registration' => $data['country_of_registration'],
                'registration_number'     => $data['registration_number'],
                'contact_email'           => $data['contact_email'],
                'contact_phone_number'    => $data['contact_phone_number'],
                'website_url'             => $data['website_url'] ?? null,
            ]);

            // Translations
            $nameT = $this->googleTranslator->translateForStorage($data['company_name']);
            $descT = $this->googleTranslator->translateForStorage($data['company_description'] ?? '');
            $countryT = $this->googleTranslator->translateForStorage($data['country_of_registration']);

            foreach (['en', 'ar'] as $lang) {
                $company->translations()->create([
                    'language' => $lang,
                    'name' => $nameT[$lang],
                    'description' => $descT[$lang],
                    'country_of_registration' => $countryT[$lang],
                ]);
            }

            // Social links
            if (!empty($data['social_links'])) {
                foreach ($data['social_links'] as $link) {
                    $icon = isset($link['icon']) ? FileManager::upload('social-links', $link['icon']) : null;

                    $social = $company->socialLinks()->create([
                        'icon' => $icon,
                        'url' => $link['url'] ?? null,
                    ]);

                    if (!empty($link['title'])) {
                        $titleT = $this->googleTranslator->translateForStorage($link['title']);

                        foreach (['en', 'ar'] as $lang) {
                            $social->translations()->create([
                                'language' => $lang,
                                'title' => $titleT[$lang],
                            ]);
                        }
                    }
                }
            }

            // Link company to freelancer
            $freelancer->update(['company_id' => $company->id]);


            /*
        |--------------------------------------------------------------------------
        | 4) RETURN RESPONSE
        |--------------------------------------------------------------------------
        */

            return [
                'user'       => $user->load(['profession', 'country', 'languages.language']),
                'freelancer' => $freelancer->load('translations'),
                'company'    => $company->load(['translations', 'socialLinks.translations']),
            ];
        });
    }


    public function index($params = [])
    {
        $query = $this->model->with('translations');

        if (!empty($params['name'])) {
            $query->whereHas('translations', function ($q) use ($params) {
                $q->where('name', 'like', '%' . $params['name'] . '%');
            });
        }

        if (!empty($params['country_of_registration'])) {
            $query->where('country_of_registration', $params['country_of_registration']);
        }


        return $query->orderByDesc('id')->get();
    }



    public function find($id)
    {
        return $this->model->with(['translations', 'socialLinks.translations'])->findOrFail($id);
    }

    public function store($data)
    {
        return DB::transaction(function () use ($data) {

            // Create the company record
            $company = $this->model->create([
                'logo' => isset($data['logo']) ? FileManager::upload('companies', $data['logo']) : null,
                'country_of_registration' => $data['country_of_registration'],
                'registration_number' => $data['registration_number'],
                'contact_email' => $data['contact_email'],
                'contact_phone_number' => $data['contact_phone_number'],
                'website_url' => $data['website_url'] ?? null,
            ]);

            // Translate company name and description
            $nameTranslations = $this->googleTranslator->translateForStorage($data['name']);
            $descTranslations = $this->googleTranslator->translateForStorage($data['description'] ?? '');
            $countryTranslations = $this->googleTranslator->translateForStorage($data['country_of_registration']);

            foreach (['en', 'ar'] as $lang) {
                $company->translations()->create([
                    'language' => $lang,
                    'name' => $nameTranslations[$lang],
                    'description' => $descTranslations[$lang] ?? null,
                    'country_of_registration' => $countryTranslations[$lang],

                ]);
            }

            // Store social links with translations if provided
            if (!empty($data['social_links']) && is_array($data['social_links'])) {
                foreach ($data['social_links'] as $index => $link) {
                    $iconPath = isset($link['icon']) ? FileManager::upload('social-links', $link['icon']) : null;

                    $socialLink = $company->socialLinks()->create([
                        'icon' => $iconPath,
                        'url' => $link['url'] ?? null,
                    ]);

                    if (!empty($link['title'])) {
                        $titleTranslations = $this->googleTranslator->translateForStorage($link['title']);

                        foreach (['en', 'ar'] as $lang) {
                            $socialLink->translations()->create([
                                'language' => $lang,
                                'title' => $titleTranslations[$lang],
                            ]);
                        }
                    }
                }
            }

            // Link company to freelancer
            $freelancerId = $data['freelancer_id'] ?? auth('api')->id();

            $freelancer = Freelancer::where('user_id', $freelancerId)->first();
            if ($freelancer) {
                $freelancer->update(['company_id' => $company->id]);
            }

            return $company;
        });
    }




    public function update($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {

            // 1️⃣ Find existing company
            $company = $this->model->findOrFail($id);

            // 2️⃣ Update main company fields
            $updateData = [
                'registration_number'     => $data['registration_number'] ?? $company->registration_number,
                'contact_email'           => $data['contact_email'] ?? $company->contact_email,
                'contact_phone_number'    => $data['contact_phone_number'] ?? $company->contact_phone_number,
                'website_url'             => $data['website_url'] ?? $company->website_url,
            ];


            // Replace logo if uploaded
            if (isset($data['logo'])) {
                $updateData['logo'] = FileManager::update(
                    'companies',
                    $data['logo'],
                    $company->logo,
                );
            }

            $company->update($updateData);

            // 3️⃣ Update translations (replace existing ones)
            $company->translations()->delete();

            // Safely handle missing translation keys
            $nameText        = $data['name'] ?? $company->translation->name ?? '';
            $descText        = $data['description'] ?? $company->translation->description ?? '';
            $countryText     = $data['country_of_registration'] ?? $company->translation->country_of_registration ?? '';

            // Translate using Google
            $nameTranslations    = $this->googleTranslator->translateForStorage($nameText);
            $descTranslations    = $this->googleTranslator->translateForStorage($descText);
            $countryTranslations = $this->googleTranslator->translateForStorage($countryText);

            foreach (['en', 'ar'] as $lang) {
                $company->translations()->create([
                    'language' => $lang,
                    'name' => $nameTranslations[$lang],
                    'description' => $descTranslations[$lang] ?? null,
                    'country_of_registration' => $countryTranslations[$lang],
                ]);
            }

            // 4️⃣ Update social links
            if (!empty($data['social_links']) && is_array($data['social_links'])) {
                $company->socialLinks()->delete();

                foreach ($data['social_links'] as $link) {
                    $iconPath = isset($link['icon'])
                        ? FileManager::upload('social-links', $link['icon'])
                        : null;

                    $socialLink = $company->socialLinks()->create([
                        'icon' => $iconPath,
                        'url' => $link['url'] ?? null,
                    ]);

                    if (!empty($link['title'])) {
                        $titleTranslations = $this->googleTranslator->translateForStorage($link['title']);
                        foreach (['en', 'ar'] as $lang) {
                            $socialLink->translations()->create([
                                'language' => $lang,
                                'title' => $titleTranslations[$lang],
                            ]);
                        }
                    }
                }
            }

            // Update freelancer-company assignment
            if (!empty($data['user_id'])) {
                Freelancer::where('company_id', $company->id)
                    ->update(['company_id' => null]);

                $freelancer = Freelancer::where('user_id', $data['user_id'])->first();
                if ($freelancer) {
                    $freelancer->update(['company_id' => $company->id]);
                }
            }
            return $company->fresh(['translations', 'socialLinks.translations']);
        });
    }

    public function getFreelancerCompanyId($freelancerUserId = null)
    {
        $freelancerUserId = $freelancerUserId ?? auth('api')->id();

        $freelancer = Freelancer::where('user_id', $freelancerUserId)->first();

        return $freelancer->company_id ?? null;
    }


    public function checkFreelancerOwnsCompany($companyId, $freelancerUserId = null)
    {
        $freelancerUserId = $freelancerUserId ?? auth('api')->id();

        $freelancer = Freelancer::where('user_id', $freelancerUserId)->first();

        if (!$freelancer || !$freelancer->company_id) {
            return null;
        }

        if ($freelancer->company_id == $companyId) {
            return $freelancer->company_id;
        }

        return null;
    }
}
