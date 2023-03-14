<?php

use App\Http\Controllers\Api\AcademyApiController;
use App\Http\Controllers\Api\CoursesApiController;
use App\Http\Controllers\Api\FacultyApiController;
use App\Http\Controllers\Api\UsersApiController;
use App\Http\Controllers\Api\LessonsApiController;
use App\Http\Controllers\Api\TestsApiController;
use App\Http\Controllers\Api\QuestionsApiController;
use App\Http\Controllers\Api\StudyApiController;




use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyApiController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\PostApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::group(['prefix' => 'v1', 'as' => 'api.', 'middleware' => ['auth:sanctum']], function () {
    // Route::get('/user', 'UserController@getUser');
    Route::resource('users', UsersApiController::class);

    Route::apiResource('companies', CompanyApiController::class)->only(['show', 'update']);
    Route::post('/companies/media', [CompanyApiController::class, 'storeMedia']);



    Route::apiResource('academies', AcademyApiController::class);
    Route::apiResource('academies/{academy}/faculties', FacultyApiController::class);

    Route::resource('courses', CoursesApiController::class);
    Route::post('/courses/media', [CoursesApiController::class, 'storeMedia']);

    Route::apiResource('courses/{course}/lessons', LessonsApiController::class);
    Route::post('/lessons/media', [LessonsApiController::class, 'storeMedia']);

    Route::delete('/media/{media_id}', [MediaController::class, 'deleteMedia']);

    Route::apiResource('lessons/{lesson}/tests', TestsApiController::class);

//    Route::apiResource('tests', TestsApiController::class);
    Route::post('/tests/media', [TestsApiController::class, 'storeMedia']);
    Route::post('/questions/media', [QuestionsApiController::class, 'storeMedia']);

    Route::post('/logout', 'AuthController@logout');
    Route::resource('posts', PostApiController::class);
    Route::post('/posts/media', [PostApiController::class, 'storeMedia']);

});

Route::post('v1/login', [AuthController::class, 'login']);

Route::post('v1/study/login', [AuthController::class, 'login']);






Route::group(['prefix' => 'v1/study', 'as' => 'api.', 'middleware' => ['auth:sanctum']], function () {
    Route::get('faculties', [StudyApiController::class, 'faculties']);

    Route::get('faculties/{faculty}/courses', [StudyApiController::class, 'courses']);

    Route::get('faculties/{faculty}/courses/{course}/lessons', [StudyApiController::class, 'lessons']);
    Route::get('faculties/{faculty}/courses/{course}/lessons/{lesson}', [StudyApiController::class, 'lesson']);



    
});

