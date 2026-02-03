<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\AdminService;
use App\DataTables\AdminDataTable;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\Admin\AdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use Exception;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function index()
    {
        $admins = $this->adminService->index();
        return view('pages.admins.index', compact('admins'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('pages.admins.create', compact('roles'));
    }

    public function store(AdminRequest $request)
    {
        try {
            $this->adminService->create($request->validated());
            return redirect()->route('admins.index')
                ->with('success', __('admin_created_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $admin = $this->adminService->find($id);
        $roles = Role::all();
        return view('pages.admins.edit', compact('admin', 'roles'));
    }

    public function update(UpdateAdminRequest $request, $id)
    {
        try {
            $this->adminService->update($id, $request->validated());
            return redirect()->route('admins.index')
                ->with('success', __('admin_updated_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $this->adminService->delete($id);
            return redirect()->back()->with('success', __('admin_deleted_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function updateActivation(Request $request)
    {
        try {
            $admin = $this->adminService->updateActivation($request->id);
            return $this->successResponse('success');
        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());

        }
    }
}
