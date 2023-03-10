<?php

use App\Http\Controllers\Api\AcademyApiController;
use App\Http\Controllers\Api\CoursesApiController;
use App\Http\Controllers\Api\FacultyApiController;
use App\Http\Controllers\Api\UsersApiController;
use App\Http\Controllers\Api\LessonsApiController;
use App\Http\Controllers\Api\TestsApiController;
use App\Http\Controllers\Api\QuestionsApiController;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyApiController;
use App\Http\Controllers\Api\MediaController;
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
});

Route::post('v1/login', [AuthController::class, 'login']);
