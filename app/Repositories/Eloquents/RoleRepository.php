<?php

namespace App\Repositories\Eloquents;

use Exception;
use Spatie\Permission\Models\Role;
use App\Repositories\Interfaces\RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface
{
    protected $model;

    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    public function index()
    {
        return $this->model->with('permissions')->orderBy('id', 'desc')->get();
    }

    public function create(array $data)
    {
        $role = $this->model->create([
            'name' => $data['name'],
            'guard_name' => 'admin'
        ]);

        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }

    public function update($id, array $data)
    {
        $role = $this->find($id);

        $role->update([
            'name' => $data['name']
        ]);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }

    public function delete($id)
    {
        $role = $this->find($id);
        if ($role->name === 'super_admin') {
            throw new Exception(__('cannot_delete_super_admin_role'));
        }
        return $role->delete();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }
} 