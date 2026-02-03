<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Services\RoleService;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\Admin\RoleRequest;
use Exception;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        $roles = $this->roleService->index();
        return view('pages.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('pages.roles.create', compact('permissions'));
    }

    public function store(RoleRequest $request)
    {
        try {
            $this->roleService->create($request->validated());
            return redirect()->route('roles.index')
                ->with('success', __('role_created_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $role = $this->roleService->find($id);
        $permissions = Permission::all();
        return view('pages.roles.edit', compact('role', 'permissions'));
    }

    public function update(RoleRequest $request, $id)
    {
        try {
            $this->roleService->update($id, $request->validated());
            return redirect()->route('roles.index')
                ->with('success', __('role_updated_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $this->roleService->delete($id);
            return redirect()->back()
                ->with('success', __('role_deleted_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
