<?php

namespace App\Http\Controllers\Freelancer;

use Exception;
use Illuminate\Http\Request;
use App\Services\SliderService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSliderRequest;
use App\Http\Requests\Admin\UpdateSliderRequest;

class SliderController extends Controller
{
    protected $sliderService;

    public function __construct(SliderService $sliderService)
    {
        $this->sliderService = $sliderService;
    }

    public function webSliders()
    {
        $sliders = $this->sliderService->getAllByPlatform($platform = 'web');
        return view('pages.sliders.index', compact('sliders'));
    }
    public function mobileSliders()
    {
        $sliders = $this->sliderService->getAllByPlatform($platform = 'mobile');
        return view('pages.intro_screens.index', compact('sliders'));
    }
    public function createWebSliders()
    {
        return view('pages.sliders.create');
    }
    public function createMobileSliders()
    {
        return view('pages.intro_screens.create');
    }
    public function storeWebSlider(StoreSliderRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['platform'] = 'web';
            $this->sliderService->create($validatedData);
            return redirect()->route('sliders.webSliders')->with('success', __('slider_created_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('something_went_wrong'))->withInput();
        }
    }
    public function storeMobileSlider(StoreSliderRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['platform'] = 'mobile';
            $this->sliderService->create($validatedData);
            return redirect()->route('sliders.mobileSliders')->with('success', __('slider_created_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('something_went_wrong'))->withInput();
        }
    }
    public function editWebSliders($id)
    {
        $slider = $this->sliderService->getById($id);
        return view('pages.sliders.edit', compact('slider'));
    }
    public function editMobileSliders($id)
    {
        $slider = $this->sliderService->getById($id);
        return view('pages.intro_screens.edit', compact('slider'));
    }
    public function updateWebSlider(UpdateSliderRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['platform'] = 'web';
            $this->sliderService->update($id, $validatedData);
            return redirect()->route('sliders.webSliders')->with('success', __('slider_updated_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('something_went_wrong'));
        }
    }
    public function updateMobileSlider(UpdateSliderRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['platform'] = 'mobile';
            $this->sliderService->update($id, $validatedData);
            return redirect()->route('sliders.mobileSliders')->with('success', __('slider_updated_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('something_went_wrong'));
        }
    }
    public function show($id)
    {
        $slider = $this->sliderService->getById($id);
        return view('pages.sliders.show', compact('slider'));
    }
    public function destroy($id)
    {
        try {
            $this->sliderService->delete($id);
            return redirect()->route('sliders.index')->with('success', __('slider_deleted_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('something_went_wrong'));
        }
    }
    public function updateActivation(Request $request)
    {
        try {
            $slider = $this->sliderService->updateActivation($request->id);
            return $this->successResponse('success');
        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());
        }
    }
}
