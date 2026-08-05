@extends('layouts.admin')

@section('title', 'Detail Payroll')
@section('page-title', 'Detail Payroll: ' . $payroll->period_name)

@section('content')
    <!-- Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-b border-gray-100 pb-5">
        <div>
            <span class="text-[10px] font-extrabold text-[#d4af37] tracking-widest uppercase">Laporan Penggajian</span>
            <h2 class="text-xl font-extrabold text-[#0a2219] mt-1">{{ $payroll->period_name }}</h2>
            <p class="text-xs text-gray-500 font-semibold mt-1">
                Periode Kerja: {{ $payroll->period_start->format('d M Y') }} s/d {{ $payroll->period_end->format('d M Y') }}
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.payrolls.index') }}" class="inline-flex items-center text-xs font-bold text-gray-400 hover:text-[#0a2219] uppercase tracking-wider transition duration-150">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali ke Daftar
            </a>
            
            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-extrabold border uppercase tracking-wider shadow-sm {{ $payroll->status === 'draft' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">
                {{ $payroll->status === 'draft' ? '✎ Draft' : '✓ Finalized' }}
            </span>
        </div>
    </div>

    <!-- Summary Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Karyawan -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition duration-200 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Karyawan</p>
                <p class="text-3xl font-extrabold text-[#0a2219] mt-2">{{ $payroll->details->count() }}</p>
                <p class="text-[10px] text-gray-500 font-semibold mt-1">Terhitung dalam periode</p>
            </div>
            <div class="w-12 h-12 bg-[#e7f0ec] rounded-xl flex items-center justify-center text-[#0a2219] border border-[#d2dfd8]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.386 11.386 0 0110.089 20.5a11.378 11.378 0 01-4.94-1.263v-.11a11.353 11.353 0 010-3.187m0 4.382v-.003c0-1.113.285-2.16.786-3.07M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm6.375 2.25a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zM13.5 8.25a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                </svg>
            </div>
        </div>

        <!-- Total Gaji Gross -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition duration-200 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Gaji Gross</p>
                <p class="text-2xl font-extrabold text-[#0a2219] mt-2">Rp {{ number_format($totalGross, 0, ',', '.') }}</p>
                <p class="text-[10px] text-emerald-600 font-semibold mt-1">Sebelum potongan pajak & BPJS</p>
            </div>
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 border border-emerald-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 16v1M17 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Total Gaji Net (Solid Dark Green Premium Card) -->
        <div class="bg-[#0a2219] rounded-2xl shadow-md p-6 hover:shadow-lg transition duration-200 flex items-center justify-between border border-[#153a2b]">
            <div>
                <p class="text-[10px] font-bold text-[#d4af37] uppercase tracking-wider">Total Gaji Net (Pengeluaran)</p>
                <p class="text-2xl font-extrabold text-white mt-2">Rp {{ number_format($totalNet, 0, ',', '.') }}</p>
                <p class="text-[10px] text-gray-300 font-semibold mt-1">Bersih ditransfer ke karyawan</p>
            </div>
            <div class="w-12 h-12 bg-[#123b2c] rounded-xl flex items-center justify-center text-[#d4af37] border border-[#1d523e]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Action Buttons Panel -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6 flex flex-wrap gap-4 items-center justify-between">
        <div class="flex items-center space-x-2 text-xs text-gray-500 font-semibold">
            <span>Operasi Payroll:</span>
        </div>
        
        <div class="flex flex-wrap gap-3">
            @if ($payroll->status === 'draft')
                <form method="POST" action="{{ route('admin.payrolls.generate', $payroll) }}" class="inline-block">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center bg-[#0a2219] hover:bg-[#123b2c] text-white px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 border border-transparent shadow-sm"
                            onclick="confirmAction(event, 'Generate payroll untuk semua karyawan? Ini akan menghitung: Gaji pokok, Tunjangan makan, Lembur, Potongan BPJS & PPh21', this);">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Generate Payroll
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.payrolls.revert', $payroll) }}" class="inline-block">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 border border-transparent shadow-sm"
                            onclick="confirmAction(event, 'Kembalikan ke Draft? Ini memungkinkan Anda men-generate ulang data gaji. Perhatikan bahwa edit manual bonus/potongan yang belum disimpan mungkin hilang saat generate ulang.', this);">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Kembalikan ke Draft
                    </button>
                </form>

                <a href="{{ route('admin.payrolls.export-pdf', $payroll) }}" class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download PDF Slip
                </a>
            @endif

            {{-- Tombol Edit Bonus/Potongan selalu tampil selama payroll sudah di-generate --}}
            @if ($payroll->details->count() > 0)
                <a href="{{ route('admin.payrolls.edit', $payroll) }}" class="inline-flex items-center justify-center bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-300 px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Bonus/Potongan
                </a>
            @endif
        </div>
    </div>

    <!-- Payroll Details Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px] border-collapse">
                <thead>
                    <tr class="bg-[#f0f4f2] border-b border-gray-100">
                        <th class="px-6 py-4.5 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Karyawan</th>
                        <th class="px-6 py-4.5 text-right text-xs font-bold text-[#0a2219] uppercase tracking-wider">Gaji Pokok</th>
                        <th class="px-6 py-4.5 text-right text-xs font-bold text-[#0a2219] uppercase tracking-wider">Tunj. Makan</th>
                        <th class="px-6 py-4.5 text-right text-xs font-bold text-[#0a2219] uppercase tracking-wider">Lembur</th>
                        <th class="px-6 py-4.5 text-right text-xs font-bold text-[#0a2219] uppercase tracking-wider">Gaji Gross</th>
                        <th class="px-6 py-4.5 text-right text-xs font-bold text-[#0a2219] uppercase tracking-wider">Potongan</th>
                        <th class="px-6 py-4.5 text-right text-xs font-bold text-[#0a2219] uppercase tracking-wider">Net Salary</th>
                        <th class="px-6 py-4.5 text-center text-xs font-bold text-[#0a2219] uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($payroll->details as $detail)
                        @php
                            $gross = $detail->base_salary + $detail->meal_allowance + $detail->overtime_total;
                            $deduction = $detail->pph21_deduction + $detail->bpjs_tk_deduction + $detail->bpjs_kes_deduction;
                        @endphp
                        <tr class="hover:bg-[#fcfdfc] transition duration-150">
                            <!-- Employee Name -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 bg-[#e7f0ec] text-[#0a2219] rounded-xl flex items-center justify-center font-bold text-sm border border-[#d2dfd8]">
                                        {{ substr($detail->employee->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">{{ $detail->employee->name }}</p>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $detail->employee->employee_code }}</p>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Base Salary -->
                            <td class="px-6 py-4 text-right text-sm font-semibold text-gray-700">
                                Rp {{ number_format($detail->base_salary, 0, ',', '.') }}
                            </td>
                            
                            <!-- Meal Allowance -->
                            <td class="px-6 py-4 text-right text-sm font-semibold text-gray-700">
                                Rp {{ number_format($detail->meal_allowance, 0, ',', '.') }}
                            </td>
                            
                            <!-- Overtime Total -->
                            <td class="px-6 py-4 text-right text-sm font-semibold text-gray-700">
                                Rp {{ number_format($detail->overtime_total, 0, ',', '.') }}
                            </td>
                            
                            <!-- Gross Salary -->
                            <td class="px-6 py-4 text-right text-sm font-bold text-[#0a2219]">
                                Rp {{ number_format($gross, 0, ',', '.') }}
                            </td>
                            
                            <!-- Deductions -->
                            <td class="px-6 py-4 text-right text-sm font-semibold text-red-500">
                                - Rp {{ number_format($deduction, 0, ',', '.') }}
                            </td>
                            
                            <!-- Net Salary -->
                            <td class="px-6 py-4 text-right text-sm font-bold text-emerald-600">
                                Rp {{ number_format($detail->net_salary, 0, ',', '.') }}
                            </td>
                            
                            <!-- Modal Button -->
                            <td class="px-6 py-4 text-center">
                                <button type="button" onclick="showDetail({{ $detail->id }})" class="inline-flex items-center text-xs font-bold text-[#0a2219] hover:text-[#d4af37] transition duration-150">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Rincian
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500 font-medium">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Belum ada detail payroll dihitung.</span>
                                    @if ($payroll->status === 'draft')
                                        <p class="text-xs text-gray-400 font-semibold mt-1">Silakan klik tombol "Generate Payroll" di atas untuk memproses data.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detail Modal (With Glassmorphic Overlay) -->
    <div id="detailModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center p-4 z-50 transition duration-300">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-2xl p-8 max-w-md w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300" id="detailContent">
            <!-- Loaded dynamically via JS -->
        </div>
    </div>

    <script>
        function showDetail(detailId) {
            const detail = @json($payroll->details).find(d => d.id === detailId);
            if (!detail) return;

            const gross = detail.base_salary + detail.meal_allowance + detail.overtime_total;
            const deduction = detail.pph21_deduction + detail.bpjs_tk_deduction + detail.bpjs_kes_deduction;

            const html = `
                <div class="flex items-center space-x-3 mb-6 border-b border-gray-100 pb-4">
                    <div class="w-10 h-10 bg-[#e7f0ec] text-[#0a2219] rounded-xl flex items-center justify-center font-bold text-sm border border-[#d2dfd8]">
                        ${detail.employee.name.charAt(0)}
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-gray-800 uppercase tracking-wider">${detail.employee.name}</h3>
                        <p class="text-[10px] font-bold text-[#d4af37] tracking-wider uppercase">${detail.employee.employee_code}</p>
                    </div>
                </div>

                <div class="space-y-3.5 text-xs font-semibold">
                    <div class="border-b border-gray-50 pb-2">
                        <span class="text-[9px] text-[#0a2219] font-bold uppercase tracking-widest block mb-2">Penghasilan (Gross)</span>
                        <div class="flex justify-between py-1 text-gray-600">
                            <span>Gaji Pokok</span>
                            <span class="text-gray-800 font-bold">Rp ${new Intl.NumberFormat('id-ID').format(detail.base_salary)}</span>
                        </div>
                        <div class="flex justify-between py-1 text-gray-600">
                            <span>Tunjangan Makan</span>
                            <span class="text-gray-800 font-bold">Rp ${new Intl.NumberFormat('id-ID').format(detail.meal_allowance)}</span>
                        </div>
                        <div class="flex justify-between py-1 text-gray-600">
                            <span>Nominal Lembur</span>
                            <span class="text-gray-800 font-bold">Rp ${new Intl.NumberFormat('id-ID').format(detail.overtime_total)}</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-dashed border-gray-200 font-extrabold text-gray-800">
                            <span>Total Gross</span>
                            <span class="text-[#0a2219]">Rp ${new Intl.NumberFormat('id-ID').format(gross)}</span>
                        </div>
                    </div>

                    <div class="border-b border-gray-50 pb-2 pt-1">
                        <span class="text-[9px] text-red-600 font-bold uppercase tracking-widest block mb-2">Potongan (Deductions)</span>
                        <div class="flex justify-between py-1 text-gray-500">
                            <span>PPh Pasal 21 (Pajak)</span>
                            <span class="text-red-500">Rp ${new Intl.NumberFormat('id-ID').format(detail.pph21_deduction)}</span>
                        </div>
                        <div class="flex justify-between py-1 text-gray-500">
                            <span>BPJS Ketenagakerjaan</span>
                            <span class="text-red-500">Rp ${new Intl.NumberFormat('id-ID').format(detail.bpjs_tk_deduction)}</span>
                        </div>
                        <div class="flex justify-between py-1 text-gray-500">
                            <span>BPJS Kesehatan</span>
                            <span class="text-red-500">Rp ${new Intl.NumberFormat('id-ID').format(detail.bpjs_kes_deduction)}</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-dashed border-gray-200 font-extrabold text-red-600">
                            <span>Total Potongan</span>
                            <span>Rp ${new Intl.NumberFormat('id-ID').format(deduction)}</span>
                        </div>
                    </div>

                    <div class="bg-[#faf3e0] p-4 rounded-xl border border-[#f3e7c4] mt-4">
                        <span class="text-[9px] text-[#8a6d1c] font-bold uppercase tracking-widest block">Gaji Bersih (Take Home Pay)</span>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-xs text-gray-600 font-bold">NET SALARY</span>
                            <span class="text-base font-extrabold text-emerald-700">Rp ${new Intl.NumberFormat('id-ID').format(detail.net_salary)}</span>
                        </div>
                    </div>
                </div>

                <button onclick="closeModal()" class="w-full mt-6 bg-[#0a2219] hover:bg-[#123b2c] text-white py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 border border-transparent shadow-sm">
                    Tutup Detail
                </button>
            `;

            document.getElementById('detailContent').innerHTML = html;
            document.getElementById('detailModal').classList.remove('hidden');
            document.getElementById('detailModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('detailModal').classList.remove('flex');
            document.getElementById('detailModal').classList.add('hidden');
        }

        // Close on clicking backdrop
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
@endsection