<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\FieldAssignment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FieldAssignmentController extends Controller
{
    /**
     * Daftar semua jadwal dinas luar
     */
    public function index(Request $request)
    {
        $query = FieldAssignment::with('employee', 'assignedBy', 'attendance');

        // Filter per tanggal
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            // Default: tampilkan mulai hari ini ke depan + 7 hari lalu
            $query->where('date', '>=', now()->subDays(7)->toDateString());
        }

        // Filter per karyawan
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $assignments = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->paginate(20);

        $employees = Employee::orderBy('name')->get();

        return view('admin.field-assignments.index', [
            'assignments' => $assignments,
            'employees'   => $employees,
            'filterDate'  => $request->date,
            'filterEmp'   => $request->employee_id,
        ]);
    }

    /**
     * Form buat jadwal dinas luar baru
     */
    public function create()
    {
        $employees = Employee::orderBy('name')->get();

        return view('admin.field-assignments.create', [
            'employees' => $employees,
        ]);
    }

    /**
     * Simpan satu atau banyak penugasan dinas luar
     * (bisa multi-select karyawan untuk 1 tanggal + lokasi yang sama)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_ids'  => 'required|array|min:1',
            'employee_ids.*'=> 'required|exists:employees,id',
            'date'          => 'required|date|after_or_equal:today',
            'location_name' => 'required|string|max:255',
            'location_lat'  => 'nullable|numeric|between:-90,90',
            'location_lng'  => 'nullable|numeric|between:-180,180',
            'notes'         => 'nullable|string|max:1000',
        ]);

        try {
            $created = 0;
            foreach ($validated['employee_ids'] as $empId) {
                // Cek apakah sudah ada assignment untuk karyawan ini di tanggal yang sama
                $existing = FieldAssignment::where('employee_id', $empId)
                    ->whereDate('date', $validated['date'])
                    ->first();

                if ($existing) {
                    // Update jika sudah ada
                    $existing->update([
                        'location_name' => $validated['location_name'],
                        'location_lat'  => $validated['location_lat'] ?? null,
                        'location_lng'  => $validated['location_lng'] ?? null,
                        'notes'         => $validated['notes'] ?? null,
                        'assigned_by'   => auth()->id(),
                    ]);
                } else {
                    FieldAssignment::create([
                        'employee_id'   => $empId,
                        'date'          => $validated['date'],
                        'location_name' => $validated['location_name'],
                        'location_lat'  => $validated['location_lat'] ?? null,
                        'location_lng'  => $validated['location_lng'] ?? null,
                        'notes'         => $validated['notes'] ?? null,
                        'assigned_by'   => auth()->id(),
                    ]);
                    $created++;
                }
            }

            $msg = $created > 0
                ? "Berhasil membuat {$created} jadwal dinas luar untuk tanggal " . Carbon::parse($validated['date'])->translatedFormat('d M Y') . "."
                : "Jadwal dinas luar diperbarui.";

            return redirect()->route('admin.field-assignments.index')
                ->with('success', $msg);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Batalkan / hapus jadwal dinas luar
     */
    public function destroy($id)
    {
        try {
            $assignment = FieldAssignment::findOrFail($id);

            // Jangan izinkan batalkan jika karyawan sudah absen via dinas luar ini
            if ($assignment->attendance && $assignment->attendance->is_field_assignment) {
                return back()->withErrors(['error' => 'Tidak dapat dibatalkan. Karyawan sudah absen menggunakan penugasan dinas luar ini.']);
            }

            $empName = $assignment->employee->name ?? 'Karyawan';
            $date    = $assignment->date->translatedFormat('d M Y');
            $assignment->delete();

            return back()->with('success', "Penugasan dinas luar {$empName} tanggal {$date} berhasil dibatalkan.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
