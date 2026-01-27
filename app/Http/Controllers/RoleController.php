<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index()
    {
        return view('roles.index');
    }

    public function data()
    {
        $roles = Role::with('permissions')->get();
        return DataTables::of($roles)
            ->addColumn('permissions', function ($role) {
                return $role->permissions->pluck('name')->map(function ($name) {
                    return '<span class="permission-badge">' . ucwords($name) . '</span>';
                })->implode('');
            })
            ->addColumn('actions', function ($role) {
                return '<a href="' . route('roles.edit', $role->id) . '" class="btn btn-sm btn-premium-secondary" style="background:#f1f5f9; color:#667eea; border:none; border-radius:8px; font-weight:600; padding:6px 14px;">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </a>';
            })
            ->rawColumns(['permissions', 'actions'])
            ->make(true);
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array'
        ]);

        $role = Role::create(['name' => $request->name]);
        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'success' => true,
            'msg' => 'Role created successfully',
            'location' => route('roles.index')
        ]);
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'nullable|array'
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return response()->json([
            'success' => true,
            'msg' => 'Role updated successfully',
            'location' => route('roles.index')
        ]);
    }

    public function destroy($id)
    {
        Role::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
