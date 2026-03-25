<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MeetingsRoomController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeImportExportController;

Route::post('/login', [AuthController::class, 'login']);

// Route for dashboard
Route::get('/dashboard', [DashboardController::class, 'index']);
Route::post('/logout', [AuthController::class, 'logout']);

// Route import export employee
Route::get('/employees/export', [EmployeeImportExportController::class, 'export']);
Route::post('/employees/import', [EmployeeImportExportController::class, 'import']);
// Route for employees
Route::get('/employees', [EmployeeController::class, 'index']);
Route::get('/employee/{id}', [EmployeeController::class, 'show']);
Route::post('/employee', [EmployeeController::class, 'store']);
Route::patch('/employee/{id}', [EmployeeController::class, 'update']);
Route::delete('/employee/{id}', [EmployeeController::class, 'destroy']);

// Route for dropdown data (jenis tenaga & unit kerja)
Route::get('/employee-types', [EmployeeController::class, 'employeeTypes']);
Route::get('/work-units', [EmployeeController::class, 'workUnits']);

// Route for users
Route::get('/users', [UserController::class, 'index']);
Route::get('/user/{id}', [UserController::class, 'show']);
Route::post('/user', [UserController::class, 'store']);
Route::patch('/user/{id}', [UserController::class, 'update']);
Route::delete('/user/{id}', [UserController::class, 'destroy']);

// Route for dropdown data (roles)
Route::get('/roles', [UserController::class, 'roles']);

// Route for meeting rooms
Route::get('/meeting-rooms', [MeetingsRoomController::class, 'index']);
Route::get('/meeting-room/{id}', [MeetingsRoomController::class, 'show']);
Route::post('/meeting-room', [MeetingsRoomController::class, 'store']);
Route::patch('/meeting-room/{id}', [MeetingsRoomController::class, 'update']);
Route::patch('/meeting-room/{id}/toggle-status', [MeetingsRoomController::class, 'toggleStatus']);
Route::delete('/meeting-room/{id}', [MeetingsRoomController::class, 'destroy']);

// Route for meetings
Route::get('/meetings', [MeetingController::class, 'index']);
Route::get('/meeting/{id}', [MeetingController::class, 'show']);
Route::post('/meeting', [MeetingController::class, 'store']);
Route::patch('/meeting/{id}', [MeetingController::class, 'update']);
Route::delete('/meeting/{id}', [MeetingController::class, 'destroy']);

// Meeting status batch update
Route::patch('/meetings/update-statuses', [MeetingController::class, 'updateStatuses']);

// Attendance
Route::post('/meeting/{id}/scan', [MeetingController::class, 'scanBarcode']);
Route::patch('/meeting/{id}/attendance/{participantId}', [MeetingController::class, 'manualAttendance']);