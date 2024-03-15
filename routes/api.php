<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatGPTController;

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

Route::post('/sign-up', [AuthController::class, 'register']); //http://127.0.0.1:8000/api/sign-up 
Route::post('/login', [AuthController::class, 'login']); //http://127.0.0.1:8000/api/login 


Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::get('/me', [AuthController::class, 'me']); //http://127.0.0.1:8000/api/me   (with bearer token)
    Route::post('/logout', [AuthController::class, 'logout']); //http://127.0.0.1:8000/api/logout   (with bearer token)
    Route::get('/chatgpt/generate-response', [ChatGPTController::class, 'generateResponse']); //http://127.0.0.1:8000/api/chatgpt/generate-response   (with bearer token)
    Route::get('/history', [ChatGPTController::class, 'getallHistory']);  //http://127.0.0.1:8000/api/history  (with bearer token)
});