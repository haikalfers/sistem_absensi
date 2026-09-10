<?php

namespace App\Http\Controllers\Admin;

use App\Models\PayrollSetting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PayrollSettingController extends Controller
{
    /**
     * Halaman pengaturan penggajian (BPJS, potongan, dll.)
     */
    public function index()
    {
        $settings = PayrollSetting::orderBy('group')->orderBy('id')->get();

        $groups = [
            'bpjs'      => 'Komponen BPJS',
            'deduction' => 'Potongan Lain-Lain',
            'allowance' => 'Tunjangan',
        ];

        return view('admin.settings.payroll', [
            'settings' => $settings,
            'groups'   => $groups,
        ]);
    }

    /**
     * Update nilai pengaturan penggajian
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings.*.value'     => 'required|numeric|min:0',
            'settings.*.is_active' => 'nullable|boolean',
        ]);

        try {
            foreach ($request->input('settings', []) as $id => $data) {
                $setting = PayrollSetting::findOrFail($id);
                $setting->update([
                    'value'     => (float) $data['value'],
                    'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : true,
                ]);
            }

            return back()->with('success', 'Pengaturan penggajian berhasil disimpan. Nilai baru akan digunakan pada generate payroll berikutnya.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan pengaturan: ' . $e->getMessage()]);
        }
    }
}
