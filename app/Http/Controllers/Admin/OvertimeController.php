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
     * Daftar pengajuan lembur karyawan (Pending / Approved / Rejected)
     */
    public function index(Request $request)
    {
        $query = Overtime::with('employee', 'attendance', 'validatedBy');

        // Filter status
        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter berdasarkan tipe
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $overtimes = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->paginate(20);

        $types = ['office', 'admin_production', 'production_aka', 'production_export'];

        return view('admin.overtime.index', [
            'overtimes'    => $overtimes,
            'types'        => $types,
            'activeStatus' => $status,
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
     * Update data overtime
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
     * Setujui / Approve pengajuan lembur oleh HRD
     */
    public function approve(Request $request, $id)
    {
        $overtime = Overtime::findOrFail($id);

        // Jika HRD menginput ulang jam/kg sebelum disetujui
        if ($request->filled('hours')) {
            $overtime->hours = (float) $request->input('hours');
        }
        if ($request->filled('kg_amount')) {
            $overtime->kg_amount = (float) $request->input('kg_amount');
        }

        $overtime->status = 'approved';
        $overtime->save();

        $result = $this->payrollService->validateOvertime(
            $overtime,
            auth()->user()->id
        );

        if ($result['success']) {
            return back()->with('success', 'Pengajuan lembur berhasil disetujui. ' . $result['message']);
        }

        return back()->withErrors(['error' => $result['message']]);
    }

    /**
     * Tolak / Reject pengajuan lembur oleh HRD
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $overtime = Overtime::findOrFail($id);
        $overtime->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->input('rejection_reason', 'Pengajuan lembur tidak disetujui oleh HRD.'),
            'validated_by'     => auth()->user()->id,
        ]);

        return back()->with('success', 'Pengajuan lembur berhasil ditolak.');
    }

    /**
     * Validasi overtime (legacy/alias untuk approve)
     */
    public function validate(Request $request, $id)
    {
        return $this->approve($request, $id);
    }
}