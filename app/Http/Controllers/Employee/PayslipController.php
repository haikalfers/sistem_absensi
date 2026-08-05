<?php

namespace App\Http\Controllers\Employee;

use App\Models\PayrollDetail;
use Illuminate\Routing\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class PayslipController extends Controller
{
    /**
     * Daftar payslip karyawan
     */
    public function index()
    {
        $employee = auth()->user()->employee;

        $payslips = PayrollDetail::where('employee_id', $employee->id)
            ->with('payroll')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('employee.payslip.index', [
            'payslips' => $payslips,
        ]);
    }

    /**
     * Tampilkan detail payslip
     */
    public function show($id)
    {
        $employee = auth()->user()->employee;

        $payslip = PayrollDetail::where('employee_id', $employee->id)
            ->with('payroll')
            ->findOrFail($id);

        return view('employee.payslip.show', [
            'payslip' => $payslip,
        ]);
    }

    /**
     * Download payslip PDF
     */
    public function download($id)
    {
        $employee = auth()->user()->employee;

        $detail = PayrollDetail::where('employee_id', $employee->id)
            ->with('payroll', 'employee')
            ->findOrFail($id);

        $pdf = Pdf::loadView('employee.payslip.template', [
            'detail' => $detail,
        ])->setPaper('a4', 'portrait');

        $filename = "payslip-{$detail->employee->employee_code}-{$detail->payroll->period_name}.pdf";

        return $pdf->download($filename);
    }
}