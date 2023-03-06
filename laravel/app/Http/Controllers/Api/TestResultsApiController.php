<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestResultRequest;
use App\Http\Requests\UpdateTestResultRequest;
use App\Http\Resources\TestResultResource;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TestResultsApiController extends Controller
{
    public function index()
    {

        return new TestResultResource(TestResult::with(['test', 'student'])->get());
    }

    public function store(StoreTestResultRequest $request)
    {
        $testResult = TestResult::create($request->validated());

        return (new TestResultResource($testResult))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function create()
    {

        return response([
            'meta' => [
                'test'    => Test::get(['id', 'title']),
                'student' => User::get(['id', 'name']),
            ],
        ]);
    }

    public function show(TestResult $testResult)
    {

        return new TestResultResource($testResult->load(['test', 'student']));
    }

    public function update(UpdateTestResultRequest $request, TestResult $testResult)
    {
        $testResult->update($request->validated());

        return (new TestResultResource($testResult))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function edit(TestResult $testResult)
    {

        return response([
            'data' => new TestResultResource($testResult->load(['test', 'student'])),
            'meta' => [
                'test'    => Test::get(['id', 'title']),
                'student' => User::get(['id', 'name']),
            ],
        ]);
    }

    public function destroy(TestResult $testResult)
    {

        $testResult->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
