<?php

namespace App\Http\Controllers\Employee;

use App\Models\Overtime;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OvertimeController extends Controller
{
    public function __construct(private AttendanceService $attendanceService) {}

    /**
     * Daftar riwayat pengajuan lembur milik karyawan
     */
    public function index()
    {
        $employee = auth()->user()->employee;

        $overtimes = Overtime::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('employee.overtime.index', [
            'overtimes' => $overtimes,
        ]);
    }

    /**
     * Form pengajuan lembur baru
     */
    public function create()
    {
        $employee = auth()->user()->employee;

        // Penentuan default tipe lembur berdasarkan divisi & jabatan
        $defaultType = $this->attendanceService->determineOvertimeType($employee);

        return view('employee.overtime.create', [
            'employee'    => $employee,
            'defaultType' => $defaultType,
        ]);
    }

    /**
     * Store pengajuan lembur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'       => 'required|date',
            'type'       => 'required|in:office,admin_production,production_aka,production_export',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'reason'     => 'required|string|max:500',
            'kg_amount'  => 'nullable|numeric|min:0',
        ]);

        try {
            $employee = auth()->user()->employee;

            // Hitung estimasi durasi lembur (dalam jam)
            $start = Carbon::parse($validated['date'] . ' ' . $validated['start_time']);
            $end   = Carbon::parse($validated['date'] . ' ' . $validated['end_time']);
            $hours = round($end->diffInMinutes($start) / 60, 2);

            // Batas maksimal jam per tipe lembur
            $maxHours = match ($validated['type']) {
                'office'           => 1.0,
                'admin_production' => 2.0,
                'production_aka'   => 3.0,
                'production_export'=> 3.0,
                default            => 1.0,
            };

            if ($hours > $maxHours) {
                return back()->withInput()->withErrors([
                    'end_time' => "Durasi lembur untuk tipe ini maksimal {$maxHours} jam. Durasi yang Anda ajukan: {$hours} jam.",
                ]);
            }

            // Buat pengajuan lembur
            Overtime::create([
                'employee_id' => $employee->id,
                'date'        => $validated['date'],
                'type'        => $validated['type'],
                'start_time'  => $validated['start_time'],
                'end_time'    => $validated['end_time'],
                'hours'       => $hours,
                'reason'      => $validated['reason'],
                'kg_amount'   => $validated['type'] === 'production_export' ? ($validated['kg_amount'] ?? 0) : null,
                'status'      => 'pending',
            ]);

            return redirect()->route('employee.overtime.index')
                ->with('success', 'Pengajuan lembur berhasil dikirim. Menunggu persetujuan HRD.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Batalkan pengajuan lembur (hanya jika pending)
     */
    public function destroy($id)
    {
        $employee = auth()->user()->employee;

        $overtime = Overtime::where('employee_id', $employee->id)->findOrFail($id);

        if ($overtime->status !== 'pending') {
            return back()->withErrors(['error' => 'Hanya pengajuan lembur yang masih bertatus pending yang dapat dibatalkan.']);
        }

        try {
            $overtime->delete();

            return redirect()->route('employee.overtime.index')
                ->with('success', 'Pengajuan lembur berhasil dibatalkan.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
