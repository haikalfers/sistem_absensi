<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Employee;
use Illuminate\Support\Facades\Route;

// ============================================
// PUBLIC ROUTES (Tidak perlu login)
// ============================================

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/offline', fn() => view('offline'))->name('offline');

// ============================================
// AUTHENTICATED ROUTES
// ============================================

Route::middleware('auth')->group(function () {
    // Dashboard redirect berdasarkan role
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('employee.dashboard');
    })->name('dashboard');

    // ============================================
    // ADMIN ROUTES
    // ============================================
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Kelola Karyawan
        Route::resource('employees', Admin\EmployeeController::class);

        // Absensi
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [Admin\AttendanceController::class, 'index'])->name('index');
            Route::post('/import', [Admin\AttendanceController::class, 'import'])->name('import');
            Route::get('/{id}', [Admin\AttendanceController::class, 'show'])->name('show');
            Route::get('/{id}/export', [Admin\AttendanceController::class, 'export'])->name('export');
        });

        // Lembur
        Route::prefix('overtime')->name('overtime.')->group(function () {
            Route::get('/', [Admin\OvertimeController::class, 'index'])->name('index');
            Route::get('/{id}/edit', [Admin\OvertimeController::class, 'edit'])->name('edit');
            Route::put('/{id}', [Admin\OvertimeController::class, 'update'])->name('update');
            Route::put('/{id}/validate', [Admin\OvertimeController::class, 'validate'])->name('validate');
        });

        // Cuti & Izin
        Route::prefix('leave-requests')->name('leave-requests.')->group(function () {
            Route::get('/', [Admin\LeaveRequestController::class, 'index'])->name('index');
            Route::get('/{id}', [Admin\LeaveRequestController::class, 'show'])->name('show');
            Route::put('/{id}/approve', [Admin\LeaveRequestController::class, 'approve'])->name('approve');
            Route::put('/{id}/reject', [Admin\LeaveRequestController::class, 'reject'])->name('reject');
        });

        // Pengajuan Presensi Ulang
        Route::prefix('attendance-revisions')->name('attendance-revisions.')->group(function () {
            Route::get('/', [Admin\AttendanceRevisionController::class, 'index'])->name('index');
            Route::get('/{id}', [Admin\AttendanceRevisionController::class, 'show'])->name('show');
            Route::put('/{id}/approve', [Admin\AttendanceRevisionController::class, 'approve'])->name('approve');
            Route::put('/{id}/reject', [Admin\AttendanceRevisionController::class, 'reject'])->name('reject');
        });

        // Penggajian
        Route::prefix('payrolls')->name('payrolls.')->group(function () {
            Route::get('/', [Admin\PayrollController::class, 'index'])->name('index');
            Route::get('/create', [Admin\PayrollController::class, 'create'])->name('create');
            Route::post('/', [Admin\PayrollController::class, 'store'])->name('store');
            Route::get('/{id}', [Admin\PayrollController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [Admin\PayrollController::class, 'edit'])->name('edit');
            Route::put('/{id}', [Admin\PayrollController::class, 'update'])->name('update');
            Route::post('/{id}/generate', [Admin\PayrollController::class, 'generate'])->name('generate');
            Route::post('/{id}/revert', [Admin\PayrollController::class, 'revertToDraft'])->name('revert');
            Route::delete('/{id}', [Admin\PayrollController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/export-pdf', [Admin\PayrollController::class, 'exportPdf'])->name('export-pdf');
        });

        // Laporan
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/attendance', [Admin\ReportController::class, 'attendance'])->name('attendance');
            Route::get('/attendance/export', [Admin\ReportController::class, 'attendanceExport'])->name('attendance.export');
            Route::get('/payroll', [Admin\ReportController::class, 'payroll'])->name('payroll');
            Route::get('/payroll/export', [Admin\ReportController::class, 'payrollExport'])->name('payroll.export');
            Route::get('/leave', [Admin\ReportController::class, 'leave'])->name('leave');
            Route::get('/leave/export', [Admin\ReportController::class, 'leaveExport'])->name('leave.export');
        });

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/locations', [Admin\SettingController::class, 'locations'])->name('locations');
            Route::post('/locations', [Admin\SettingController::class, 'updateLocations'])->name('locations.update');
            Route::get('/schedules', [Admin\SettingController::class, 'schedules'])->name('schedules');
            Route::post('/schedules', [Admin\SettingController::class, 'updateSchedules'])->name('schedules.update');
        });
    });

    // ============================================
    // EMPLOYEE ROUTES (PWA)
    // ============================================
    Route::middleware('role:employee')->prefix('employee')->name('employee.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [Employee\DashboardController::class, 'index'])->name('dashboard');

        // Absensi
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [Employee\AttendanceController::class, 'index'])->name('index');
            Route::post('/check-in', [Employee\AttendanceController::class, 'checkIn'])->name('check-in');
            Route::post('/check-out', [Employee\AttendanceController::class, 'checkOut'])->name('check-out');
            Route::get('/history', [Employee\AttendanceController::class, 'history'])->name('history');
            Route::get('/summary', [Employee\AttendanceController::class, 'monthlySummary'])->name('summary');
            Route::get('/summary/export', [Employee\AttendanceController::class, 'exportMonthlySummary'])->name('summary.export');
        });

        // Cuti & Izin
        Route::prefix('leave-requests')->name('leave-requests.')->group(function () {
            Route::get('/', [Employee\LeaveRequestController::class, 'index'])->name('index');
            Route::get('/create', [Employee\LeaveRequestController::class, 'create'])->name('create');
            Route::post('/', [Employee\LeaveRequestController::class, 'store'])->name('store');
            Route::get('/{id}', [Employee\LeaveRequestController::class, 'show'])->name('show');
            Route::delete('/{id}', [Employee\LeaveRequestController::class, 'destroy'])->name('destroy');
        });

        // Pengajuan Presensi Ulang
        Route::prefix('attendance-revisions')->name('attendance-revisions.')->group(function () {
            Route::get('/', [Employee\AttendanceRevisionController::class, 'index'])->name('index');
            Route::get('/create', [Employee\AttendanceRevisionController::class, 'create'])->name('create');
            Route::post('/', [Employee\AttendanceRevisionController::class, 'store'])->name('store');
            Route::delete('/{id}', [Employee\AttendanceRevisionController::class, 'destroy'])->name('destroy');
        });

        // Payslip & Penggajian
        Route::prefix('payslip')->name('payslip.')->group(function () {
            Route::get('/', [Employee\PayslipController::class, 'index'])->name('index');
            Route::get('/{id}', [Employee\PayslipController::class, 'show'])->name('show');
            Route::get('/{id}/download', [Employee\PayslipController::class, 'download'])->name('download');
        });

        // Profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [Employee\ProfileController::class, 'show'])->name('show');
            Route::put('/', [Employee\ProfileController::class, 'update'])->name('update');
        });
    });
});

// Load auth routes
require __DIR__.'/auth.php';
// Auth routes sudah di-handle oleh routes/auth.php (login, register, password reset, logout, profile edit)