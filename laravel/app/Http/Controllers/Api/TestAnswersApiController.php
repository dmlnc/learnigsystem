<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestAnswerRequest;
use App\Http\Requests\UpdateTestAnswerRequest;
use App\Http\Resources\TestAnswerResource;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\TestAnswer;
use App\Models\TestResult;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TestAnswersApiController extends Controller
{
    public function index(Request $request)
    {

        return new TestAnswerResource(TestAnswer::where('test_result_id', $request->input('test_result_id'))->with(['testResult', 'question', 'option'])->get());
    }

    public function store(StoreTestAnswerRequest $request)
    {
        $testAnswer = TestAnswer::create($request->validated());

        return (new TestAnswerResource($testAnswer))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function create()
    {

        return response([
            'meta' => [
                'test_result' => TestResult::get(['id', 'score']),
                'question'    => Question::get(['id', 'question_text']),
                'option'      => QuestionOption::get(['id', 'option_text']),
            ],
        ]);
    }

    public function show(TestAnswer $testAnswer)
    {

        return new TestAnswerResource($testAnswer->load(['testResult', 'question', 'option']));
    }

    public function update(UpdateTestAnswerRequest $request, TestAnswer $testAnswer)
    {
        $testAnswer->update($request->validated());

        return (new TestAnswerResource($testAnswer))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function edit(TestAnswer $testAnswer)
    {

        return response([
            'data' => new TestAnswerResource($testAnswer->load(['testResult', 'question', 'option'])),
            'meta' => [
                'test_result' => TestResult::get(['id', 'score']),
                'question'    => Question::get(['id', 'question_text']),
                'option'      => QuestionOption::get(['id', 'option_text']),
            ],
        ]);
    }

    public function destroy(TestAnswer $testAnswer)
    {

        $testAnswer->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
