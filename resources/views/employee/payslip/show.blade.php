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
        <div class="flex items-center justify-between border-b border-gray-100 pb-2 mb-3">
            <h4 class="text-xs font-extrabold text-[#0a2219] uppercase tracking-wider">Pendapatan (Penghasilan)</h4>
            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">Bruto</span>
        </div>
        <div class="space-y-2.5 text-xs font-semibold text-gray-600">
            <div class="flex justify-between items-center">
                <div>
                    <span class="font-bold text-gray-800">Gaji Pokok</span>
                    <p class="text-[10px] text-gray-400">Gaji dasar karyawan</p>
                </div>
                <span class="text-gray-800 font-bold">Rp {{ number_format($payslip->base_salary, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-gray-700">BPJS JHTP</span>
                    <p class="text-[10px] text-gray-400">Jaminan Hari Tua (ditanggung perusahaan)</p>
                </div>
                <span class="text-gray-800 font-semibold">Rp {{ number_format($payslip->bpjs_jht_company, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-gray-700">BPJS JKK</span>
                    <p class="text-[10px] text-gray-400">Jaminan Kecelakaan Kerja (ditanggung perusahaan)</p>
                </div>
                <span class="text-gray-800 font-semibold">Rp {{ number_format($payslip->bpjs_jkk_income, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-gray-700">BPJS JKM</span>
                    <p class="text-[10px] text-gray-400">Jaminan Kematian (ditanggung perusahaan)</p>
                </div>
                <span class="text-gray-800 font-semibold">Rp {{ number_format($payslip->bpjs_jkm_income, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-gray-700">BPJS JKN P</span>
                    <p class="text-[10px] text-gray-400">Jaminan Kesehatan Nasional (ditanggung perusahaan)</p>
                </div>
                <span class="text-gray-800 font-semibold">Rp {{ number_format($payslip->bpjs_jkn_company, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-gray-700">JP Perusahaan</span>
                    <p class="text-[10px] text-gray-400">Jaminan Pensiun dari perusahaan</p>
                </div>
                <span class="text-gray-800 font-semibold">Rp {{ number_format($payslip->jp_company_income, 0, ',', '.') }}</span>
            </div>
            @if (($payslip->overtime_total ?? 0) > 0)
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-gray-700">Lembur / Overtime</span>
                        <p class="text-[10px] text-gray-400">Insentif lembur tervalidasi</p>
                    </div>
                    <span class="text-gray-800 font-bold">Rp {{ number_format($payslip->overtime_total, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between items-center pt-2.5 border-t border-dashed border-gray-200 font-extrabold text-gray-800 text-xs">
                <span class="text-[#0a2219]">Penghasilan Total</span>
                <span class="text-[#0a2219] font-black">Rp {{ number_format($payslip->total_income, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Deductions Section -->
        <div class="flex items-center justify-between border-b border-gray-100 pb-2 mb-3 mt-7">
            <h4 class="text-xs font-extrabold text-red-700 uppercase tracking-wider">Potongan</h4>
            <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-md">Deductions</span>
        </div>
        <div class="space-y-2.5 text-xs font-semibold text-gray-500">
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-gray-700">BPJS JHTP</span>
                    <p class="text-[10px] text-gray-400">Potongan Jaminan Hari Tua (porsi perusahaan)</p>
                </div>
                <span class="text-red-500 font-semibold">- Rp {{ number_format($payslip->bpjs_jht_company_deduct ?? $payslip->bpjs_jht_company, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-gray-700">BPJS JHTTK</span>
                    <p class="text-[10px] text-gray-400">Jaminan Hari Tua Tenaga Kerja (porsi karyawan)</p>
                </div>
                <span class="text-red-500 font-semibold">- Rp {{ number_format($payslip->bpjs_jht_employee, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-gray-700">BPJS JKK</span>
                    <p class="text-[10px] text-gray-400">Jaminan Kecelakaan Kerja</p>
                </div>
                <span class="text-red-500 font-semibold">- Rp {{ number_format($payslip->bpjs_jkk_deduct, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-gray-700">BPJS JKM</span>
                    <p class="text-[10px] text-gray-400">Jaminan Kematian</p>
                </div>
                <span class="text-red-500 font-semibold">- Rp {{ number_format($payslip->bpjs_jkm_deduct, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-gray-700">BPJS JKN P</span>
                    <p class="text-[10px] text-gray-400">Jaminan Kesehatan Nasional</p>
                </div>
                <span class="text-red-500 font-semibold">- Rp {{ number_format($payslip->bpjs_jkn_company_deduct, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-gray-700">JP Perusahaan</span>
                    <p class="text-[10px] text-gray-400">Jaminan Pensiun (porsi perusahaan)</p>
                </div>
                <span class="text-red-500 font-semibold">- Rp {{ number_format($payslip->jp_company_deduct, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-gray-700">JP Tenaga Kerja</span>
                    <p class="text-[10px] text-gray-400">Jaminan Pensiun (porsi karyawan)</p>
                </div>
                <span class="text-red-500 font-semibold">- Rp {{ number_format($payslip->jp_employee, 0, ',', '.') }}</span>
            </div>
            @if (($payslip->pot_bpjs ?? 0) > 0)
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-gray-700">Pot. BPJS</span>
                        <p class="text-[10px] text-gray-400">Potongan BPJS tambahan</p>
                    </div>
                    <span class="text-red-500 font-semibold">- Rp {{ number_format($payslip->pot_bpjs, 0, ',', '.') }}</span>
                </div>
            @endif
            @if (($payslip->pot_pesantren ?? 0) > 0)
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-gray-700">Pot. Pesantren</span>
                        <p class="text-[10px] text-gray-400">Potongan iuran/infak pesantren</p>
                    </div>
                    <span class="text-red-500 font-semibold">- Rp {{ number_format($payslip->pot_pesantren, 0, ',', '.') }}</span>
                </div>
            @endif
            @if (($payslip->absent_deduction ?? 0) > 0)
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-gray-700">Potongan Alpa</span>
                        <p class="text-[10px] text-gray-400">Alpa: {{ $payslip->absent_count ?? 0 }} hari</p>
                    </div>
                    <span class="text-red-500 font-semibold">- Rp {{ number_format($payslip->absent_deduction, 0, ',', '.') }}</span>
                </div>
            @endif
            @if (($payslip->other_deduction ?? 0) > 0)
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-gray-700">Potongan Lainnya</span>
                        <p class="text-[10px] text-gray-400">Penyesuaian administratif</p>
                    </div>
                    <span class="text-red-500 font-semibold">- Rp {{ number_format($payslip->other_deduction, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between items-center pt-2.5 border-t border-dashed border-gray-200 font-extrabold text-red-600 text-xs">
                <span>Jumlah Potongan</span>
                <span class="font-black">- Rp {{ number_format($payslip->total_deduction, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Grand Total Net (Take Home Pay) -->
        <div class="mt-8 p-5 bg-[#0a2219] text-white border border-[#153a2b] rounded-xl flex justify-between items-center shadow-md">
            <div>
                <span class="font-extrabold text-[10px] text-[#d4af37] uppercase tracking-widest block">Gaji Bersih Diterima</span>
                <span class="text-[10px] text-gray-300">Take Home Pay (THP)</span>
            </div>
            <span class="font-black text-xl text-white">Rp {{ number_format($payslip->net_salary, 0, ',', '.') }}</span>
        </div>
        
        <p class="text-center text-[10px] text-gray-400 font-semibold mt-6 italic">
            Slip gaji digital ini diterbitkan otomatis secara resmi oleh sistem absensi PT. Indobismar. 
            Apabila terdapat selisih perhitungan, hubungi departemen HRD/Payroll.
        </p>
    </div>
</div>
@endsection
