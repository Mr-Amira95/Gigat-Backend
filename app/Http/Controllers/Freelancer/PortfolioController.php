<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Freelancer\PortfolioRequest;
use App\Http\Requests\Freelancer\UpdatePortfolioRequest;
use App\Models\Service;
use App\Services\FreelancerService;
use App\Services\PortfolioService;
use App\Services\ServiceService;
use Exception;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    protected $portfolioService;
    protected $serviceService;
    protected $freelancerService;

    public function __construct(PortfolioService $portfolioService, ServiceService $serviceService, FreelancerService $freelancerService)
    {
        $this->portfolioService = $portfolioService;
        $this->serviceService = $serviceService;
        $this->freelancerService = $freelancerService;
    }
    public function index()
    {
        $portfolios = $this->portfolioService->getUserPortfolio();
        return view('pages-freelancer.portfolios.index', compact('portfolios'));
    }
    public function create()
    {
        $services = Service::where('user_id', auth()->id())->get();
        return view('pages-freelancer.portfolios.create', compact('services'));
    }
    public function store(PortfolioRequest $request)
    {
        try {
            $this->portfolioService->create($request->validated());
            return redirect()->route('freelancer.portfolios.index')
                ->with('success', __('portfolio_created_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
    public function edit($id)
    {
        $portfolio = $this->portfolioService->getPortfolioDetails($id);
        $services = Service::where('user_id', auth()->id())->get();
        return view('pages-freelancer.portfolios.edit', compact('portfolio', 'services'));
    }
    public function update(UpdatePortfolioRequest $request, $id)
    {
        // dd($request->all());
        try {
            $this->portfolioService->update($request->validated(), $id);
            return redirect()->route('freelancer.portfolios.index')
                ->with('success', __('portfolio_updated_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
    public function show($id)
    {
        $portfolio = $this->portfolioService->getPortfolioDetails($id);
        return view('pages-freelancer.portfolios.show', compact('portfolio'));
    }
    public function destroy($id)
    {
        $this->portfolioService->delete($id);
        return redirect()->route('freelancer.portfolios.index')
            ->with('success', __('portfolio_deleted_successfully'));
    }
}
