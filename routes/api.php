<?php

use App\Http\Controllers\UpdatePwdController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('req-password-reset', [UpdatePwdController::class, 'reqForgotPassword']);
Route::post('update-password', [UpdatePwdController::class, 'updatePassword']);

// Test route for view passwordToken whiteout UI
Route::get('view-token/{passwordToken}', [UpdatePwdController::class, 'viewExampleEmailToken']);

Route::group(['middleware' => 'jwt.verify'], function () {
    Route::get('users', [UserController::class, 'getAll']);
    Route::get('users/{name}/{email}', [UserController::class, 'getFiltered']);
    Route::delete('users/{id}', [UserController::class, 'delete']);
    Route::put('users/{id}', [UserController::class, 'update']);
});
