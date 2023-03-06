<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PermissionsApiController extends Controller
{
    public function index()
    {

        return new PermissionResource(Permission::get());
    }

    public function store(StorePermissionRequest $request)
    {
        $permission = Permission::create($request->validated());

        return (new PermissionResource($permission))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function create()
    {

        return response([
            'meta' => [],
        ]);
    }

    public function show(Permission $permission)
    {

        return new PermissionResource($permission);
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $permission->update($request->validated());

        return (new PermissionResource($permission))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function edit(Permission $permission)
    {

        return response([
            'data' => new PermissionResource($permission),
            'meta' => [],
        ]);
    }

    public function destroy(Permission $permission)
    {

        $permission->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
