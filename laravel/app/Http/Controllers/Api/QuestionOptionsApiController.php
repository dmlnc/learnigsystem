<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionOptionRequest;
use App\Http\Requests\UpdateQuestionOptionRequest;
use App\Http\Resources\QuestionOptionResource;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QuestionOptionsApiController extends Controller
{
    public function index()
    {

        return new QuestionOptionResource(QuestionOption::with(['question'])->get());
    }

    public function store(StoreQuestionOptionRequest $request)
    {
        $questionOption = QuestionOption::create($request->validated());

        return (new QuestionOptionResource($questionOption))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function create()
    {

        return response([
            'meta' => [
                'question' => Question::get(['id', 'question_text']),
            ],
        ]);
    }

    public function show(QuestionOption $questionOption)
    {

        return new QuestionOptionResource($questionOption->load(['question']));
    }

    public function update(UpdateQuestionOptionRequest $request, QuestionOption $questionOption)
    {
        $questionOption->update($request->validated());

        return (new QuestionOptionResource($questionOption))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function edit(QuestionOption $questionOption)
    {

        return response([
            'data' => new QuestionOptionResource($questionOption->load(['question'])),
            'meta' => [
                'question' => Question::get(['id', 'question_text']),
            ],
        ]);
    }

    public function destroy(QuestionOption $questionOption)
    {

        $questionOption->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
