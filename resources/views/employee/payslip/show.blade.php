@extends('layouts.employee')

@section('title', 'Detail Slip Gaji')

@section('content')
<div class="mb-5 flex items-center justify-between border-b border-gray-100 pb-4">
    <div class="flex items-center gap-2">
        <a href="{{ route('employee.payslip.index') }}" class="text-gray-400 hover:text-[#0a2219] transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h2 class="text-base font-extrabold text-[#0a2219] uppercase tracking-wider">Rincian Slip Gaji</h2>
            <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">Detail breakdown gaji Anda</p>
        </div>
    </div>
    <a href="{{ route('employee.payslip.download', $payslip->id) }}" class="bg-red-600 hover:bg-red-700 text-white px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border border-transparent shadow-sm">
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <span>PDF</span>
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="p-6 border-b border-gray-50 bg-[#e7f0ec]/30">
        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Periode Slip Pembayaran</p>
        <h3 class="font-extrabold text-lg text-[#0a2219] mt-0.5">{{ $payslip->payroll->period_name }}</h3>
    </div>

    <div class="p-6">
        <!-- Earnings Section -->
        <h4 class="text-xs font-extrabold text-[#0a2219] uppercase tracking-wider border-b border-gray-100 pb-2 mb-3">Penerimaan (Earnings)</h4>
        <div class="space-y-3.5 text-xs font-semibold text-gray-600">
            <div class="flex justify-between">
                <span>Gaji Pokok Utama</span>
                <span class="text-gray-800 font-bold">Rp {{ number_format($payslip->base_salary, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Tunjangan Uang Makan</span>
                <span class="text-gray-800 font-bold">Rp {{ number_format($payslip->meal_allowance, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Lembur / Overtime</span>
                <span class="text-gray-800 font-bold">Rp {{ number_format($payslip->overtime_total, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Bonus KPI Khusus</span>
                <span class="text-gray-800 font-bold">Rp 0</span>
            </div>
            <div class="flex justify-between pt-2.5 border-t border-dashed border-gray-200 font-extrabold text-gray-800 text-xs">
                <span>Total Penghasilan Gross</span>
                <span class="text-[#0a2219]">Rp {{ number_format($payslip->base_salary + $payslip->meal_allowance + $payslip->overtime_total, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Deductions Section -->
        <h4 class="text-xs font-extrabold text-red-700 uppercase tracking-wider border-b border-gray-100 pb-2 mb-3 mt-7">Potongan (Deductions)</h4>
        <div class="space-y-3.5 text-xs font-semibold text-gray-500">
            <div class="flex justify-between">
                <span>BPJS Ketenagakerjaan (2%)</span>
                <span class="text-red-500 font-bold">- Rp {{ number_format($payslip->bpjs_tk_deduction, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span>BPJS Kesehatan (1%)</span>
                <span class="text-red-500 font-bold">- Rp {{ number_format($payslip->bpjs_kes_deduction, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Pajak Penghasilan PPh Pasal 21</span>
                <span class="text-red-500 font-bold">- Rp {{ number_format($payslip->pph21_deduction, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between pt-2.5 border-t border-dashed border-gray-200 font-extrabold text-red-600 text-xs">
                <span>Total Potongan</span>
                <span>- Rp {{ number_format($payslip->bpjs_tk_deduction + $payslip->bpjs_kes_deduction + $payslip->pph21_deduction, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Grand Total Net (Take Home Pay) -->
        <div class="mt-8 p-5 bg-[#0a2219] text-white border border-[#153a2b] rounded-xl flex justify-between items-center shadow-md">
            <span class="font-extrabold text-[10px] text-[#d4af37] uppercase tracking-widest">Gaji Bersih Diterima</span>
            <span class="font-black text-lg text-white">Rp {{ number_format($payslip->net_salary, 0, ',', '.') }}</span>
        </div>
        
        <p class="text-center text-[10px] text-gray-400 font-semibold mt-6 italic">
            Slip gaji digital ini diterbitkan otomatis secara resmi oleh sistem absensi PT. Triliun Anugrah Nusantara. 
            Apabila terdapat selisih perhitungan, hubungi departemen HRD/Payroll.
        </p>
    </div>
</div>
@endsection
