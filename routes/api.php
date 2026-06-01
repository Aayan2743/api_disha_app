<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FollowupController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\staffcontroller;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/admin-login', [AuthController::class, 'login']);
Route::post('/telecaller-login', [AuthController::class, 'telecallerLogin']);
Route::post('/receptionist-login', [AuthController::class, 'receptionistLogin']);

// Route::middleware('auth:api')->group(function () {
//     Route::post('/add-staff', [staffcontroller::class, 'addStaff']);
// });

// Admin
Route::group([
    'prefix'     => 'admin',
    'middleware' => 'jwt.api',
], function () {

    Route::post('/add-staff', [StaffController::class, 'addStaff']);

    Route::get('/staff-list', [StaffController::class, 'staffList']);

    Route::get('/staff-details/{id}', [StaffController::class, 'staffDetails']);

    Route::post('/update-staff/{id}', [StaffController::class, 'updateStaff']);
//
    Route::delete('/delete-staff/{id}', [StaffController::class, 'deleteStaff']);

    Route::post('/toggle-status/{id}', [StaffController::class, 'toggleStatus']);

    Route::get('/leads', [LeadController::class, 'allLeads']);

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

    'middleware' => 'jwt.api',

], function () {

    Route::post('/mark', [AttendanceController::class, 'attendance']);
    Route::post('/attendance-details', [AttendanceController::class, 'attendanceDetails']);

    //web
    Route::post(
        '/staff-attendance',
        [AttendanceController::class, 'staffAttendance']
    );

    Route::post(
        '/punch-in',
        [AttendanceController::class, 'punchIn']
    );

    Route::post(
        '/punch-out',
        [AttendanceController::class, 'punchOut']
    );

    Route::get(
        '/today-session',
        [AttendanceController::class, 'todaySession']
    );

});

// client
Route::group([

    'prefix'     => 'client',

    'middleware' => 'jwt.api',

], function () {

    Route::post('/add-client', [ClientController::class, 'addClient']);
    Route::get('/client-list', [ClientController::class, 'clientList']);
    Route::get('/client-details/{id}', [ClientController::class, 'showClient']);
    Route::post('/update-client/{id}', [ClientController::class, 'updateClient']);
    Route::delete('/delete-client/{id}', [ClientController::class, 'deleteClient']);
    Route::get('/search-by-phone', [ClientController::class, 'searchByPhone']);

    Route::get(
        '/all-clients',
        [ClientController::class, 'allClients']
    );

});

// appointment

Route::group([

    'prefix'     => 'appointment',

    'middleware' => 'jwt.api',

], function () {

    Route::post('/add-appointment-mobile', [AppointmentController::class, 'addAppointment_using_clinet']);
    Route::post(
        '/update-appointment/{id}',
        [AppointmentController::class, 'updateAppointment_using_client']
    );
    Route::post('/add-appointment', [AppointmentController::class, 'addAppointment']);
    Route::get('/appointment-list', [AppointmentController::class, 'appointmentList']);
    Route::delete('/delete-appointment/{id}', [AppointmentController::class, 'deleteAppointment']);

    // using for receptionist to view appointments
    // Route::post('/store', [AppointmentController::class, 'store']);
    Route::post('/fetch-client', [AppointmentController::class, 'fetchClient']);
    // Route::post('/all-appointments', [AppointmentController::class, 'allAppointments']);
    Route::get('/all-appointments', [AppointmentController::class, 'allAppointments']);
    Route::post('/mark-reached/{id}', [AppointmentController::class, 'markReached']);
    Route::post('/fee-collected/{id}', [AppointmentController::class, 'feeCollected']);

    Route::post(
        '/calendar-appointments',
        [AppointmentController::class, 'calendarAppointments']
    );

    Route::post(
        '/date-appointments',
        [AppointmentController::class, 'dateAppointments']
    );

});

Route::group([

    'prefix'     => 'dashboard',

    'middleware' => 'jwt.api',

], function () {

    Route::get(
        '/analytics',
        [DashboardController::class, 'dashboardAnalytics']
    );

    Route::get(
        '/reception-dashboard',
        [DashboardController::class, 'receptionDashboard']
    );

    Route::get(
        '/admin-dashboard',
        [DashboardController::class, 'adminDashboard']
    );

});

// followup

Route::group([

    'prefix'     => 'followup',

    'middleware' => 'jwt.api',

], function () {

    Route::post(
        '/add-followup',
        [FollowupController::class, 'addFollowup']
    );

    Route::get(
        '/followup-list',
        [FollowupController::class, 'followupList']
    );

    Route::get('/all-followups', [FollowupController::class, 'allFollowUps']);
    Route::post(
        '/update-followup/{id}',
        [FollowupController::class, 'updateFollowup']
    );

});

Route::group([

    'prefix'     => 'collection',

    'middleware' => 'jwt.api',

], function () {

    Route::post(
        '/all-collections',
        [CollectionController::class, 'allCollections']
    );

});
