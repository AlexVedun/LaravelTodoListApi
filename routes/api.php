<?php

use App\Http\Controllers\TaskController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// In real application this group of routes must be under authentication middleware control.
// For example, if we use sanctum:
// Route::middleware('auth:sanctum')->group(...
Route::group(['prefix' => 'tasks'], function () {
    Route::get('all', [TaskController::class, 'all']);
    Route::get('task', [TaskController::class, 'get']);
    Route::post('create', [TaskController::class, 'create']);
    Route::post('update', [TaskController::class, 'update']);
    Route::delete('task', [TaskController::class, 'delete']);
    Route::post('complete', [TaskController::class, 'complete']);
});
