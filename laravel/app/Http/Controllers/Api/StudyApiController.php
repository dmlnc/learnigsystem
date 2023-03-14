<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
// use App\Http\Requests\StoreCourseRequest;
// use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LessonResource;
use App\Http\Resources\LessonStudyResource;

use App\Http\Resources\TestResource;
use App\Models\Academy;
use App\Models\Faculty;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Test;
use App\Models\TestAnswer;
use App\Models\TestResult;
// use App\Models\User;
use Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
// use Spatie\MediaLibrary\MediaCollections\Models\Media;

class StudyApiController extends Controller
{


    // Академии->факультеты->курсы

    // Чаще всего, но не всегда
    // 1 академия
    // 1 Факульет

 
// Рестораны - академии +
// Сотрудники +
// Должности - факультеты +
// Меню - 
// Учебные курсы +
// Аттестация - тесты??
// Общение -
// Новости компании -
// Библиотека - ???
// Управлявющий - ???
// Отчеты -
// График работы ??? 
// Настройки - профиль компании +

    public function faculties(Request $request)
    {

        $user = auth()->user();

        $academies = Academy::whereHas('faculties', function ($query) use ($user) {
            $query->whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            });
        })->with(['faculties' => function ($query) use ($user) {
            $query->whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            });
        }])->select(['id', 'name'])->get();


        // $academies = $user->faculties->academies

        return response()->json(['data'=>$academies]);

        // return new CourseResource($user->faculties()->get(['id', 'name']));
    }

    public function courses(Request $request, Faculty $faculty)
    {
        // $user = auth()->user();
        return [
            'data' =>  CourseResource::collection($faculty->courses()->where('is_published', 1)->get()),
            'meta' => [
                'name' => $faculty->name,
            ]
        ];
    }



    public function lessons(Request $request, Faculty $faculty, Course $course)
    {
        abort_if(($course->is_published == 0), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = auth()->user();

        // $hasAccess = $user->courses()->where('id', $course->id)->exists();

        // abort_if(!$hasAccess, Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'data' => LessonResource::collection(
                $course->lessons()
                    ->where('is_published', 1)
                    ->withCount(['tests' => function ($query) {
                        $query->where('is_published', 1);
                    }])
                    ->with([
                        'tests' => function($q) use($user){
                            $q->where("is_published",1);
                            $q->withCount([
                                'test_results as test_finished'=> function ($query) use ($user) {
                                    $query->where('student_id', $user->id);
                                }
                            ]);
                        }
                    ])
                    ->get()
            ),
            'meta' => [
                'course'=>[
                    'id' => $course->id,
                    'name'=>$course->title,
                ],
                'faculty'=>[
                    'id' => $faculty->id,
                    'name'=>$faculty->name,
                ],
            ],
        ]);
    }
    
    // public function lessons(Request $request, Course $course)
    // {
    //     abort_if((Gate::denies('study_access') || ($course->is_published == 0)), Response::HTTP_FORBIDDEN, '403 Forbidden');
    //     $user = auth()->user();
    //     $hasAccess = $user->courses()->where('id', $course->id)->exists();
    //     abort_if(!$hasAccess, Response::HTTP_FORBIDDEN, '403 Forbidden');

    //     return new LessonResource($course->lessons()->where('is_published', 1)->get());
    // }

    public function lesson(Request $request, Faculty $faculty, Course $course, Lesson $lesson)
    {
        abort_if(($course->is_published == 0) || ($lesson->is_published == 0), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = auth()->user();
        // $hasAccess = $user->courses()->where('id', $course->id)->exists();
        // abort_if(!$hasAccess, Response::HTTP_FORBIDDEN, '403 Forbidden');

        abort_if($lesson->course_id != $course->id, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $previousLesson = Lesson::where('course_id', $course->id)->where('id', '<', $lesson->id)->select('id', 'title')->orderBy('id', 'desc')->first();
        $nextLesson = Lesson::where('course_id', $course->id)->where('id', '>', $lesson->id)->select('id', 'title')->orderBy('id', 'asc')->first();


        $lessonsMeta = [
            'nextLesson' => $nextLesson,
            'previousLesson' => $previousLesson,
        ];



        return response([
            'data'=> new LessonStudyResource($lesson
                ->load([
                    "tests" => function($q) use($user){
                        $q->where("is_published",1);
                        $q->with(['test_results' => function ($query) use ($user) {
                                $query->where('student_id', $user->id);
                                $query->take(1);
                                // $query->select(['test_results.id','test_results.score']);
                            }
                        ]);
                        $q->withCount([
                            'test_results as test_finished'=> function ($query) use ($user) {
                                $query->where('student_id', $user->id);
                            }
                        ]);
                    }
                ])
            ),
            'meta' => [
                'course'=>[
                    'id' => $course->id,
                    'name'=>$course->title,
                ],
                'faculty'=>[
                    'id' => $faculty->id,
                    'name'=>$faculty->name,
                ],
                'lessons' => $lessonsMeta,
            ],
        ]);
    }

    public function test(Request $request, Course $course, Lesson $lesson, Test $test)
    {
        // abort_if(($course->is_published == 0) || ($lesson->is_published == 0) || ($test->is_published == 0)), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = auth()->user();
        $hasAccess = $user->courses()->where('id', $course->id)->exists();
        abort_if(!$hasAccess, Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($lesson->course_id != $course->id, Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($test->lesson_id != $lesson->id, Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($test->test_results()->where('student_id', $user->id)->exists(), Response::HTTP_FORBIDDEN, '403 Forbidden');


        return new TestResource($test->load('questions', 'questions.options'));
    }

    public function answer(Request $request, Course $course, Lesson $lesson, Test $test)
    {
        // abort_if(($course->is_published == 0) || ($lesson->is_published == 0) || ($test->is_published == 0)), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = auth()->user();
        $hasAccess = $user->courses()->where('id', $course->id)->exists();
        abort_if(!$hasAccess, Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($lesson->course_id != $course->id, Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($test->lesson_id != $lesson->id, Response::HTTP_FORBIDDEN, '403 Forbidden');

        abort_if($test->test_results()->where('student_id', $user->id)->exists(), Response::HTTP_FORBIDDEN, '403 Forbidden');



        $questions = Question::where('test_id', $test->id)->get()->load('options');
        // $options = QuestionOption::whereIn('question_id', $questions->pluck('id'))->get();

        $request_questions = $request->input('answers');
        
    

        // Create test result
        $totalScore = 0;
        $maxScore = 0;
        $testResult = TestResult::create([
            'score' => $totalScore,
            'test_id' => $test->id,
            'student_id' => $user->id,
        ]);




        foreach ($request_questions as $q){
            $db_q = $questions->where('id', $q['id'])->first();

            // Log::info($db_q);

            $answers_id = [];
            $total_correct = 1;
            foreach ($q['answers'] as $id => $answer){
                if($answer == true){
                    $correct = 0;
                    if(isset($db_q->options)){
                        $db_o = $db_q->options->where('id', $id)->first();
                        $correct = $db_o->is_correct;
                        // Log::info($total_correct);

                        $total_correct = $total_correct && $correct;
                        // Log::info($total_correct);
                    }

                    

                    TestAnswer::create([
                        'is_correct'=> $correct,
                        'test_result_id' => $testResult->id,
                        'question_id' => $q['id'],
                        'option_id' => $id,
                    ]);
                }
            }
            // Log::info($total_correct);

            if($total_correct == 1){
                $totalScore += $db_q->points;
            }
            $maxScore+= $db_q->points;
        }

        $testResult->update(['score'=>$totalScore]);


        return ['score' => $totalScore, 'max_score'=> $maxScore, 'test_result_id' => $testResult->id];
        // return new TestResource($test->load('questions', 'questions.options'));
    }

    public function result(Request $request, Course $course, Lesson $lesson, Test $test)
    {
        // abort_if(($course->is_published == 0) || ($lesson->is_published == 0) || ($test->is_published == 0)), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = auth()->user();
        $hasAccess = $user->courses()->where('id', $course->id)->exists();
        abort_if(!$hasAccess, Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($lesson->course_id != $course->id, Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($test->lesson_id != $lesson->id, Response::HTTP_FORBIDDEN, '403 Forbidden');




        $maxScore = Question::where('test_id', $test->id)->sum('points');

        $testResult = TestResult::where('test_id', $test->id)->where('student_id', $user->id)->first();


        // Create test result
     


        return ['data'=>$test, 'score' => $testResult->score, 'max_score'=> $maxScore, 'test_result_id' => $testResult->id];
        // return new TestResource($test->load('questions', 'questions.options'));
    }


    public function checkResult(Request $request, Course $course, Lesson $lesson, Test $test)
    {
        // abort_if(($course->is_published == 0) || ($lesson->is_published == 0) || ($test->is_published == 0)), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = auth()->user();
        $hasAccess = $user->courses()->where('id', $course->id)->exists();
        abort_if(!$hasAccess, Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($lesson->course_id != $course->id, Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($test->lesson_id != $lesson->id, Response::HTTP_FORBIDDEN, '403 Forbidden');

        return ['result_exist' =>$test->test_results()->where('student_id', $user->id)->exists()];
        // return new TestResource($test->load('questions', 'questions.options'));
    }

    


    

   
}
