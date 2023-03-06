<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\ThumbnailResource;
use App\Models\Academy;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CoursesApiController extends Controller
{
    public function index()
    {

        $user = auth()->user();


        return CourseResource::collection(Course::where('company_id', $user->company_id)->get());
    }

    public function store(StoreCourseRequest $request)
    {
        $data = $request->validated();

        $authUser = auth()->user();
        $data['company_id'] = $authUser->company_id;

        $course = Course::create($data);

        $course->faculties()->sync($request->input('faculties', []));

        (new MediaController)->syncMedia($request->input('images', []), $course->id);

        return (new CourseResource($course))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function create()
    {

        $user = auth()->user();

        return response([
            'meta' => [
                'academies' => Academy::where('company_id', $user->company_id)->with(['faculties'])->get(['id', 'name'])
            ],
        ]);
    }

    public function show(Course $course)
    {

        return new CourseResource($course->load(['faculties']));
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $course->update($request->validated());
        $course->faculties()->sync($request->input('faculties', []));

        (new MediaController)->syncMedia($request->input('images', []), $course->id);

        return (new CourseResource($course))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function edit(Course $course)
    {

        // return response([
        //     'data' => new CourseResource($course->load(['teacher', 'students'])),
        //     'meta' => [
        //         'teacher'  => User::get(['id', 'name']),
        //         'students' => User::get(['id', 'name']),
        //     ],
        // ]);
    }

    public function destroy(Course $course)
    {

        $course->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeMedia(Request $request)
    {
        $model = new Course();
        return  (new MediaController)->storeMedia($request, $model, 'course_thumbnail');

    }

    
}
