<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MeetingsRoomController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeImportExportController;
use App\Http\Controllers\WorkUnitController;
use App\Http\Controllers\EmployeeTypeController;
use App\Http\Controllers\MinutesController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\SystemSettingController;

// ─── Authentication & Public ───────────────────────────────────────────────
Route::get('/auth/mode', [AuthController::class, 'getAuthMode']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/system-settings/logos', [SystemSettingController::class, 'getLogos']);

// ─── Protected Routes ──────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Employees
    Route::get('/employees/export', [EmployeeImportExportController::class, 'export']);
    Route::post('/employees/import', [EmployeeImportExportController::class, 'import']);
    
    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::get('/employee/{id}', [EmployeeController::class, 'show']);
    Route::post('/employee', [EmployeeController::class, 'store']);
    Route::patch('/employee/{id}', [EmployeeController::class, 'update']);
    Route::delete('/employee/{id}', [EmployeeController::class, 'destroy']);

    // Reference Data (Dropdowns)
    Route::get('/employee-types', [EmployeeController::class, 'employeeTypes']);
    Route::get('/work-units', [EmployeeController::class, 'workUnits']);
    Route::get('/roles', [UserController::class, 'roles']);

    // Users
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::post('/user', [UserController::class, 'store']);
    Route::patch('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);

    // Meeting Rooms
    Route::get('/meeting-rooms', [MeetingsRoomController::class, 'index']);
    Route::get('/meeting-room/{id}', [MeetingsRoomController::class, 'show']);
    Route::post('/meeting-room', [MeetingsRoomController::class, 'store']);
    Route::patch('/meeting-room/{id}', [MeetingsRoomController::class, 'update']);
    Route::patch('/meeting-room/{id}/toggle-status', [MeetingsRoomController::class, 'toggleStatus']);
    Route::delete('/meeting-room/{id}', [MeetingsRoomController::class, 'destroy']);

    // Work Units (Manage)
    Route::get('/work-units-manage', [WorkUnitController::class, 'index']);
    Route::get('/work-unit/{id}', [WorkUnitController::class, 'show']);
    Route::post('/work-unit', [WorkUnitController::class, 'store']);
    Route::patch('/work-unit/{id}', [WorkUnitController::class, 'update']);
    Route::delete('/work-unit/{id}', [WorkUnitController::class, 'destroy']);

    // Employee Types (Manage)
    Route::get('/employee-types-manage', [EmployeeTypeController::class, 'index']);
    Route::get('/employee-type/{id}', [EmployeeTypeController::class, 'show']);
    Route::post('/employee-type', [EmployeeTypeController::class, 'store']);
    Route::patch('/employee-type/{id}', [EmployeeTypeController::class, 'update']);
    Route::delete('/employee-type/{id}', [EmployeeTypeController::class, 'destroy']);

    // Meetings
    Route::get('/meetings', [MeetingController::class, 'index']);
    Route::get('/meeting/{id}', [MeetingController::class, 'show']);
    Route::post('/meeting', [MeetingController::class, 'store']);
    Route::patch('/meeting/{id}', [MeetingController::class, 'update']);
    Route::delete('/meeting/{id}', [MeetingController::class, 'destroy']);
    Route::patch('/meetings/update-statuses', [MeetingController::class, 'updateStatuses']);
    
    // Attendance
    Route::post('/meeting/{id}/scan', [MeetingController::class, 'scanBarcode']);
    Route::patch('/meeting/{id}/attendance/{participantId}', [MeetingController::class, 'manualAttendance']);

    // Meeting Minutes (Notulensi)
    Route::get('/meeting/{meetingId}/minutes', [MinutesController::class, 'show']);
    Route::post('/meeting/{meetingId}/minutes', [MinutesController::class, 'upsert']);
    Route::post('/minutes/upload-image', [MinutesController::class, 'uploadImage']);

    // Documents
    Route::post('/meeting/{meetingId}/documents', [MinutesController::class, 'uploadDocument']);
    Route::delete('/meeting/{meetingId}/documents/{documentId}', [MinutesController::class, 'deleteDocument']);

    // Laporan (Reports)
    Route::get('/laporan/rapat', [LaporanController::class, 'index']);
    Route::get('/laporan/rapat/{id}', [LaporanController::class, 'show']);
    Route::get('/laporan/rapat/{id}/export', [LaporanController::class, 'export']);

    // Backups
    Route::get('/backups', [BackupController::class, 'index']);
    Route::get('/backups/stats', [BackupController::class, 'stats']);
    Route::post('/backup', [BackupController::class, 'store']);
    Route::get('/backup/{id}/download', [BackupController::class, 'download']);
    Route::post('/backup/{id}/cancel', [BackupController::class, 'cancel']);
    Route::delete('/backup/{id}', [BackupController::class, 'destroy']);

    // System Settings
    Route::post('/logo/upload', [SystemSettingController::class, 'uploadLogo']);
});