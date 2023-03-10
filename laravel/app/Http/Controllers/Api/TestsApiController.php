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

        $questionsIds = $test->questions()->pluck('id')->toArray();
        $optionsIds = $test->questions->options()->pluck('id')->toArray();


        (new MediaController)->syncMedia($request->input('images', []), $test->id);

        if(!empty($request->questions)){
            $questionsReq = $request->input('questions', []);
            $foundQuestionsIds = [];
            $foundOptionsIds = [];

            foreach ($questionsReq as $question){

                if(!isset($question['id'])){
                    $question['id'] = 'NEW';
                }

                if(in_array($question['id'],$questionsIds)){
                    $dbQuestion = Question::find($question['id']);
                    $dbQuestion->update($question);
                }
                else{
                    $question['test_id'] = $test->id;
                    $dbQuestion = $this->createQuestion($question);
                }

                $questionImages = [];
                if(isset($question['images']) && $question['images']!=null){
                    $questionImages = $question['images'];
                }
                
                (new MediaController)->syncMedia($questionImages, $dbQuestion->id);


                $foundQuestionsIds[] = $dbQuestion->id;

                if(isset($question['options'])) {
                    foreach ($question['options'] as $option) {

                        if(!isset($option['id'])){
                            $option['id'] = 'NEW';
                        }

                        if(in_array($option['id'],$optionsIds)){
                            $dbOption = QuestionOption::find($option['id']);
                            $dbOption->update($option);
                        }
                        else{
                            $option['question_id'] = $dbQuestion->id;
                            $dbOption = $this->createOption($option);
                        }

                        $foundOptionsIds[] = $dbOption->id;
                    }
                }
            }

            QuestionOption::whereIn('question_id', $questionsIds)->whereNotIn('id', $foundOptionsIds)->delete();
            Question::where('test_id', $test->id)->whereNotIn('id', $foundQuestionsIds)->delete();

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
