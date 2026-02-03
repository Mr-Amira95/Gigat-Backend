<?php

namespace App\Http\Controllers\Freelancer;

use Exception;
use Illuminate\Http\Request;
use App\Services\ClientService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ClientRequest;
use App\Services\ProfessionService;
use App\Services\CountryService;
use App\Services\LanguageService;
class ClientController extends Controller
{
    protected $clientService;
    protected $professionService;
    protected $countryService;
    protected $languageService;
    public function __construct(ClientService $clientService, ProfessionService $professionService, CountryService $countryService, LanguageService $languageService)
    {
        $this->clientService = $clientService;
        $this->professionService = $professionService;
        $this->countryService = $countryService;
        $this->languageService = $languageService;
    }

    public function index(Request $request)
    {
        $params = $request->all();

        $clients = $this->clientService->index($params);
        $professions = $this->professionService->getAllActive();
        $countries = $this->countryService->getAllActive();
        return view('pages.clients.index', compact('clients', 'professions', 'countries'));
    }
    public function create()
    {
        $professions = $this->professionService->getAllActive();
        $countries = $this->countryService->getAllActive();
        $languages = $this->languageService->getAllActive();
        return view('pages.clients.create', compact('professions', 'countries', 'languages'));
    }
    public function store(ClientRequest $request)
    {
        $this->clientService->store($request);
        return redirect()->route('clients.index')->with('success', __('client_created_successfully'));
    }
    public function show($id)
    {
        $client = $this->clientService->find($id);
        return view('pages.clients.show', compact('client'));
    }
    public function destroy($id)
    {
        try {
            $this->clientService->delete($id);
            return redirect()->back()->with('success', __('client_deleted_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function updateActivation(Request $request)
    {
        try {
            $client = $this->clientService->updateActivation($request->id);
            return $this->successResponse('success');
        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());
        }
    }

    public function archived()
    {
        $clients = $this->clientService->getArchived();
        return view('pages.clients.archived', compact('clients'));
    }

    public function restore($id)
    {
        try {
            $this->clientService->restore($id);
            return redirect()->back()->with('success', __('client_restored_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
}
