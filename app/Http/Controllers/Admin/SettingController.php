<?php

namespace App\Http\Controllers\Admin;

use App\Models\{CompanyLocation, WorkSchedule};
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
        $schedules = WorkSchedule::all();
        $daysOfWeek = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];

        return view('admin.settings.schedules', [
            'schedules' => $schedules,
            'daysOfWeek' => $daysOfWeek,
        ]);
    }

    /**
     * Update jadwal kerja
     */
    public function updateSchedules(Request $request)
    {
        $validated = $request->validate([
            'schedules.*.name' => 'required|string',
            'schedules.*.working_days' => 'required|array',
            'schedules.*.check_in_time' => 'required|date_format:H:i',
            'schedules.*.check_out_time' => 'required|date_format:H:i',
        ]);

        try {
            foreach ($request->input('schedules', []) as $schedId => $data) {
                $schedule = WorkSchedule::findOrFail($schedId);
                $schedule->update($data);
            }

            return back()->with('success', 'Jadwal kerja berhasil diupdate.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}