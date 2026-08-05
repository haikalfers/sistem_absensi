<?php

namespace App\Http\Controllers\Employee;

use App\Models\{Attendance, LeaveRequest, PayrollDetail};
use Carbon\Carbon;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $employee = auth()->user()->employee;

        // Guard: jika user belum punya data employee (data tidak konsisten)
        if (!$employee) {
            abort(403, 'Data karyawan tidak ditemukan untuk akun ini. Hubungi administrator.');
        }

        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisMonthEnd = Carbon::now()->endOfMonth();

        // Absensi hari ini
        $attendanceToday = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        // Statistik absensi bulan ini
        $attendancesThisMonth = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$thisMonth, $thisMonthEnd])
            ->get();

        $presentThisMonth = $attendancesThisMonth->whereIn('status', ['on_time', 'late'])->count();
        $lateThisMonth = $attendancesThisMonth->where('status', 'late')->count();
        $absentThisMonth = $attendancesThisMonth->where('status', 'absent')->count();

        // Sisa cuti tahunan
        $annualLeaveBalance = $employee->annual_leave_balance;

        // Pengajuan cuti pending
        $pendingLeaveRequests = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->count();

        // Payslip terakhir
        $latestPayslip = PayrollDetail::where('employee_id', $employee->id)
            ->with('payroll')
            ->latest()
            ->first();

        // Quick stats
        $stats = [
            'status_hari_ini' => $attendanceToday?->status ?? 'Belum absen',
            'jam_masuk' => $attendanceToday?->check_in?->format('H:i') ?? '-',
            'jam_keluar' => $attendanceToday?->check_out?->format('H:i') ?? '-',
            'hadir_bulan_ini' => $presentThisMonth,
            'terlambat_bulan_ini' => $lateThisMonth,
            'alpha_bulan_ini' => $absentThisMonth,
            'sisa_cuti_tahunan' => $annualLeaveBalance,
        ];

        return view('employee.dashboard', [
            'employee' => $employee,
            'stats' => $stats,
            'attendanceToday' => $attendanceToday,
            'pendingLeaveRequests' => $pendingLeaveRequests,
            'latestPayslip' => $latestPayslip,
        ]);
    }
}