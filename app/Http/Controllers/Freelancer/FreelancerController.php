<?php

namespace App\Http\Controllers\Freelancer;

use Exception;
use Illuminate\Http\Request;
use App\Services\CountryService;
use App\Services\LanguageService;
use App\Services\FreelancerService;
use App\Services\ProfessionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FreelancerRequest;
use App\Models\Freelancer;
use App\Services\CategoryService;

class FreelancerController extends Controller
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

    public function index(Request $request)
    {
        $params = request()->all();
        $freelancers = $this->freelancerService->index($params);
        $professions = $this->professionService->getAllActive();
        $countries = $this->countryService->getAllActive();
        return view('pages.freelancers.index', compact('freelancers', 'professions', 'countries'));
    }
    public function create()
    {
        $professions = $this->professionService->getAllActive();
        $countries = $this->countryService->getAllActive();
        $languages = $this->languageService->getAllActive();
        $categories = $this->categoryService->getAllActive();
        return view('pages.freelancers.create', compact('professions', 'countries', 'languages', 'categories'));
    }
    public function store(FreelancerRequest $request)
    {
        $this->freelancerService->store($request);
        return redirect()->route('freelancers.index')->with('success', __('freelancer_created_successfully'));
    }
    public function destroy($id)
    {
        try {
            $this->freelancerService->delete($id);
            return redirect()->back()->with('success', __('freelancer_deleted_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function updateActivation(Request $request)
    {
        try {
            $freelancer = $this->freelancerService->updateActivation($request->id);
            return $this->successResponse('success');
        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());
        }
    }
    public function updateVerification(Request $request)
    {
        try {
            $freelancer = $this->freelancerService->updateVerification($request->id);
            return $this->successResponse('success');
        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());
        }
    }
    public function show($id)
    {
        $freelancer = $this->freelancerService->find($id);
        return view('pages.freelancers.show', compact('freelancer'));
    }

    public function archived()
    {
        $freelancers = $this->freelancerService->getArchived();
        return view('pages.freelancers.archived', compact('freelancers'));
    }

    public function restore($id)
    {
        try {
            $this->freelancerService->restore($id);
            return redirect()->back()->with('success', __('freelancer_restored_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
}
