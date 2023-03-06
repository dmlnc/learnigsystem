<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Academy;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CoursesApiController extends Controller
{
    public function index()
    {

        return new CourseResource(Course::with(['faculties'])->get());
    }

    public function store(StoreCourseRequest $request)
    {
        $course = Course::create($request->validated());

        $course->faculties()->sync($request->input('faculties', []));

        if ($media = $request->input('thumbnail', [])) {
            Media::whereIn('id', data_get($media, '*.id'))
                ->where('model_id', 0)
                ->update(['model_id' => $course->id]);
        }

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

        return new CourseResource($course->load(['faculties', ]));
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $course->update($request->validated());
        $course->students()->sync($request->input('students.*.id', []));
        $course->updateMedia($request->input('thumbnail', []), 'course_thumbnail');

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

        if ($request->has('size')) {
            $this->validate($request, [
                'file' => 'max:' . $request->input('size') * 1024,
            ]);
        }

        $model         = new Course();
        $model->id     = $request->input('model_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('file')->toMediaCollection($request->input('collection_name', 'course_thumbnail'));

        return response()->json($media, Response::HTTP_CREATED);
    }
}
