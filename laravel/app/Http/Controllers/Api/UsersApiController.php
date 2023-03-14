<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Academy;
use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UsersApiController extends Controller
{
    public function index()
    {

        $user = auth()->user();

        return UserResource::collection(User::where('company_id', $user->company_id)->with(['roles', 'faculties'])->get());
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $authUser = auth()->user();
        $data['company_id'] = $authUser->company_id;
        $data['birthday'] = Carbon::parse($data['birthday']);
        $user = User::create($data);
        $user->roles()->sync($request->input('roles', []));
        $user->faculties()->sync($request->input('faculties', []));

        (new MediaController)->syncMedia($request->input('images', []), $user->id);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function create()
    {
        $user = auth()->user();

        return response([
            'meta' => [
                'roles' => Role::get(['id', 'title']),
                'academies' => Academy::where('company_id', $user->company_id)->with(['faculties'])->get(['id', 'name']),
            ],
        ]);
    }

    public function show(User $user)
    {

        return new UserResource($user->load(['roles', 'faculties']));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $data['birthday'] = Carbon::parse($data['birthday']);
        $user->update($data);

        $user->roles()->sync($request->input('roles', []));
        $user->faculties()->sync($request->input('faculties', []));
        (new MediaController)->syncMedia($request->input('images', []), $user->id);


        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    // public function edit(User $user)
    // {

    //     return response([
    //         'data' => new UserResource($user->load(['roles','courses'])),
    //         'meta' => [
    //             'roles' => Role::get(['id', 'title']),
    //             'courses' => Course::get(['id', 'title']),

    //         ],
    //     ]);
    // }
    public function storeMedia(Request $request)
    {
        $model = new User();
        return  (new MediaController)->storeMedia($request, $model, 'user_avatar');
    }

    public function destroy(User $user)
    {

        $user->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
