<?php

use App\Http\Controllers\Api\ToDoController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\VerificationController;
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
Route::prefix('user')->group(function () {
    Route::group(['middleware' => 'jwt.auth'], function () {
        Route::post('/refresh', [UserApiController::class, 'refresh']);

        // Create To Do
        Route::post('/create-to-do', [ToDoController::class, 'create_todo'])->name('create-todo');
        Route::post('/view-to-do', [ToDoController::class, 'view_todo'])->name('view-todo');
        Route::post('/update-to-do', [ToDoController::class, 'update_todo'])->name('update-todo');
        Route::post('/delete-to-do', [ToDoController::class, 'delete_todo'])->name('delete-todo');
        Route::get('/list-to-do/{user}', [ToDoController::class, 'list_todo'])->name('list-todo');
    });

    // Route::prefix('user')->group(function () {
    Route::controller(UserApiController::class)->group(function () {
        Route::post('/login', 'login')->name('login');
        Route::post('/register', 'register')->name('register');
        Route::post('/logout', 'logout');
    });
});

Route::get('/verify-token/{token}', [VerificationController::class,  'verify_token'])->name('verify-token');
