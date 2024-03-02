<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\ClubEnrollmentController;
use App\Http\Controllers\ClubScheduleController;
use App\Http\Controllers\ClubSessionController;
use App\Http\Controllers\StudentClassController;
use App\Http\Controllers\StudentController;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::middleware('api')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });

    Route::prefix('student')->group(function () {
        Route::get('', [StudentController::class, 'index'])->name('index');
        Route::post('', [StudentController::class, 'store'])->name('store');
        Route::put('/{id}', [StudentController::class, 'update'])->whereUuid('id')->name('update');
        Route::delete('/{id}', [StudentController::class, 'destroy'])->whereUuid('id')->name('destroy');
    });

    Route::prefix('class')->group(function () {
        Route::get('', [StudentClassController::class, 'index'])->name('index');
        Route::post('', [StudentClassController::class, 'store'])->name('store');
        Route::put('/{id?}', [StudentClassController::class, 'update'])->name('update');
        Route::delete('/{id?}', [StudentClassController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('club')->group(function () {
        Route::get('', [ClubController::class, 'index'])->name('index');
        Route::post('', [ClubController::class, 'store'])->name('store');
        Route::put('/{id?}', [ClubController::class, 'update'])->name('update');
        Route::delete('/{id?}', [ClubController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('club-enrollment')->group(function () {
        Route::get('', [ClubEnrollmentController::class, 'index'])->name('index');
        Route::post('', [ClubEnrollmentController::class, 'store'])->name('store');
        Route::put('/{id?}', [ClubEnrollmentController::class, 'update'])->name('update');
        Route::delete('/{id?}', [ClubEnrollmentController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('club-schedule')->group(function () {
        Route::get('', [ClubScheduleController::class, 'index'])->name('index');
        Route::post('', [ClubScheduleController::class, 'store'])->name('store');
        Route::put('/{id?}', [ClubScheduleController::class, 'update'])->name('update');
        Route::delete('/{id?}', [ClubScheduleController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('club-session')->group(function () {
        Route::get('', [ClubSessionController::class, 'index'])->name('index');
        Route::post('', [ClubSessionController::class, 'store'])->name('store');
        Route::put('/{id?}', [ClubSessionController::class, 'update'])->name('update');
        Route::delete('/{id?}', [ClubSessionController::class, 'destroy'])->name('destroy');
    });
});


