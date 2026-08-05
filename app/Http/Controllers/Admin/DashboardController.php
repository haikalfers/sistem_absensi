<?php

namespace App\Http\Controllers\Admin;

use App\Models\{Employee, Attendance, LeaveRequest, Payroll};
use Carbon\Carbon;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisMonthEnd = Carbon::now()->endOfMonth();

        // Statistik karyawan
        $totalEmployees = Employee::count();
        $totalAttendanceToday = Attendance::whereDate('date', $today)->count();
        $presentToday = Attendance::whereDate('date', $today)
            ->whereIn('status', ['on_time', 'late'])
            ->count();
        $absentToday = Attendance::whereDate('date', $today)
            ->where('status', 'absent')
            ->count();

        // Statistik cuti
        $pendingLeaveRequests = LeaveRequest::where('status', 'pending')->count();
        $approvedThisMonth = LeaveRequest::where('status', 'approved')
            ->whereBetween('start_date', [$thisMonth, $thisMonthEnd])
            ->count();

        // Statistik penggajian
        $totalPayrolls = Payroll::count();
        $lastPayroll = Payroll::latest()->first();

        // Data untuk chart absensi (7 hari terakhir)
        $attendanceChart = $this->getAttendanceChartData();

        // Data untuk chart keterlambatan (7 hari terakhir)
        $lateChart = $this->getLateChartData();

        return view('admin.dashboard', [
            'totalEmployees' => $totalEmployees,
            'totalAttendanceToday' => $totalAttendanceToday,
            'presentToday' => $presentToday,
            'absentToday' => $absentToday,
            'pendingLeaveRequests' => $pendingLeaveRequests,
            'approvedThisMonth' => $approvedThisMonth,
            'totalPayrolls' => $totalPayrolls,
            'lastPayroll' => $lastPayroll,
            'attendanceChart' => $attendanceChart,
            'lateChart' => $lateChart,
        ]);
    }

    /**
     * Data untuk chart absensi 7 hari terakhir
     */
    private function getAttendanceChartData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $present = Attendance::whereDate('date', $date)
                ->whereIn('status', ['on_time', 'late'])
                ->count();
            $absent = Attendance::whereDate('date', $date)
                ->where('status', 'absent')
                ->count();

            $data[] = [
                'date' => $date->format('d M'),
                'present' => $present,
                'absent' => $absent,
            ];
        }
        return $data;
    }

    /**
     * Data untuk chart keterlambatan 7 hari terakhir
     */
    private function getLateChartData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $lateCount = Attendance::whereDate('date', $date)
                ->where('status', 'late')
                ->count();

            $data[] = [
                'date' => $date->format('d M'),
                'late' => $lateCount,
            ];
        }
        return $data;
    }
}