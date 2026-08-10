<?php

namespace App\Http\Controllers\Employee;

use App\Models\{Attendance, CompanyLocation};
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceController extends Controller
{
    public function __construct(private AttendanceService $attendanceService) {}

    /**
     * Halaman utama absensi (PWA - untuk check-in/check-out)
     */
    public function index()
    {
        $employee = auth()->user()->employee;
        $today = Carbon::today();

        $attendanceToday = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        $office = CompanyLocation::where('name', 'like', '%' . $employee->department . '%')->first() 
            ?? CompanyLocation::first();

        return view('employee.attendance.index', [
            'employee' => $employee,
            'attendanceToday' => $attendanceToday,
            'office' => $office,
        ]);
    }

    /**
     * Process check-in (AJAX/API)
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $employee = auth()->user()->employee;

        $result = $this->attendanceService->processCheckIn(
            employeeId: $employee->id,
            lat: $request->latitude,
            lng: $request->longitude,
            source: 'pwa'
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'attendance' => $result['attendance'],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
            'code' => $result['code'] ?? 'ERROR',
        ], 422);
    }

    /**
     * Process check-out (AJAX/API)
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $employee = auth()->user()->employee;

        $result = $this->attendanceService->processCheckOut(
            employeeId: $employee->id,
            lat: $request->latitude ?? 0,
            lng: $request->longitude ?? 0,
            source: 'pwa'
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'attendance' => $result['attendance'],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
            'code' => $result['code'] ?? 'ERROR',
        ], 422);
    }

    /**
     * Riwayat absensi (list semua)
     */
    public function history(Request $request)
    {
        $employee = auth()->user()->employee;

        $query = Attendance::where('employee_id', $employee->id);

        // Filter berdasarkan bulan
        if ($request->filled('month') && $request->filled('year')) {
            $month = $request->month;
            $year = $request->year;
            $query->whereMonth('date', $month)->whereYear('date', $year);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(20);
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        return view('employee.attendance.history', [
            'attendances' => $attendances,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
        ]);
    }

    /**
     * Rekap absensi bulanan (dengan chart & PDF export)
     */
    public function monthlySummary(Request $request)
    {
        $employee = auth()->user()->employee;

        // Default ke bulan ini
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Ambil semua absensi bulan ini
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        // Hitung statistik
        $stats = $this->calculateAttendanceStats($attendances, $startDate, $endDate);

        // Data untuk chart
        $chartData = $this->prepareChartData($attendances, $startDate, $endDate);

        return view('employee.attendance.monthly-summary', [
            'employee' => $employee,
            'month' => $month,
            'year' => $year,
            'attendances' => $attendances,
            'stats' => $stats,
            'chartData' => $chartData,
        ]);
    }

    /**
     * Export rekap absensi ke PDF
     */
    public function exportMonthlySummary(Request $request)
    {
        $employee = auth()->user()->employee;
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        $stats = $this->calculateAttendanceStats($attendances, $startDate, $endDate);

        $pdf = Pdf::loadView('employee.attendance.monthly-summary-pdf', [
            'employee' => $employee,
            'month' => $month,
            'year' => $year,
            'attendances' => $attendances,
            'stats' => $stats,
        ])->setPaper('a4', 'landscape');

        $filename = "Rekap-Absensi-{$employee->employee_code}-" .
                    Carbon::createFromDate($year, $month, 1)->format('M-Y') . ".pdf";

        return $pdf->download($filename);
    }

    /**
     * Hitung statistik absensi
     */
    private function calculateAttendanceStats($attendances, $startDate, $endDate)
    {
        $onTime = $attendances->where('status', 'on_time')->count();
        $late = $attendances->where('status', 'late')->count();
        $absent = $attendances->where('status', 'absent')->count();

        // Hitung hari kerja (Senin-Sabtu di periode tersebut)
        $workingDays = 0;
        $current = $startDate->copy();
        while ($current <= $endDate) {
            if (in_array($current->dayOfWeek, [1, 2, 3, 4, 5, 6])) {
                $workingDays++;
            }
            $current->addDay();
        }

        // Hitung overtime
        $totalOvertime = 0;
        foreach ($attendances as $att) {
            if ($att->overtime) {
                $totalOvertime += $att->overtime->hours ?? 0;
            }
        }

        return [
            'on_time' => $onTime,
            'late' => $late,
            'absent' => $absent,
            'total_present' => $onTime + $late,
            'working_days' => $workingDays,
            'attendance_percentage' => $workingDays > 0
                ? round((($onTime + $late) / $workingDays) * 100, 2)
                : 0,
            'total_overtime' => $totalOvertime,
            'period_name' => $startDate->format('F Y'),
        ];
    }

    /**
     * Siapkan data untuk chart
     */
    private function prepareChartData($attendances, $startDate, $endDate)
    {
        $data = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $dayAttendance = $attendances->first(function ($att) use ($current) {
                return $att->date->toDateString() === $current->toDateString();
            });

            $data[] = [
                'date' => $current->format('d M'),
                'day' => $current->format('l'),
                'status' => $dayAttendance?->status ?? 'no_data',
                'check_in' => $dayAttendance?->check_in,
                'check_out' => $dayAttendance?->check_out,
            ];

            $current->addDay();
        }

        return $data;
    }
}