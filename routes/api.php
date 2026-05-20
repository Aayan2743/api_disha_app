<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FollowupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\staffcontroller;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/admin-login', [AuthController::class, 'login']);
Route::post('/telecaller-login', [AuthController::class, 'telecallerLogin']);

// Route::middleware('auth:api')->group(function () {
//     Route::post('/add-staff', [staffcontroller::class, 'addStaff']);
// });

// Admin
Route::group([
    'prefix'     => 'admin',
    'middleware' => 'auth:api',
], function () {

    Route::post('/add-staff', [StaffController::class, 'addStaff']);

    Route::get('/staff-list', [StaffController::class, 'staffList']);

    Route::get('/staff-details/{id}', [StaffController::class, 'staffDetails']);

    Route::post('/update-staff/{id}', [StaffController::class, 'updateStaff']);
//
    Route::delete('/delete-staff/{id}', [StaffController::class, 'deleteStaff']);

    Route::post('/toggle-status/{id}', [StaffController::class, 'toggleStatus']);

    Route::group([
        'prefix' => 'profile',

    ], function () {

        Route::post('/update-profile', [ProfileController::class, 'updateProfile']);
        Route::post('/change-password', [ProfileController::class, 'changePassword']);

    });

});

// Attendance
Route::group([

    'prefix'     => 'attendance',

    'middleware' => 'auth:api',

], function () {

    Route::post('/mark', [AttendanceController::class, 'attendance']);
    Route::post('/attendance-details', [AttendanceController::class, 'attendanceDetails']);

});

// client
Route::group([

    'prefix'     => 'client',

    'middleware' => 'auth:api',

], function () {

    Route::post('/add-client', [ClientController::class, 'addClient']);
    Route::get('/client-list', [ClientController::class, 'clientList']);
    Route::get('/client-details/{id}', [ClientController::class, 'showClient']);

});

// appointment

Route::group([

    'prefix'     => 'appointment',

    'middleware' => 'auth:api',

], function () {

    Route::post('/add-appointment', [AppointmentController::class, 'addAppointment']);
    Route::get('/appointment-list', [AppointmentController::class, 'appointmentList']);

});

Route::group([

    'prefix'     => 'dashboard',

    'middleware' => 'auth:api',

], function () {

    Route::get(
        '/analytics',
        [DashboardController::class, 'dashboardAnalytics']
    );

});

// followup

Route::group([

    'prefix'     => 'followup',

    'middleware' => 'auth:api',

], function () {

    Route::post(
        '/add-followup',
        [FollowupController::class, 'addFollowup']
    );

    Route::get(
        '/followup-list',
        [FollowupController::class, 'followupList']
    );

});