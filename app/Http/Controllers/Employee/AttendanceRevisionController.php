<?php

namespace App\Http\Controllers\Employee;

use App\Models\{Attendance, AttendanceRevision};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AttendanceRevisionController extends Controller
{
    /**
     * Daftar pengajuan presensi ulang milik karyawan yang sedang login
     */
    public function index()
    {
        $employee = auth()->user()->employee;

        $revisions = AttendanceRevision::forEmployee($employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('employee.attendance-revisions.index', [
            'revisions' => $revisions,
        ]);
    }

    /**
     * Form pengajuan presensi ulang baru
     */
    public function create()
    {
        $employee = auth()->user()->employee;

        // Daftar 7 hari terakhir (tidak termasuk hari ini)
        $availableDates = collect();
        for ($i = 1; $i <= 7; $i++) {
            $date = Carbon::today()->subDays($i);
            // Skip hari Minggu
            if ($date->dayOfWeek === Carbon::SUNDAY) {
                continue;
            }
            $availableDates->push($date);
        }

        // Cek tanggal mana yang sudah punya pengajuan pending
        $pendingDates = AttendanceRevision::forEmployee($employee->id)
            ->where('status', 'pending')
            ->pluck('revision_date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        // Absensi yang sudah ada untuk ditampilkan sebagai referensi
        $existingAttendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [
                Carbon::today()->subDays(7)->toDateString(),
                Carbon::yesterday()->toDateString(),
            ])
            ->get()
            ->keyBy(fn($a) => Carbon::parse($a->date)->toDateString());

        return view('employee.attendance-revisions.create', [
            'availableDates'      => $availableDates,
            'pendingDates'        => $pendingDates,
            'existingAttendances' => $existingAttendances,
        ]);
    }

    /**
     * Simpan pengajuan presensi ulang
     */
    public function store(Request $request)
    {
        $request->validate([
            'revision_date'       => ['required', 'date', 'before:today', 'after_or_equal:' . Carbon::today()->subDays(7)->toDateString()],
            'requested_check_in'  => ['nullable', 'date_format:H:i'],
            'requested_check_out' => ['nullable', 'date_format:H:i', 'after:requested_check_in'],
            'reason'              => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'revision_date.required'       => 'Tanggal revisi wajib dipilih.',
            'revision_date.before'         => 'Tanggal revisi harus hari sebelumnya.',
            'revision_date.after_or_equal' => 'Revisi hanya bisa dilakukan untuk 7 hari terakhir.',
            'requested_check_out.after'    => 'Jam keluar harus setelah jam masuk.',
            'reason.required'              => 'Alasan pengajuan wajib diisi.',
            'reason.min'                   => 'Alasan minimal 10 karakter.',
        ]);

        $employee = auth()->user()->employee;

        // Cek: max 1 pengajuan pending per hari
        $existingPending = AttendanceRevision::forEmployee($employee->id)
            ->where('revision_date', $request->revision_date)
            ->where('status', 'pending')
            ->exists();

        if ($existingPending) {
            return back()
                ->withInput()
                ->withErrors(['revision_date' => 'Sudah ada pengajuan pending untuk tanggal ini. Tunggu hingga diproses admin.']);
        }

        // Cari attendance yang sudah ada (jika ada)
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $request->revision_date)
            ->first();

        AttendanceRevision::create([
            'employee_id'         => $employee->id,
            'attendance_id'       => $attendance?->id,
            'revision_date'       => $request->revision_date,
            'requested_check_in'  => $request->requested_check_in,
            'requested_check_out' => $request->requested_check_out,
            'reason'              => $request->reason,
            'status'              => 'pending',
        ]);

        return redirect()
            ->route('employee.attendance-revisions.index')
            ->with('success', 'Pengajuan presensi ulang berhasil dikirim. Menunggu persetujuan admin.');
    }

    /**
     * Batalkan pengajuan (hanya jika masih pending)
     */
    public function destroy($id)
    {
        $employee = auth()->user()->employee;

        $revision = AttendanceRevision::forEmployee($employee->id)
            ->findOrFail($id);

        if (!$revision->isPending()) {
            return back()->with('error', 'Pengajuan yang sudah diproses tidak dapat dibatalkan.');
        }

        $revision->delete();

        return back()->with('success', 'Pengajuan presensi ulang berhasil dibatalkan.');
    }
}
