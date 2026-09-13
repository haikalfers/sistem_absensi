<?php

namespace App\Http\Controllers\Admin;

use App\Models\{CompanyLocation, WorkSchedule, Employee};
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SettingController extends Controller
{
    /**
     * View settings lokasi kantor
     */
    public function locations()
    {
        $locations = CompanyLocation::all();

        return view('admin.settings.locations', [
            'locations' => $locations,
        ]);
    }

    /**
     * Update lokasi kantor
     */
    public function updateLocations(Request $request)
    {
        $validated = $request->validate([
            'locations.*.name' => 'required|string',
            'locations.*.latitude' => 'required|numeric|between:-90,90',
            'locations.*.longitude' => 'required|numeric|between:-180,180',
            'locations.*.radius_meters' => 'required|numeric|min:1',
        ]);

        try {
            foreach ($request->input('locations', []) as $locId => $data) {
                $location = CompanyLocation::findOrFail($locId);
                $location->update($data);
            }

            return back()->with('success', 'Lokasi kantor berhasil diupdate.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * View settings jadwal kerja
     */
    public function schedules()
    {
        $schedules = WorkSchedule::orderByRaw('division IS NOT NULL ASC')->orderBy('name')->get();
        $daysOfWeek = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];

        // Ambil daftar divisi unik dari karyawan + list divisi populer
        $existingDivisions = Employee::whereNotNull('division')
            ->where('division', '!=', '')
            ->distinct()
            ->pluck('division')
            ->toArray();

        $presetDivisions = ['Marketing', 'Produksi', 'Sales', 'IT', 'HR', 'Finance', 'Operasional', 'Logistik'];
        $divisions = array_unique(array_merge($presetDivisions, $existingDivisions));
        sort($divisions);

        return view('admin.settings.schedules', [
            'schedules'  => $schedules,
            'daysOfWeek' => $daysOfWeek,
            'divisions'  => $divisions,
        ]);
    }

    /**
     * Update jadwal kerja
     */
    public function updateSchedules(Request $request)
    {
        $validated = $request->validate([
            'schedules.*.name' => 'required|string',
            'schedules.*.division' => 'nullable|string',
            'schedules.*.working_days' => 'required|array',
            'schedules.*.check_in_time' => 'required|date_format:H:i',
            'schedules.*.check_out_time' => 'required|date_format:H:i',
        ]);

        try {
            foreach ($request->input('schedules', []) as $schedId => $data) {
                if (isset($data['working_days']) && is_array($data['working_days'])) {
                    $data['working_days'] = array_map('intval', $data['working_days']);
                }
                if (isset($data['division']) && trim($data['division']) === '') {
                    $data['division'] = null;
                }
                $schedule = WorkSchedule::findOrFail($schedId);
                $schedule->update($data);
            }

            return back()->with('success', 'Jadwal kerja berhasil diupdate.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Tambah jadwal kerja baru (khusus divisi atau umum)
     */
    public function storeSchedule(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'division' => 'nullable|string|max:255',
            'working_days' => 'required|array',
            'check_in_time' => 'required|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i',
            'late_tolerance_minutes' => 'nullable|integer|min:0',
        ]);

        try {
            $workingDays = array_map('intval', $request->input('working_days', []));
            $division = $request->filled('division') ? trim($request->input('division')) : null;

            WorkSchedule::create([
                'name' => $request->input('name'),
                'division' => $division,
                'working_days' => $workingDays,
                'check_in_time' => $request->input('check_in_time'),
                'check_out_time' => $request->input('check_out_time'),
                'late_tolerance_minutes' => $request->input('late_tolerance_minutes', 0) ?? 0,
            ]);

            return back()->with('success', 'Jadwal kerja baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Hapus jadwal kerja
     */
    public function destroySchedule($id)
    {
        try {
            $schedule = WorkSchedule::findOrFail($id);
            $schedule->delete();

            return back()->with('success', 'Jadwal kerja berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}