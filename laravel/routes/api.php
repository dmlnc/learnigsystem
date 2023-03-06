<?php

use App\Http\Controllers\Api\AcademyApiController;
use App\Http\Controllers\Api\CoursesApiController;
use App\Http\Controllers\Api\FacultyApiController;
use App\Http\Controllers\Api\UsersApiController;


use App\Http\Controllers\Api\AuthController;
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


    Route::apiResource('academies', AcademyApiController::class);
    Route::apiResource('academies/{academy}/faculties', FacultyApiController::class);
    Route::resource('courses', CoursesApiController::class);
    Route::post('/courses/media', [CoursesApiController::class, 'storeMedia']);
    Route::delete('/media/{media_id}', [MediaController::class, 'deleteMedia']);



    Route::post('/logout', 'AuthController@logout');
});

Route::post('v1/login', [AuthController::class, 'login']);
