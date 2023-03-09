<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestRequest;
use App\Http\Requests\UpdateTestRequest;
use App\Http\Resources\TestFullResource;
use App\Http\Resources\TestResource;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Test;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;

class TestsApiController extends Controller
{
    public function index(Request $request,Lesson $lesson)
    {
        return response([
            'data' => TestResource::collection($lesson->tests),
            'meta' => [
                'title'  => $lesson->title,
            ],
        ]);
    }

    public function store(StoreTestRequest $request,Lesson $lesson)
    {
        $validatedData = $request->validated();
        $validatedData['lesson_id'] = $lesson->id;

        $test = Test::create($validatedData);
        (new MediaController)->syncMedia($request->input('images', []), $test->id);

        if(!empty($request->questions)){
            foreach ($request->questions as $question){
                $question['test_id'] = $test->id;
                $this->createQuestion($question);
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


    public function show(Lesson $lesson,Test $test)
    {
        return new TestFullResource($test);
    }

    public function update(UpdateTestRequest $request,Lesson $lesson, Test $test)
    {
        $test->update($request->validated());
        $questions = $test->questions()->pluck('id')->toArray();
        (new MediaController)->syncMedia($request->input('images', []), $test->id);

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

        return (new TestFullResource($test))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }


    public function destroy(Lesson $lesson,Test $test )
    {

        $test->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeMedia(Request $request)
    {
        $model = new Test();
        return  (new MediaController)->storeMedia($request, $model, 'test_thumbnail');
    }
}
