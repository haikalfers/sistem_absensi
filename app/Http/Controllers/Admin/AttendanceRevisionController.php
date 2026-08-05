<?php

namespace App\Http\Controllers\Admin;

use App\Models\{Attendance, AttendanceRevision, Employee};
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AttendanceRevisionController extends Controller
{
    public function __construct(private AttendanceService $attendanceService) {}

    /**
     * Daftar semua pengajuan presensi ulang
     */
    public function index(Request $request)
    {
        $query = AttendanceRevision::with(['employee', 'attendance']);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter karyawan
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $revisions  = $query->orderBy('created_at', 'desc')->paginate(20);
        $employees  = Employee::orderBy('name')->get();

        return view('admin.attendance-revisions.index', [
            'revisions' => $revisions,
            'employees' => $employees,
        ]);
    }

    /**
     * Detail pengajuan + compare original vs requested
     */
    public function show($id)
    {
        $revision = AttendanceRevision::with(['employee', 'attendance', 'reviewedBy'])
            ->findOrFail($id);

        return view('admin.attendance-revisions.show', [
            'revision' => $revision,
        ]);
    }

    /**
     * Setujui pengajuan → otomatis update/create attendance record
     */
    public function approve(Request $request, $id)
    {
        $revision = AttendanceRevision::with('employee')->findOrFail($id);

        if (!$revision->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        // Tentukan status (on_time / late) berdasarkan work schedule
        // Default: on_time jika tidak ada jadwal spesifik
        $checkInTime = $revision->requested_check_in;
        $newStatus   = 'on_time';

        // Cari atau buat attendance record
        $attendance = Attendance::where('employee_id', $revision->employee_id)
            ->whereDate('date', $revision->revision_date)
            ->first();

        if ($attendance) {
            // Update record yang sudah ada
            $attendance->update([
                'check_in'  => $revision->requested_check_in  ?? $attendance->check_in,
                'check_out' => $revision->requested_check_out ?? $attendance->check_out,
                'status'    => $checkInTime ? $newStatus : $attendance->status,
                'notes'     => $attendance->notes . ' [Direvisi via pengajuan presensi ulang #' . $revision->id . ']',
            ]);
        } else {
            // Buat record baru
            $attendance = Attendance::create([
                'employee_id' => $revision->employee_id,
                'date'        => $revision->revision_date,
                'check_in'    => $revision->requested_check_in,
                'check_out'   => $revision->requested_check_out,
                'source'      => 'pwa',
                'status'      => $checkInTime ? $newStatus : 'absent',
                'notes'       => 'Dibuat via pengajuan presensi ulang #' . $revision->id,
            ]);
        }

        // Cek dan Hitung Lembur Otomatis jika kedua jam (masuk & keluar) sudah terisi
        if ($attendance->check_in && $attendance->check_out) {
            $dateString = $revision->revision_date->toDateString();
            $checkInTime = Carbon::parse($dateString . ' ' . $attendance->check_in);
            $checkOutTime = Carbon::parse($dateString . ' ' . $attendance->check_out);
            
            $overtimeHours = $this->attendanceService->calculateOvertimeHours($checkInTime, $checkOutTime);
            
            if ($overtimeHours > 0) {
                \App\Models\Overtime::updateOrCreate(
                    [
                        'attendance_id' => $attendance->id,
                        'employee_id' => $revision->employee_id,
                        'date' => $revision->revision_date,
                    ],
                    [
                        'type' => $this->attendanceService->determineOvertimeType($revision->employee),
                        'hours' => $overtimeHours,
                    ]
                );
            } else {
                // Hapus overtime jika sebelumnya ada tapi ternyata setelah direvisi jadi tidak lembur
                \App\Models\Overtime::where('attendance_id', $attendance->id)->delete();
            }
        }

        // Update revision
        $revision->update([
            'attendance_id' => $attendance->id,
            'status'        => 'approved',
            'reviewed_by'   => auth()->id(),
            'reviewed_at'   => now(),
        ]);

        return redirect()
            ->route('admin.attendance-revisions.index')
            ->with('success', "Pengajuan presensi ulang {$revision->employee->name} tanggal {$revision->revision_date->format('d M Y')} telah disetujui.");
    }

    /**
     * Tolak pengajuan dengan feedback
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'review_notes' => ['required', 'string', 'min:5', 'max:300'],
        ], [
            'review_notes.required' => 'Alasan penolakan wajib diisi.',
            'review_notes.min'      => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $revision = AttendanceRevision::with('employee')->findOrFail($id);

        if (!$revision->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $revision->update([
            'status'       => 'rejected',
            'reviewed_by'  => auth()->id(),
            'review_notes' => $request->review_notes,
            'reviewed_at'  => now(),
        ]);

        return redirect()
            ->route('admin.attendance-revisions.index')
            ->with('success', "Pengajuan presensi ulang {$revision->employee->name} telah ditolak.");
    }
}
