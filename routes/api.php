<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\project\ProjectController;
use App\Http\Controllers\Api\project\ProjectUserController;
use Monolog\Handler\RotatingFileHandler;

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




Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('lastActivity');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});



Route::group(['middleware' => ['auth:api']], function () {

    Route::apiResource('projects', ProjectController::class)->middleware('admin');
    Route::get('projects', [ProjectController::class, 'index']);

    Route::post('pivote', [ProjectUserController::class, 'store'])->middleware('admin');
    Route::get('pivote', [ProjectUserController::class, 'index'])->middleware('admin');
    Route::put('users/{user_id}/projects/{project_id}', [ProjectUserController::class, 'update'])->middleware('admin');
    Route::get('users/{user_id}/projects/{project_id}', [ProjectUserController::class, 'show'])->middleware('admin');
    Route::delete('/users/{user_id}/projects/{project_id}', [ProjectUserController::class, 'destroy'])->middleware('admin');

    Route::apiResource('tasks', TaskController::class)->middleware('manager');
    Route::get('tasks', [TaskController::class, 'index']);
    Route::get('tasks/{task}', [TaskController::class, 'show']);
    Route::put('tasks/{task}/assigne', [TaskController::class, 'update_assigned_to']);

    Route::get('my_tasks', [TaskController::class, 'get_my_task']);

    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus']);
    Route::patch('tasks/{task}/note', [TaskController::class, 'makeNote']);
});
