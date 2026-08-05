<?php

namespace App\Http\Controllers\Admin;

use App\Models\Overtime;
use App\Services\PayrollService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OvertimeController extends Controller
{
    public function __construct(private PayrollService $payrollService) {}

    /**
     * Daftar overtime yang belum di-validate
     */
    public function index(Request $request)
    {
        $query = Overtime::with('employee', 'attendance');

        // Filter hanya yang belum di-validate
        if ($request->input('status') === 'pending') {
            $query->whereNull('validated_by');
        } elseif ($request->input('status') === 'validated') {
            $query->whereNotNull('validated_by');
        }

        // Filter berdasarkan tipe
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $overtimes = $query->orderBy('date', 'desc')->paginate(20);

        $types = ['office', 'admin_production', 'production_aka', 'production_export'];

        return view('admin.overtime.index', [
            'overtimes' => $overtimes,
            'types' => $types,
        ]);
    }

    /**
     * Form edit overtime
     */
    public function edit($id)
    {
        $overtime = Overtime::with('employee', 'attendance')->findOrFail($id);

        return view('admin.overtime.edit', [
            'overtime' => $overtime,
        ]);
    }

    /**
     * Update data overtime (sebelum di-validate)
     */
    public function update(Request $request, $id)
    {
        $overtime = Overtime::findOrFail($id);

        $validated = $request->validate([
            'hours' => 'nullable|numeric|min:0.5',
            'kg_amount' => 'nullable|numeric|min:0',
            'export_bonus_per_kg' => 'nullable|numeric|min:0',
        ]);

        $overtime->update($validated);

        return back()->with('success', 'Data overtime berhasil diupdate.');
    }

    /**
     * Validasi overtime (hitung amount & tandai sebagai validated)
     */
    public function validate(Request $request, $id)
    {
        $overtime = Overtime::findOrFail($id);

        $result = $this->payrollService->validateOvertime(
            $overtime,
            auth()->user()->id
        );

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->withErrors(['error' => $result['message']]);
    }
}