<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Services\CountryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CountryRequest;

class CountryController extends Controller
{
    protected $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function index()
    {
        $countries = $this->countryService->index();
        return view('pages.countries.index', compact('countries'));
    }

    public function create()
    {
        return view('pages.countries.create');
    }

    public function store(CountryRequest $request)
    {
        try {
            $this->countryService->store($request->validated());
            return redirect()->route('countries.index')
                ->with('success', __('country_created_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $country = $this->countryService->find($id);
        return view('pages.countries.edit', compact('country'));
    }

    public function update(CountryRequest $request, $id)
    {
        try {
            $this->countryService->update($id, $request->validated());
            return redirect()->route('countries.index')
                ->with('success', __('country_updated_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $this->countryService->delete($id);
            return redirect()->back()
                ->with('success', __('country_deleted_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function updateActivation(Request $request)
    {
        try {
            $country = $this->countryService->updateActivation($request->id);
            return $this->successResponse('success');
        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());
        }
    }
} 