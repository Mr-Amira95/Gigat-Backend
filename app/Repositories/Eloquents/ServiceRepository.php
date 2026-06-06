<?php

namespace App\Repositories\Eloquents;

use Exception;
use App\Models\Tag;
use App\Models\Filter;
use App\Models\Service;
use App\Models\PlanFeature;
use App\Models\ServiceMedia;
use App\Traits\PaginateTrait;
use App\Utilities\FileManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\ServiceRepositoryInterface;
use App\Utilities\GoogleTranslator;

class ServiceRepository implements ServiceRepositoryInterface
{
    use PaginateTrait;
    protected $model;
    public function __construct(Service $service)
    {
        $this->model = $service;
    }
    public function index()
    {
        return $this->model->with('subCategory', 'user')->orderBy('id', 'desc')->get();
    }
    public function getForFreelancer($id)
    {
        return $this->model->where('user_id', $id)->with('subCategory', 'user', 'media', 'user.profession')->orderBy('id', 'desc')->get();
    }
    public function getAllActive($perPage = null)
    {
        $query = $this->model
            ->with('reviews')
            ->status('approved')
            ->where('is_active', true)
            ->orderBy('id', 'desc');

        return $this->paginate($query, $perPage);
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }
    public function getBySubCategory($subCategoryId, $perPage = null)
    {
        $query = $this->model
            ->where('sub_category_id', $subCategoryId)
            ->with(['media', 'user.profession'])
            ->status('approved')
            ->where('is_active', true)
            ->orderBy('id', 'desc');
        return $this->paginate($query, $perPage);
    }
    public function getServicesByTag($tag, $perPage = null)
    {
        $query = $this->model
            ->whereHas('tags', function ($query) use ($tag) {
                $query->where('slug', $tag);
            })
            ->with(['media', 'user.profession'])
            ->status('approved')
            ->where('is_active', true)
            ->orderBy('id', 'desc');

        return $this->paginate($query, $perPage);
    }

    public function getServicesByUserId($userId, $perPage = null)
    {
        $query = $this->model
            ->with(['media', 'user.profession'])
            ->where('user_id', $userId)
            ->status('approved')
            ->orderBy('id', 'desc');
        return $this->paginate($query, $perPage);
    }
    public function getRecommendedServices($perPage = null)
    {
        $query = $this->model
            ->with(['media', 'user.profession'])
            ->where('is_recommended', true)
            ->status('approved')
            ->where('is_active', true)
            ->orderBy('id', 'desc');

        return $this->paginate($query, $perPage);
    }

    public function getFeaturedServices($perPage = null)
    {
        $query = $this->model
            ->with(['media', 'user.profession'])
            ->where('is_featured', true)
            ->status('approved')
            ->where('is_active', true)
            ->orderBy('id', 'desc');

        return $this->paginate($query, $perPage);
    }

    // public function getServiceDetails($serviceId)
    // {
    //     return $this->model->with(['media', 'user.profession', 'reviews'])->find($serviceId);
    // }
    public function getServiceDetails($serviceId)
    {
        $service = $this->model
            ->with(['media', 'user.profession', 'reviews'])
            ->find($serviceId);

        if (!$service) {
            return null;
        }

        if (auth('admin')->check()) {
            return $service;
        }
        // Logged-in user OR null if guest
        $user = auth('api')->user() ?? null;
        // Guest = no user at all
        $isGuest = !$user;
        // Client = user exists but does NOT have freelancer profile
        $isClient = $user && $user->freelancer === null;
        // If service is inactive and (client OR guest), hide it
        if (($isClient || $isGuest) && $service->is_active == false) {
            return null;
        }

        return $service;
    }


    public function getRelatedServices($serviceId)
    {
        $service = $this->find($serviceId);

        return $this->model
            ->with(['media', 'user.profession'])
            ->where('sub_category_id', $service->sub_category_id)
            ->where('id', '!=', $serviceId)
            ->where('is_active', true)
            ->inRandomOrder()
            ->take(10)
            ->get();
    }

    public function getRelatedServicesByCategory($categoryId)
    {
        return $this->model
            ->with(['media', 'user.profession'])
            ->whereHas('subCategory', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->status('approved')
            ->inRandomOrder()
            ->take(6)
            ->get();
    }


    public function getPlansByServiceId($serviceId)
    {
        $service = $this->find($serviceId);

        $servicePlans = $service->features->load([
            'plan',
        ])->groupBy('plan_id')->map(function ($features, $planId) {
            return (object) [
                'plan_id' => $planId,
                'plan' => $features->first()->plan,
                'features' => $features,
            ];
        })->values();

        return $servicePlans;
    }

    public function search($searchQuery, $subCategoryId, $perPage = null)
    {
        if (strlen($searchQuery) <= 3) {
            return [
                'data' => collect([]),
                'meta' => [
                    'total' => 0,
                    'per_page' => $perPage,
                    'current_page' => 1
                ]
            ];
        }
        $query = $this->model
            ->whereHas('translation', function ($query) use ($searchQuery) {
                $query->where(function ($q) use ($searchQuery) {
                    $words = array_filter(explode(' ', $searchQuery), function ($word) {
                        return strlen($word) >= 2;
                    });
                    foreach ($words as $word) {
                        $q->orWhere('title', 'LIKE', "%{$word}%")
                            ->orWhere('description', 'LIKE', "%{$word}%");
                    }
                });
            })
            ->where('sub_category_id', $subCategoryId)
            ->with(['media', 'user.profession'])
            ->status('approved')
            ->orderBy('id', 'desc');
        return $this->paginate($query, $perPage);
    }
    public function getResultFilter($filters)
    {
        if (!empty($filters)) {
            // Extract the filter IDs from the provided filters
            $filterIds = array_map(function ($filter) {
                return $filter['filter_id'];
            }, $filters);
            // Retrieve the keys associated with those filters
            $filterKeys = Filter::whereIn('id', $filterIds)->pluck('key', 'id');
            // Initialize an empty array to store the filtered plan features
            $filteredPlanFeatures = collect();
            // Loop through each filter to compare values
            foreach ($filters as $filter) {
                // Get the key for the current filter
                $filterKey = $filterKeys[$filter['filter_id']] ?? null;
                // Continue if no key exists for this filter
                if (!$filterKey) continue;
                // Now, let's fetch the PlanFeatures based on the filter type (key)
                $planFeatures = PlanFeature::where('type', $filterKey)->get();
                // Compare the feature values with the values sent in the request
                foreach ($planFeatures as $planFeature) {
                    switch ($filterKey) {
                        case 'price':
                            // If filter type is price, compare the 'value' field
                            if (isset($filter['min']) && isset($filter['max'])) {
                                if ($planFeature->value >= $filter['min'] && $planFeature->value <= $filter['max']) {
                                    $filteredPlanFeatures->push($planFeature);
                                }
                            }
                            break;
                        case 'delivery_days':
                            // If filter type is delivery_days, compare the 'value' field
                            if ($planFeature->value == $filter['value']) {
                                $filteredPlanFeatures->push($planFeature);
                            }
                            break;
                        case 'revisions':
                            // If filter type is revisions, compare the 'value' field
                            if ($planFeature->value == $filter['value']) {
                                $filteredPlanFeatures->push($planFeature);
                            }
                            break;
                        case 'source_files':
                            // If filter type is checkbox (true/false), compare the 'value' field
                            if ((bool)$planFeature->value === (bool)$filter['value']) {
                                $filteredPlanFeatures->push($planFeature);
                            }
                            break;
                        case 'dropdown':
                        case 'dropdown_multiple':
                            // If filter type is dropdown or multiple dropdown, compare the options
                            $options = $filter['options'] ?? [];
                            if ($options) {
                                foreach ($options as $option) {
                                    if (in_array($option['id'], $planFeature->value)) {
                                        $filteredPlanFeatures->push($planFeature);
                                    }
                                }
                            }
                            break;
                        default:
                            // For any other custom type, you can add your own comparisons
                            break;
                    }
                }
            }
            return $filteredPlanFeatures->pluck('service_id')->unique();
        }
        // Return an empty collection if no filters are provided
        return collect();
    }
    public function create($data)
    {

        try {
            DB::beginTransaction();

            // Create the Service record
            $service = $this->model->create([
                'sub_category_id' => $data['sub_category_id'],
                'user_id' => $data['user_id'] ?? throw new \InvalidArgumentException('user_id is required'),
                'status' => 'approved',
                'is_recommended' => true
            ]);

            // Create placeholder translations immediately; async job will overwrite with real translations
            foreach (['en', 'ar'] as $locale) {
                $service->translations()->create([
                    'language'    => $locale,
                    'title'       => $data['title'],
                    'description' => $data['description'],
                ]);
            }

            \App\Jobs\TranslateEntityJob::dispatch(
                \App\Models\Service::class, $service->id, $data['title'], 'title'
            );
            \App\Jobs\TranslateEntityJob::dispatch(
                \App\Models\Service::class, $service->id, $data['description'], 'description'
            );

            // Handle the service plans and features
            foreach ($data['plans'] as $planData) {
                $service->plans()->attach($planData['plan_id']);

                // Handle the features for this plan
                foreach ($planData['features'] as $feature) {
                    $featureModel = $service->features()->updateOrCreate(
                        [
                            'plan_id' => $planData['plan_id'],
                            'type' => $feature['type']
                        ],
                        [
                            'value' => $feature['value'],
                        ]
                    );

                    // Try to get translation from lang files if it exists
                    $key = strtolower(str_replace(' ', '_', $feature['type'] ?? ''));

                    foreach (['en', 'ar'] as $locale) {
                        $titleFromLang = __("features.$key", locale: $locale);

                        $featureModel->translations()->updateOrCreate(
                            ['language' => $locale],
                            [
                                // Use lang file if found, otherwise fall back to provided title
                                'title' => $titleFromLang !== "features.$key"
                                    ? $titleFromLang
                                    : $feature['title'],
                            ]
                        );
                    }
                }
            }


            // Handle tags (if any)
            if (!empty($data['tags'])) {
                $tagIds = Tag::whereIn('slug', $data['tags'])->pluck('id')->toArray();
                $service->tags()->sync($tagIds);
            }

            // Handle media files (if any)
            if (!empty($data['media'])) {
                foreach ($data['media'] as $media) {
                    $mediaPath = FileManager::upload('services', $media);
                    $fileType = FileManager::getFileTypeFromPath($mediaPath);
                    $service->media()->create([
                        'media_path' => $mediaPath,
                        'media_type' => $fileType,
                    ]);
                }
            }

            // Handle cover image (if any)
            if (!empty($data['cover'])) {
                $coverPath = FileManager::upload('services', $data['cover']);
                $coverType = FileManager::getFileTypeFromPath($coverPath);
                $service->media()->create([
                    'media_path' => $coverPath,
                    'media_type' => $coverType,
                    'is_cover' => true,
                ]);
            }

            DB::commit();
            return $service;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function update($data, $service)
    {

        try {
            DB::beginTransaction();

            // Update the Service record
            $service->update([
                'sub_category_id' => $data['sub_category_id'],
                'status' => 'approved',
                'is_active'       => $data['is_active'] ?? $service->is_active,
            ]);

            // Update Translations for Service
            $translator = new GoogleTranslator();
            $titleTranslations = $translator->translateForStorage($data['title']);
            $descriptionTranslations = $translator->translateForStorage($data['description']);

            foreach (['en', 'ar'] as $locale) {
                $service->translations()->updateOrCreate(
                    ['language' => $locale],
                    [
                        'title'       => $titleTranslations[$locale],
                        'description' => $descriptionTranslations[$locale],
                    ]
                );
            }

            // Detach existing plans and re-attach the new ones
            $service->plans()->detach();
            foreach ($data['plans'] as $planData) {
                $service->plans()->attach($planData['plan_id']);

                // Handle the features for this plan
                foreach ($planData['features'] as $feature) {
                    $featureModel = $service->features()->updateOrCreate(
                        [
                            'plan_id' => $planData['plan_id'],
                            'type' => $feature['type']
                        ],
                        [
                            'value' => $feature['value'],
                        ]
                    );

                    $key = strtolower(str_replace(' ', '_', $feature['type'] ?? ''));

                    foreach (['en', 'ar'] as $locale) {
                        $titleFromLang = __("features.$key", locale: $locale);

                        $featureModel->translations()->updateOrCreate(
                            ['language' => $locale],
                            [
                                // Use lang file if found, otherwise fall back to provided title
                                'title' => $titleFromLang !== "features.$key"
                                    ? $titleFromLang
                                    : $feature['title'],
                            ]
                        );
                    }
                }
            }

            // Handle tags (if any)
            if (!empty($data['tags'])) {
                $tagIds = Tag::whereIn('slug', $data['tags'])->pluck('id')->toArray();
                $service->tags()->sync($tagIds);
            }

            // Handle media files (if any)
            if (!empty($data['media'])) {
                // $service->media()->where('is_cover', false)->delete();
                foreach ($data['media'] as $media) {
                    $mediaPath = FileManager::upload('services', $media);
                    $fileType = FileManager::getFileTypeFromPath($mediaPath);
                    $service->media()->create([
                        'media_path' => $mediaPath,
                        'media_type' => $fileType,
                    ]);
                }
            }

            // Handle cover image (if any)
            if (!empty($data['cover'])) {
                $service->media()->where('is_cover', true)->delete();
                $coverPath = FileManager::upload('services', $data['cover']);
                $coverType = FileManager::getFileTypeFromPath($coverPath);
                $service->media()->create([
                    'media_path' => $coverPath,
                    'media_type' => $coverType,
                    'is_cover' => true,
                ]);
            }

            DB::commit();
            return $service;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function delete($id)
    {
        $service = $this->find($id);

        return $service->delete();
    }
    public function deleteMedia($id)
    {
        $media = ServiceMedia::find($id);
        if ($media) {
            $media->delete();
        }
    }
    public function toggleActivation($id)
    {
                // $service = Service::findOrFail($id);
        $service = $this->find($id);
        // dd($service);

        $service->update([
            'is_active' => ! $service->is_active,
        ]);

        return $service->fresh();
    }
}
