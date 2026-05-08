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
use App\Http\Controllers\MinutesController;
use App\Http\Controllers\LaporanController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
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

    // Route for work units
    Route::get('/work-units-manage', [WorkUnitController::class, 'index']);
    Route::get('/work-unit/{id}', [WorkUnitController::class, 'show']);
    Route::post('/work-unit', [WorkUnitController::class, 'store']);
    Route::patch('/work-unit/{id}', [WorkUnitController::class, 'update']);
    Route::delete('/work-unit/{id}', [WorkUnitController::class, 'destroy']);

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

    // Meeting Minutes (Notulensi Rapat)
    Route::get('/meeting/{meetingId}/minutes', [MinutesController::class, 'show']);
    Route::post('/meeting/{meetingId}/minutes', [MinutesController::class, 'upsert']);

    // Upload image for Quill editor
    Route::post('/minutes/upload-image', [MinutesController::class, 'uploadImage']);

    // Dokumen rapat (undangan, lampiran, dll.)
    Route::post('/meeting/{meetingId}/documents', [MinutesController::class, 'uploadDocument']);
    Route::delete('/meeting/{meetingId}/documents/{documentId}', [MinutesController::class, 'deleteDocument']);

    // ─── Laporan Rapat (Dashboard Sekretaris) ────────────────────────────────────
    // Daftar rapat + status kelengkapan lampiran (filter: search, date, status)
    Route::get('/laporan/rapat', [LaporanController::class, 'index']);
    // Detail satu rapat: peserta, kehadiran, notulensi, dokumen
    Route::get('/laporan/rapat/{id}', [LaporanController::class, 'show']);
    // Export data rapat lengkap (untuk FE generate PDF)
    Route::get('/laporan/rapat/{id}/export', [LaporanController::class, 'export']);
});