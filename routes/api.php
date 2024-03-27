<?php

use App\Http\Controllers\AbsenceReportController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\ClubEnrollmentController;
use App\Http\Controllers\ClubScheduleController;
use App\Http\Controllers\ClubSessionController;
use App\Http\Controllers\ClubSessionPhotoController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StudentClassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
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

    Route::prefix('user')->group(function () {
        Route::get('/all', [UserController::class, 'all'])->name('all');
        Route::get('', [UserController::class, 'index'])->name('index');
        Route::get('{id}', [UserController::class, 'show'])->name('show');
    });

    Route::prefix('student')->group(function () {
        Route::get('/all', [StudentController::class, 'all'])->name('all');
        Route::get('', [StudentController::class, 'index'])->name('index');
        Route::post('', [StudentController::class, 'store'])->name('store');
        Route::get('{id}', [StudentController::class, 'show'])->name('show');
        Route::put('/{id}', [StudentController::class, 'update'])->whereUuid('id')->name('update');
        Route::delete('/{id}', [StudentController::class, 'destroy'])->whereUuid('id')->name('destroy');
    });

    Route::prefix('class')->group(function () {
        Route::get('/all', [StudentClassController::class, 'all'])->name('all');
        Route::get('', [StudentClassController::class, 'index'])->name('index');
        Route::post('', [StudentClassController::class, 'store'])->name('store');
        Route::get('{id}', [StudentClassController::class, 'show'])->name('show');
        Route::put('/{id?}', [StudentClassController::class, 'update'])->name('update');
        Route::delete('/{id?}', [StudentClassController::class, 'destroy'])->name('destroy');
        Route::post('/assign-students', [StudentClassController::class, 'assignStudents'])->name('assign-students');
    });

    Route::prefix('club')->group(function () {
        Route::get('/all', [ClubController::class, 'all'])->name('all');
        Route::get('', [ClubController::class, 'index'])->name('index');
        Route::post('', [ClubController::class, 'store'])->name('store');
        Route::get('{id}', [ClubController::class, 'show'])->name('show');
        Route::get('{id}/students', [ClubController::class, 'students'])->name('students');
        Route::put('/{id?}', [ClubController::class, 'update'])->name('update');
        Route::delete('/{id?}', [ClubController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('club-enrollment')->group(function () {
        Route::get('/all', [ClubEnrollmentController::class, 'all'])->name('all');
        Route::get('', [ClubEnrollmentController::class, 'index'])->name('index');
        Route::post('', [ClubEnrollmentController::class, 'store'])->name('store');
        Route::delete('/{id?}', [ClubEnrollmentController::class, 'destroy'])->name('destroy');
        Route::post('/assign-students', [ClubEnrollmentController::class, 'assignStudents'])->name('assignStudents');

    });

    Route::prefix('club-schedule')->group(function () {
        Route::get('/all', [ClubScheduleController::class, 'all'])->name('all');
        Route::get('', [ClubScheduleController::class, 'index'])->name('index');
        Route::post('', [ClubScheduleController::class, 'store'])->name('store');
        Route::get('{id}', [ClubScheduleController::class, 'show'])->name('show');
        Route::get('/club/{id}', [ClubScheduleController::class, 'getByClub'])->name('getByClub');
        Route::put('/{id?}', [ClubScheduleController::class, 'update'])->name('update');
        Route::delete('/{id?}', [ClubScheduleController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('club-session')->group(function () {
        Route::get('/all', [ClubSessionController::class, 'all'])->name('all');
        Route::get('', [ClubSessionController::class, 'index'])->name('index');
        Route::post('', [ClubSessionController::class, 'store'])->name('store');
        Route::get('{id}', [ClubSessionController::class, 'show'])->name('show');
        Route::get('/club/{id}', [ClubSessionController::class, 'getByClub'])->name('getByClub');
        Route::put('/{id?}', [ClubSessionController::class, 'update'])->name('update');
        Route::delete('/{id?}', [ClubSessionController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('absence-report')->group(function () {
        Route::get('/all', [AbsenceReportController::class, 'all'])->name('all');
        Route::get('', [AbsenceReportController::class, 'index'])->name('index');
        Route::post('', [AbsenceReportController::class, 'store'])->name('store');
        Route::get('/club-session/{id}', [AbsenceReportController::class, 'getByClubSession'])->name('getByClubSession');
        Route::put('/{id?}', [AbsenceReportController::class, 'update'])->name('update');
        Route::delete('/{id?}', [AbsenceReportController::class, 'destroy'])->name('destroy');
        Route::get('/session/{id?}', [AbsenceReportController::class, 'getBySession'])->name('getBySession');

    });

    Route::prefix('attendance')->group(function () {
        Route::get('/all', [AttendanceController::class, 'all'])->name('all');
        Route::get('', [AttendanceController::class, 'index'])->name('index');
        Route::post('', [AttendanceController::class, 'store'])->name('store');
        Route::get('/club-session/{id}', [AttendanceController::class, 'getByClubSession'])->name('getByClubSession');
        Route::put('/{id?}', [AttendanceController::class, 'update'])->name('update');
        Route::put('update-many/{id?}', [AttendanceController::class, 'updateMany'])->name('updateMany');
        Route::delete('/{id?}', [AttendanceController::class, 'destroy'])->name('destroy');
        Route::get('/club/{club_id?}/student/{student_id?}', [AttendanceController::class, 'statisticStudents'])->name('statisticStudents');
    });

    Route::prefix('club-session-photo')->group(function () {
        Route::get('/all', [ClubSessionPhotoController::class, 'all'])->name('all');
        Route::get('', [ClubSessionPhotoController::class, 'index'])->name('index');
        Route::post('', [ClubSessionPhotoController::class, 'store'])->name('store');
        Route::get('/club/{id}', [ClubSessionPhotoController::class, 'getByClub'])->name('getByClub');
        Route::get('/club-session/{id}', [ClubSessionPhotoController::class, 'getByClubSession'])->name('getByClubSession');
        Route::put('/{id?}', [ClubSessionPhotoController::class, 'update'])->name('update');
        Route::delete('/{id?}', [ClubSessionPhotoController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('comment')->group(function () {
        Route::get('/all', [CommentController::class, 'all'])->name('all');
        Route::get('', [CommentController::class, 'index'])->name('index');
        Route::post('', [CommentController::class, 'store'])->name('store');
        Route::get('/club-session/{id}', [CommentController::class, 'getByClubSession'])->name('getByClubSession');
        Route::put('/{id?}', [CommentController::class, 'update'])->name('update');
        Route::delete('/{id?}', [CommentController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('notification')->group(function () {
        Route::get('/all', [NotificationController::class, 'all'])->name('all');
        Route::get('', [NotificationController::class, 'index'])->name('index');
        Route::post('', [NotificationController::class, 'store'])->name('store');
        Route::put('/{id?}', [NotificationController::class, 'update'])->name('update');
        Route::delete('/{id?}', [NotificationController::class, 'destroy'])->name('destroy');
    });
});


