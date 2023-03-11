<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Http\Resources\LessonResource;
use App\Http\Resources\LessonFullResource;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class LessonsApiController extends Controller
{

    public function index(Request $request, Course $course)
    {
        return response([
            'data' => LessonResource::collection($course->lessons),
            'meta' => [
                'title'  => $course->title,
            ],
        ]);

    }

    public function store(StoreLessonRequest $request, Course $course)
    {

        $validatedData = $request->validated();

        $validatedData['course_id'] = $course->id;

        $lesson = Lesson::create($validatedData);

        (new MediaController)->syncMedia($request->input('images', []), $lesson->id);

        return (new LessonFullResource($lesson))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    // public function create()
    // {

    //     return response([
    //         'meta' => [
    //             'course' => Course::get(['id', 'title']),
    //         ],
    //     ]);
    // }

    public function show(Course $course, Lesson $lesson)
    {

        return new LessonFullResource($lesson);
    }


    public function update(UpdateLessonRequest $request, Course $course, Lesson $lesson)
    {

        $validated = $request->validated();

        $validated['long_text'] = (new MediaController)->syncImagesFromHtml($lesson, $validated['long_text']);

        $lesson->update($validated);

        (new MediaController)->syncMedia($request->input('images', []), $lesson->id);

        return (new LessonFullResource($lesson))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    

    // public function edit(Lesson $lesson)
    // {

    //     return response([
    //         'data' => new LessonResource($lesson->load(['course'])),
    //         'meta' => [
    //             'course' => Course::get(['id', 'title']),
    //         ],
    //     ]);
    // }

    public function destroy(Course $course, Lesson $lesson)
    {

        $lesson->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeMedia(Request $request)
    {
        $model = new Lesson();
        return  (new MediaController)->storeMedia($request, $model, 'lesson_thumbnail');
    }
}
