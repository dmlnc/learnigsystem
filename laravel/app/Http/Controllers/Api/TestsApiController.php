<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestRequest;
use App\Http\Requests\UpdateTestRequest;
use App\Http\Resources\TestResource;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;

class TestsApiController extends Controller
{
    public function index(Request $request)
    {

        return new TestResource(Test::where('lesson_id', $request->input('lesson_id'))->with(['course', 'lesson'])->get());
    }

    public function store(StoreTestRequest $request)
    {
        $test = Test::create($request->validated());
        if(!empty($request->questions)){
            foreach ($request->questions as $question){
                $question['test_id'] = $test->id;
                $this->createQuestion($question);
//                $q = Question::create($question);
//                foreach ($question['options'] as $option){
//                    $option['question_id'] = $q->id;
//                    QuestionOption::create($option);
//                }
            }
        }
        return (new TestResource($test))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function createQuestion($question){
        $q = Question::create($question);
        if(!isset($question['options'])){
            return $q;
        }
        foreach ($question['options'] as $option){
            $option['question_id'] = $q->id;
            $this->createOption($option);
        }
        return $q;
    }

    public function createOption($option){
        return QuestionOption::create($option);
    }

    public function create()
    {

        return response([
            'meta' => [
                'course' => Course::get(['id', 'title']),
                'lesson' => Lesson::get(['id', 'title']),
            ],
        ]);
    }

    public function show(Test $test)
    {

        return new TestResource($test->load(['course', 'lesson']));
    }

    public function update(UpdateTestRequest $request, Test $test)
    {
        $test->update($request->validated());
        $questions = $test->questions()->pluck('id')->toArray();
        if(!empty($request->questions)){
            $questionsReq = $request->input('questions');
            $foundQuestions = [];
            foreach ($questionsReq as $question){

                if(isset($question['id'])){

                    if(in_array($question['id'],$questions)){
                        $quest = Question::find($question['id']);
                        $quest->update($question);
                        $foundQuestions[] = $question['id'];
                        $options = $quest->options()->pluck('id')->toArray();
                        $foundOptions = [];
                        if(isset($question['options'])) {
                            foreach ($question['options'] as $option) {
                                if (isset($option['id'])) {
                                    if (in_array($question['id'], $questions)) {
                                        QuestionOption::find($option['id'])->update($option);
                                        $foundOptions[] = $option['id'];
                                    }
                                } else {
                                    $option['question_id'] = $question['id'];
                                    $option = $this->createOption($option);
                                    $foundOptions[] = $option->id;
                                }
                            }
                        }

                        $d = QuestionOption::where('question_id', $question['id'])->whereNotIn('id', $foundOptions)->pluck('id');
//                        Log::info($d);
                        QuestionOption::destroy($d);
                }
                }
                else{
                    $question['test_id'] = $test->id;
                    $q = $this->createQuestion($question);
                    $foundQuestions[] = $q['id'];
                }
            }

            $allqids= Question::where('test_id', $test->id)->whereNotIn('id', $foundQuestions)->pluck('id');
            $d = QuestionOption::whereIn('question_id', $allqids)->pluck('id');
            QuestionOption::destroy($d);
            Question::destroy($allqids);
        }

        return (new TestResource($test))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function edit(Test $test)
    {

        return response([
            'data' => new TestResource($test->load(['course', 'lesson','questions', 'questions.options'])),
            'meta' => [
                'course' => Course::get(['id', 'title']),
                'lesson' => Lesson::get(['id', 'title']),
            ],
        ]);
    }

    public function destroy(Test $test)
    {

        $test->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
