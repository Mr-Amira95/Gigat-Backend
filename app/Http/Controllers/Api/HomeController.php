<?php

namespace App\Http\Controllers\Api;

use App\Services\HomeService;
use App\Http\Resources\FaqResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SliderResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PortfolioResource;

class HomeController extends Controller
{
    public function __construct(private readonly HomeService $homeService) {}

    public function sliders()
    {
        return $this->successResponse(__('success'), SliderResource::collection($this->homeService->getSliders('mobile')));
    }

    public function homeMobile()
    {
        return $this->successResponse(__('success'), $this->homeService->getHomeMobileData());
    }
 
    public function homePage()
    {
        return $this->successResponse(__('success'), $this->homeService->getHomePageData());
    }
    public function homeFreelancer(){
        return $this->successResponse(__('success'),$this->homeService->getHomeFreelancerData());
    }
}
