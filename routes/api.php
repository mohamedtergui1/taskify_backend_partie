<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\V1\TaskController;
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


    Route::post("/register",[AuthController::class,"register"]);
    Route::post("/login",[AuthController::class,"login"]);
    Route::post('/logout', [AuthController::class, 'logout']);


    // Route::get("/tasks",[TaskController::class,"index"]);
    // Route::get("/tasks/{id}",[TaskController::class,"show"]);
    // Route::post("/tasks",[TaskController::class,"store"]);
    // Route::put("/tasks/{id}",[TaskController::class,"update"]);
    // Route::delete("/tasks/{id}",[TaskController::class,"delete"]);
    Route::apiResource("tasks",TaskController::class);
    Route::put("/tasks/changeTaskToCompleted/{id}",[TaskController::class,"changeTaskToCompleted"]);
    Route::put("/tasks/changeTaskToInProgress/{id}",[TaskController::class,"changeTaskToInProgress"]);
    Route::put("/tasks/changeTaskToToDo/{id}",[TaskController::class,"changeTaskToToDo"]);





Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
