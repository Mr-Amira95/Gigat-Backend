<?php

namespace App\Repositories\Eloquents;

use Exception;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Interfaces\AdminRepositoryInterface;

class AdminRepository implements AdminRepositoryInterface
{
    protected $model;

    public function __construct(Admin $admin)
    {
        $this->model = $admin;
    }

    public function index()
    {
        return $this->model->with('roles')->orderBy('id', 'desc')->get();
    }

    public function create(array $data)
    {
        $admin = $this->model->create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        
        if (!empty($data['role'])) {
            $admin->assignRole($data['role']);
        }

        return $admin;
    }

    public function update($id, array $data)
    {
        $admin = $this->find($id);

        $updateData = [
            'username' => $data['username'],
            'email' => $data['email'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $admin->update($updateData);

        // Update role if provided
        if (!empty($data['role'])) {
            $admin->syncRoles([$data['role']]);
        }

        return $admin;
    }

    public function delete($id)
    {
        $admin = $this->find($id);
            if ($admin->hasRole('super_admin')) {
                throw new Exception(__('cannot_delete_super_admin'));
            }

            $activeAdminsCount = $this->model->where('is_active', true)->count();
            if ($activeAdminsCount <= 1) {
                throw new Exception(__('cannot_delete_last_admin'));
            }
        return $admin->delete();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function updateActivation($id)
    {
        $admin = $this->find($id);

        if ($admin->is_active) {
            if ($admin->hasRole('super_admin')) {
                throw new Exception(__('cannot_deactivate_super_admin'));
            }

            $activeAdminsCount = $this->model->where('is_active', true)->count();
            if ($activeAdminsCount <= 1) {
                throw new Exception(__('cannot_deactivate_last_admin'));
            }
        }

        $admin->is_active = !$admin->is_active;
        $admin->save();

        return $admin;
    }
}
