<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Services\ProfessionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProfessionRequest;

class ProfessionController extends Controller
{
    protected $professionService;

    public function __construct(ProfessionService $professionService)
    {
        $this->professionService = $professionService;
    }

    public function index()
    {
        $professions = $this->professionService->index();
        return view('pages.professions.index', compact('professions'));
    }

    public function create()
    {
        return view('pages.professions.create');
    }

    public function store(ProfessionRequest $request)
    {
        try {
            $this->professionService->store($request->validated());
            return redirect()->route('professions.index')
                ->with('success', __('profession_created_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $profession = $this->professionService->find($id);
        return view('pages.professions.edit', compact('profession'));
    }

    public function update(ProfessionRequest $request, $id)
    {
        try {
            $this->professionService->update($id, $request->validated());
            return redirect()->route('professions.index')
                ->with('success', __('profession_updated_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $this->professionService->delete($id);
            return redirect()->back()
                ->with('success', __('profession_deleted_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function updateActivation(Request $request)
    {
        try {
            $profession = $this->professionService->updateActivation($request->id);
            return $this->successResponse('success');
        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());
        }
    }
} 