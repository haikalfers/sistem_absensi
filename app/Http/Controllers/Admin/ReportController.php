<?php

namespace App\Http\Controllers\Admin;

use App\Models\{Attendance, PayrollDetail, LeaveRequest};
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * View laporan absensi
     */
    public function attendance(Request $request)
    {
        $query = Attendance::with('employee');

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(50);

        return view('admin.reports.attendance', [
            'attendances' => $attendances,
        ]);
    }

    /**
     * Export laporan absensi ke Excel
     */
    public function attendanceExport(Request $request)
    {
        return Excel::download(
            new \App\Exports\AttendanceExport(
                $request->date_from ?? now()->startOfMonth(),
                $request->date_to ?? now()->endOfMonth()
            ),
            'laporan-absensi-' . now()->format('d-m-Y') . '.xlsx'
        );
    }

    /**
     * View laporan penggajian
     */
    public function payroll(Request $request)
    {
        $query = PayrollDetail::with('payroll', 'employee');

        if ($request->filled('payroll_id')) {
            $query->where('payroll_id', $request->payroll_id);
        }

        $details = $query->paginate(50);
        $payrolls = \App\Models\Payroll::all();

        return view('admin.reports.payroll', [
            'details' => $details,
            'payrolls' => $payrolls,
        ]);
    }

    /**
     * Export laporan penggajian ke Excel
     */
    public function payrollExport(Request $request)
    {
        return Excel::download(
            new \App\Exports\PayrollExport($request->payroll_id ?? null),
            'laporan-penggajian-' . now()->format('d-m-Y') . '.xlsx'
        );
    }

    /**
     * View laporan cuti
     */
    public function leave(Request $request)
    {
        $query = LeaveRequest::with('employee', 'leaveType');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaveRequests = $query->orderBy('start_date', 'desc')->paginate(50);
        $statuses = ['pending', 'approved', 'rejected'];

        return view('admin.reports.leave', [
            'leaveRequests' => $leaveRequests,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Export laporan cuti ke Excel
     */
    public function leaveExport(Request $request)
    {
        return Excel::download(
            new \App\Exports\LeaveExport($request->status ?? null),
            'laporan-cuti-' . now()->format('d-m-Y') . '.xlsx'
        );
    }
}