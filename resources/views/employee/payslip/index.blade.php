@extends('layouts.employee')

@section('title', 'Slip Gaji Saya')

@section('content')
<div class="mb-5 border-b border-gray-100 pb-4">
    <h2 class="text-base font-extrabold text-[#0a2219] uppercase tracking-wider">Slip Gaji Bulanan</h2>
    <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">Daftar riwayat slip gaji resmi Anda</p>
</div>

<!-- List Payslip -->
<div class="space-y-4">
    @forelse($payslips as $payslip)
        <a href="{{ route('employee.payslip.show', $payslip->id) }}" class="block bg-white rounded-2xl shadow-sm p-5 border border-gray-100 border-l-4 border-l-[#d4af37] hover:shadow transition duration-200">
            <div class="flex justify-between items-center mb-2.5">
                <span class="font-extrabold text-sm text-gray-800">{{ $payslip->payroll->period_name }}</span>
                <span class="inline-flex items-center px-2 py-0.5 bg-[#e7f0ec] text-[#0a2219] text-[10px] font-extrabold rounded-lg border border-[#d2dfd8] uppercase tracking-wider">
                    Tersedia
                </span>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mt-3 text-xs font-bold text-gray-600 pt-2.5 border-t border-gray-50">
                <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                    <span class="text-[9px] text-gray-400 uppercase tracking-wider block mb-1">Gaji Pokok</span>
                    <span class="text-xs font-extrabold text-gray-700">Rp {{ number_format($payslip->base_salary, 0, ',', '.') }}</span>
                </div>
                <div class="bg-[#e7f0ec]/40 p-2.5 rounded-xl border border-[#d2dfd8]/50">
                    <span class="text-[9px] text-[#0a2219] uppercase tracking-wider block mb-1">Penerimaan Bersih</span>
                    <span class="text-xs font-extrabold text-emerald-700">Rp {{ number_format($payslip->net_salary, 0, ',', '.') }}</span>
                </div>
            </div>
            
            <div class="mt-3.5 text-[9px] text-gray-400 font-bold uppercase tracking-wider flex items-center justify-between pt-2">
                <span>Diterbitkan: {{ $payslip->created_at->format('d M Y') }}</span>
                <span class="text-[#0a2219] font-extrabold hover:text-[#d4af37] transition flex items-center">
                    Lihat Slip
                    <svg class="w-5 h-5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </span>
            </div>
        </a>
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center text-gray-500 font-medium">
            <svg class="w-12 h-12 text-gray-300 mb-3 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="text-xs text-gray-400 font-semibold">Belum ada data slip gaji yang tersedia.</p>
        </div>
    @endforelse
</div>

<div class="mt-5 pb-6">
    {{ $payslips->links() }}
</div>
@endsection
