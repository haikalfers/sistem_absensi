<?php

namespace App\Http\Controllers\Admin;

use App\Models\{Payroll, PayrollDetail, Employee};
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function __construct(private PayrollService $payrollService) {}

    /**
     * Daftar payroll
     */
    public function index()
    {
        $payrolls = Payroll::with('createdBy')
            ->orderBy('period_start', 'desc')
            ->paginate(15);

        return view('admin.payrolls.index', [
            'payrolls' => $payrolls,
        ]);
    }

    /**
     * Form create payroll baru
     */
    public function create()
    {
        // Suggest periode berdasarkan payroll terakhir
        $lastPayroll = Payroll::latest('period_end')->first();

        $suggestedStart = $lastPayroll
            ? $lastPayroll->period_end->addDays(1)
            : Carbon::now()->startOfMonth();

        $suggestedEnd = $suggestedStart->copy()->addDays(29);

        return view('admin.payrolls.create', [
            'suggestedStart' => $suggestedStart,
            'suggestedEnd' => $suggestedEnd,
        ]);
    }

    /**
     * Store payroll baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'period_name' => 'required|string|max:100',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
        ]);

        try {
            $payroll = Payroll::create([
                'period_name' => $validated['period_name'],
                'period_start' => $validated['period_start'],
                'period_end' => $validated['period_end'],
                'status' => 'draft',
                'created_by' => auth()->user()->id,
            ]);

            return redirect()->route('admin.payrolls.show', $payroll)
                ->with('success', 'Payroll berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Tampilkan detail payroll
     */
    public function show($id)
    {
        $payroll = Payroll::with('details.employee', 'createdBy')->findOrFail($id);

        $totalGross = $payroll->details->sum(function ($detail) {
            return $detail->base_salary + $detail->meal_allowance + $detail->overtime_total;
        });

        $totalNet = $payroll->details->sum('net_salary');
        $totalDeduction = $totalGross - $totalNet;

        return view('admin.payrolls.show', [
            'payroll' => $payroll,
            'totalGross' => $totalGross,
            'totalDeduction' => $totalDeduction,
            'totalNet' => $totalNet,
        ]);
    }

    /**
     * Generate payroll (hitung gaji semua karyawan)
     */
    public function generate($id)
    {
        $payroll = Payroll::findOrFail($id);

        if ($payroll->status === 'finalized') {
            return back()->withErrors(['error' => 'Payroll sudah di-finalize, tidak bisa di-generate lagi.']);
        }

        $result = $this->payrollService->generatePayroll($payroll);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->withErrors(['error' => $result['message']]);
    }

    /**
     * Kembalikan status payroll ke draft (bisa di-generate ulang)
     */
    public function revertToDraft($id)
    {
        $payroll = Payroll::findOrFail($id);

        if ($payroll->status !== 'finalized') {
            return back()->withErrors(['error' => 'Hanya payroll dengan status finalized yang dapat dikembalikan ke draft.']);
        }

        try {
            $payroll->update(['status' => 'draft']);
            return back()->with('success', 'Status payroll berhasil dikembalikan ke draft. Anda sekarang dapat men-generate ulang payroll ini.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Form edit payroll detail (bonus, dll)
     */
    public function edit($id)
    {
        $payroll = Payroll::with('details.employee')->findOrFail($id);

        return view('admin.payrolls.edit', [
            'payroll' => $payroll,
        ]);
    }

    /**
     * Update payroll detail (edit bonus & potongan lainnya)
     */
    public function update(Request $request, $id)
    {
        $payroll = Payroll::findOrFail($id);

        $request->validate([
            'details.*.kpi_bonus'       => 'nullable|numeric|min:0',
            'details.*.other_deduction' => 'nullable|numeric|min:0',
        ]);

        try {
            foreach ($request->input('details', []) as $detailId => $data) {
                $detail = PayrollDetail::findOrFail($detailId);

                $kpiBonus      = (float) ($data['kpi_bonus'] ?? $detail->kpi_bonus ?? 0);
                $otherDeduct   = (float) ($data['other_deduction'] ?? $detail->other_deduction ?? 0);

                // Hitung ulang net salary
                $gross  = (float) $detail->base_salary
                        + (float) $detail->meal_allowance
                        + (float) $detail->overtime_total
                        + $kpiBonus;

                $deduct = (float) $detail->pph21_deduction
                        + (float) $detail->bpjs_tk_deduction
                        + (float) $detail->bpjs_kes_deduction
                        + $otherDeduct;

                $detail->update([
                    'kpi_bonus'       => $kpiBonus,
                    'other_deduction' => $otherDeduct,
                    'net_salary'      => $gross - $deduct,
                ]);
            }

            return back()->with('success', 'Bonus & potongan berhasil diperbarui. Net salary telah dihitung ulang.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Export payroll ke PDF (semua karyawan)
     */
    public function exportPdf($id)
    {
        $payroll = Payroll::with('details.employee')->findOrFail($id);

        $pdf = Pdf::loadView('admin.payrolls.export-pdf', [
            'payroll' => $payroll,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('payroll-' . str_replace(' ', '-', $payroll->period_name) . '.pdf');
    }

    /**
     * Hapus payroll yang masih draft
     */
    public function destroy($id)
    {
        $payroll = Payroll::findOrFail($id);

        if ($payroll->status !== 'draft') {
            return back()->withErrors(['error' => 'Hanya payroll dengan status draft yang dapat dihapus.']);
        }

        try {
            $payroll->delete();
            return redirect()->route('admin.payrolls.index')
                ->with('success', 'Payroll berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}