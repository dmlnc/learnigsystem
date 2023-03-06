<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Http\Resources\Admin\QuestionResource;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class QuestionsApiController extends Controller
{
    public function index()
    {

        return new QuestionResource(Question::with(['course'])->get());
    }

    public function store(StoreQuestionRequest $request)
    {
        $question = Question::create($request->validated());

        if ($media = $request->input('question_image', [])) {
            Media::whereIn('id', data_get($media, '*.id'))
                ->where('model_id', 0)
                ->update(['model_id' => $question->id]);
        }

        return (new QuestionResource($question))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function create()
    {

        return response([
            'meta' => [
                'course' => Test::get(['id', 'title']),
            ],
        ]);
    }

    public function show(Question $question)
    {

        return new QuestionResource($question->load(['course']));
    }

    public function update(UpdateQuestionRequest $request, Question $question)
    {
        $question->update($request->validated());

        $question->updateMedia($request->input('question_image', []), 'question_question_image');

        return (new QuestionResource($question))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function edit(Question $question)
    {

        return response([
            'data' => new QuestionResource($question->load(['course'])),
            'meta' => [
                'course' => Test::get(['id', 'title']),
            ],
        ]);
    }

    public function destroy(Question $question)
    {

        $question->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeMedia(Request $request)
    {

        if ($request->has('size')) {
            $this->validate($request, [
                'file' => 'max:' . $request->input('size') * 1024,
            ]);
        }

        $model         = new Question();
        $model->id     = $request->input('model_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('file')->toMediaCollection($request->input('collection_name'));

        return response()->json($media, Response::HTTP_CREATED);
    }
}
