<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Http\Resources\Admin\QuestionResource;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Database\Eloquent\Model;
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
        (new MediaController)->syncMedia($request->input('images', []), $question->id);

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

        (new MediaController)->syncMedia($request->input('images', []), $question->id);

//        $question->updateMedia($request->input('question_image', []), 'question_question_image');

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
        $model = new Question();
        return  (new MediaController)->storeMedia($request, $model, 'question_thumbnail');

        return response()->json($media, Response::HTTP_CREATED);
    }
}
