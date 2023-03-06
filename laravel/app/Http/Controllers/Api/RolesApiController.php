<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RolesApiController extends Controller
{
    public function index()
    {

        return new RoleResource(Role::with(['permissions'])->get());
    }

    public function store(StoreRoleRequest $request)
    {
        $role = Role::create($request->validated());
        $role->permissions()->sync($request->input('permissions.*.id', []));

        return (new RoleResource($role))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function create()
    {

        return response([
            'meta' => [
                'permissions' => Permission::get(['id', 'title']),
            ],
        ]);
    }

    public function show(Role $role)
    {

        return new RoleResource($role->load(['permissions']));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->validated());
        $role->permissions()->sync($request->input('permissions.*.id', []));

        return (new RoleResource($role))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function edit(Role $role)
    {

        return response([
            'data' => new RoleResource($role->load(['permissions'])),
            'meta' => [
                'permissions' => Permission::get(['id', 'title']),
            ],
        ]);
    }

    public function destroy(Role $role)
    {

        $role->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
