<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Http\Resources\LessonResource;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class LessonsApiController extends Controller
{
    public function index(Request $request)
    {

        return new LessonResource(Lesson::where('course_id', $request->input('course_id'))->with(['course'])->get());
    }

    public function store(StoreLessonRequest $request)
    {
        $lesson = Lesson::create($request->validated());

        if ($media = $request->input('thumbnail', [])) {
            Media::whereIn('id', data_get($media, '*.id'))
                ->where('model_id', 0)
                ->update(['model_id' => $lesson->id]);
        }

        // if ($media = $request->input('video', [])) {
        //     Media::whereIn('id', data_get($media, '*.id'))
        //         ->where('model_id', 0)
        //         ->update(['model_id' => $lesson->id]);
        // }

        return (new LessonResource($lesson))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function create()
    {

        return response([
            'meta' => [
                'course' => Course::get(['id', 'title']),
            ],
        ]);
    }

    public function show(Lesson $lesson)
    {

        return new LessonResource($lesson->load(['course']));
    }

    public function update(UpdateLessonRequest $request, Lesson $lesson)
    {
        $lesson->update($request->validated());

        $lesson->updateMedia($request->input('thumbnail', []), 'lesson_thumbnail');
        // $lesson->updateMedia($request->input('video', []), 'lesson_video');

        return (new LessonResource($lesson))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function edit(Lesson $lesson)
    {

        return response([
            'data' => new LessonResource($lesson->load(['course'])),
            'meta' => [
                'course' => Course::get(['id', 'title']),
            ],
        ]);
    }

    public function destroy(Lesson $lesson)
    {

        $lesson->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeMedia(Request $request)
    {

        if ($request->has('size')) {
            $this->validate($request, [
                'file' => 'max:' . $request->input('size') * 1024,
            ]);
        }

        $model         = new Lesson();
        $model->id     = $request->input('model_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('file')->toMediaCollection($request->input('collection_name'));

        return response()->json($media, Response::HTTP_CREATED);
    }
}
