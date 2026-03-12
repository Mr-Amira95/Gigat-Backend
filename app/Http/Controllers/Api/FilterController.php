<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\FilterService;
use App\Http\Controllers\Controller;
use App\Http\Resources\FilterResource;
use App\Http\Resources\ServiceDetailsResource;
use App\Http\Resources\ServiceResource;
use App\Models\PlanFeature;
use App\Models\Service;
use App\Services\ServiceService;
use Illuminate\Support\Facades\DB;
use App\Utilities\CurrencyConverter;
use App\Models\Currency;

class FilterController extends Controller
{
    protected $filterService;
    protected $serviceService;

    public function __construct(FilterService $filterService, ServiceService $serviceService)
    {
        $this->filterService = $filterService;
        $this->serviceService = $serviceService;
    }
    public function getFiltersByCategoryId(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);
        $filters = $this->filterService->getFiltersByCategoryId($request->category_id);
        return $this->successResponse(__('success'), FilterResource::collection($filters));
    }

    public function getFilterValues(Request $request)
    {
        // 1. Get currency from query (default = USD)
        $currencyCode = $request->header('currency', 'USD');
        $currencyModel = Currency::where('code', strtoupper($currencyCode))->first();
        $fromCurrency = $currencyModel ? $currencyModel->code : 'USD';
        $symbol = $currencyModel ? $currencyModel->symbol : '$';

        // 2. Get subcategory if provided
        $subCategoryId = $request->query('sub_category_id');

        // 3. Collect service IDs (filtered by subcategory if present)
        $serviceIdsQuery = Service::query();
        if (!empty($subCategoryId)) {
            $serviceIdsQuery->where('sub_category_id', $subCategoryId);
        }
        $serviceIds = $serviceIdsQuery->pluck('id');

        // If no services found, return zeros
        if ($serviceIds->isEmpty()) {
            return $this->successResponse(__('success'), [
                'min_price' => 0,
                'max_price' => 0,
                'min_revisions' => 0,
                'max_revisions' => 0,
                'min_delivery_days' => 0,
                'max_delivery_days' => 0,
            ]);
        }

        // 4. PRICE
        $priceQuery = PlanFeature::where('type', 'price')
            ->whereIn('service_id', $serviceIds);

        $minPriceUSD = $priceQuery->min(DB::raw('CAST(value AS DECIMAL(10,2))'));
        $maxPriceUSD = $priceQuery->max(DB::raw('CAST(value AS DECIMAL(10,2))'));
        // dd($maxPriceUSD, $minPriceUSD);

        $minPrice = $minPriceUSD ? CurrencyConverter::convert($minPriceUSD, 'USD', $fromCurrency) : 0;
        $maxPrice = $maxPriceUSD ? CurrencyConverter::convert($maxPriceUSD, 'USD', $fromCurrency) : 0;
        // ✅ Round prices to nearest integer
        $minRoundPrice = $minPrice ? floor((float) str_replace(',', '', $minPrice)) : 0;
        $maxRoundPrice = $maxPrice ? ceil((float) str_replace(',', '', $maxPrice)) : 0;

        // 5. REVISIONS
        $revisionQuery = PlanFeature::where('type', 'revisions')
            ->whereIn('service_id', $serviceIds);

        $minRevisions = $revisionQuery->min(DB::raw('CAST(value AS SIGNED)')) ?? 0;
        $maxRevisions = $revisionQuery->max(DB::raw('CAST(value AS SIGNED)')) ?? 0;

        // 6. DELIVERY DAYS
        $deliveryQuery = PlanFeature::where('type', 'delivery_days')
            ->whereIn('service_id', $serviceIds);

        $minDelivery = $deliveryQuery->min(DB::raw('CAST(value AS SIGNED)')) ?? 0;
        $maxDelivery = $deliveryQuery->max(DB::raw('CAST(value AS SIGNED)')) ?? 0;

        // 7. Build response
        $data = [
            'min_price'         =>  $minRoundPrice,
            'max_price'         =>  $maxRoundPrice,
            'min_revisions'     =>  $minRevisions,
            'max_revisions'     =>  $maxRevisions,
            'min_delivery_days' =>  $minDelivery,
            'max_delivery_days' =>  $maxDelivery,
        ];

        return $this->successResponse(__('success'), $data);
    }


    public function applyFilters(Request $request)
    {
        $filters = $request->input('filters', []);

        // Get currency from header
        $currencyCode = $request->currency;
        $currencyModel = Currency::where('code', strtoupper($currencyCode))->first();
        $fromCurrency = $currencyModel ? $currencyModel->code : 'USD';

        // 1. Start with base services query
        $servicesQuery = Service::with(['media', 'user.profession.translation'])->where('is_active', true);

        // 2. Apply search keyword first
        $search = $request->input('search');
        if ($search) {
            $servicesQuery->whereHas('translation', function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // 3. Apply filters if present
        if (!empty($filters)) {
            $serviceIds = null;

            foreach ($filters as $filter) {
                $query = PlanFeature::query();

                // Price filter
                if (isset($filter['filter_type'])) {
                    $type = $filter['filter_type'];
                    $min = $filter['min'] ?? 0;
                    $max = $filter['max'] ?? 999999;

                    if ($type === 'price') {
                        // $convertedMin = CurrencyConverter::convert($min, $fromCurrency, 'USD');
                        // $convertedMax = CurrencyConverter::convert($max, $fromCurrency, 'USD');
                        $convertedMin = (float) str_replace([',', 'USD'], '', CurrencyConverter::convert($min, $fromCurrency, 'USD'));
                        $convertedMax = (float) str_replace([',', 'USD'], '', CurrencyConverter::convert($max, $fromCurrency, 'USD'));
                    } else {
                        $convertedMin = $min;
                        $convertedMax = $max;
                    }
                    // dd($convertedMin, $convertedMax);
                    $query->where('type', $type)
                        ->whereBetween(
                            DB::raw("CAST(REPLACE(value, ',', '') AS DECIMAL(10,2))"),
                            [$convertedMin, $convertedMax]
                        );
                }

                // Source files filter
                if (isset($filter['source_files'])) {
                    $expectedValue = $filter['source_files'] ? 1 : 0;

                    $query->where('type', 'source_files')
                        ->whereIn('value', [$expectedValue, $expectedValue ? 'true' : 'false']);
                }


                $ids = $query->pluck('service_id')->unique();

                if (is_null($serviceIds)) {
                    $serviceIds = $ids;
                } else {
                    $serviceIds = $serviceIds->intersect($ids);
                }
            }

            if ($serviceIds && $serviceIds->isNotEmpty()) {
                $servicesQuery->whereIn('id', $serviceIds);
            } else {
                // No matches after applying filters
                return $this->successResponse(__('success'), ServiceResource::collection(collect()));
            }
        }

        // 4. Subcategory filter (direct on services)
        $subCategoryId = $request->input('sub_category_id');
        if ($subCategoryId) {
            $servicesQuery->where('sub_category_id', $subCategoryId);
        }

        // 5. Rating filter (direct on services)
        $rating = $request->input('rating');
        if ($rating) {
            $minRating = $rating['min'] ?? 0;
            $maxRating = $rating['max'] ?? 5;

            $servicesQuery->whereBetween('rating', [$minRating, $maxRating]);
        }

        // 6. Languages filter (direct on services)
        $languages = $request->input('languages');
        if (!empty($languages)) {
            $servicesQuery->whereHas('user.languages', function ($q) use ($languages) {
                $q->whereIn('language_id', $languages);
            });
        }

        // 7. Execute final query (search + filters combined)
        $services = $servicesQuery->get();

        return $this->successResponse(__('success'), ServiceResource::collection($services));
    }
}
