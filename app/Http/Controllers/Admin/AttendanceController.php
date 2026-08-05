<?php

namespace App\Http\Controllers\Admin;

use App\Models\{Attendance, Employee};
use App\Imports\AttendancesImport;
use App\Exports\AttendanceExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceController extends Controller
{
    /**
     * Daftar absensi dengan filter
     */
    public function index(Request $request)
    {
        $query = Attendance::with('employee');

        // Filter berdasarkan employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(20);
        $employees = Employee::all();
        $statuses = ['on_time', 'late', 'absent'];

        return view('admin.attendance.index', [
            'attendances' => $attendances,
            'employees' => $employees,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Tampilkan detail absensi
     */
    public function show($id)
    {
        $attendance = Attendance::with('employee', 'overtime')->findOrFail($id);

        return view('admin.attendance.show', [
            'attendance' => $attendance,
        ]);
    }

    /**
     * Form import absensi dari CSV/Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        try {
            $import = new AttendancesImport();
            Excel::import($import, $request->file('file'));

            $results = $import->getResults();

            $message = "Import selesai: {$results['success']} data berhasil diimport";
            if ($results['skipped'] > 0) {
                $message .= ", {$results['skipped']} dilewati/duplikat";
            }

            return back()
                ->with('success', $message)
                ->with('import_errors', $results['errors']);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal import file: ' . $e->getMessage()]);
        }
    }

    /**
     * Export absensi ke PDF
     */
    public function export(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $attendances = Attendance::with('employee')
            ->whereBetween('date', [$request->date_from, $request->date_to])
            ->orderBy('date')
            ->get();

        $pdf = Pdf::loadView('admin.attendance.export-pdf', [
            'attendances' => $attendances,
            'dateFrom' => $request->date_from,
            'dateTo' => $request->date_to,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('absensi-' . now()->format('d-m-Y') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new AttendanceExport(
                startDate:  $request->date_from,
                endDate:    $request->date_to,
                employeeId: $request->employee_id,
            ),
            'absensi-' . now()->format('d-m-Y') . '.xlsx'
        );
    }
}