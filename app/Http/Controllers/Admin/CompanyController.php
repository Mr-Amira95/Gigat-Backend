<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompanyRequest;
use App\Http\Requests\Admin\UpdateCompanyRequest;
use App\Services\CompanyService;
use App\Services\FreelancerService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    protected $companyService;
    protected $freelancerService;


    public function __construct(CompanyService $companyService, FreelancerService $freelancerService)
    {
        $this->companyService = $companyService;
        $this->freelancerService = $freelancerService;
    }

    public function index()
    {
        $companies = $this->companyService->index();

        return view('pages.companies.index', compact('companies'));
    }

    public function create()
    {
        $freelancers = $this->freelancerService->index([]);
        return view('pages.companies.create', compact('freelancers'));
    }

    public function store(CompanyRequest $request)
    {
        try {
            $data = $request->validated();

            $data['freelancer_id'] = $request->user_id;

            $company = $this->companyService->store($data);

            return redirect()
                ->route('companies.index')
                ->with('success', __('company_created_successfully'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }


    public function show($id)
    {
        try {
            $company = $this->companyService->find($id);


            return view('pages.companies.show', compact('company'));
        } catch (\Exception $e) {
            return redirect()->route('companies.index')
                ->with('error', $e->getMessage());
        }
    }


    public function edit($id)
    {
        $company = $this->companyService->find($id);
        $freelancers = $this->freelancerService->index([]);
        return view('pages.companies.edit', compact('company', 'freelancers'));
    }

    public function update(UpdateCompanyRequest $request, $id)
    {

        try {
            $data = $request->validated();
            $data['freelancer_id'] = $request->user_id;

            $company = $this->companyService->update($id, $data);

            return redirect()
                ->route('companies.index')
                ->with('success', __('company_updated_successfully'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
