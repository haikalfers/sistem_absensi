@extends('layouts.admin')

@section('title', 'Edit Payroll')
@section('page-title', 'Edit Bonus & Potongan: ' . $payroll->period_name)

@section('content')
    <!-- Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-b border-gray-100 pb-5">
        <div>
            <span class="text-[10px] font-extrabold text-[#d4af37] tracking-widest uppercase">Penyesuaian Insentif</span>
            <h2 class="text-xl font-extrabold text-[#0a2219] mt-1">Edit Bonus & Potongan</h2>
        </div>
        <a href="{{ route('admin.payrolls.show', $payroll) }}" class="inline-flex items-center text-xs font-bold text-gray-400 hover:text-[#0a2219] uppercase tracking-wider transition duration-150">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali ke Detail
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
        <form method="POST" action="{{ route('admin.payrolls.update', $payroll) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] border-collapse">
                    <thead>
                        <tr class="bg-[#f0f4f2] border-b border-gray-100">
                            <th class="px-6 py-4.5 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Karyawan</th>
                            <th class="px-6 py-4.5 text-right text-xs font-bold text-[#0a2219] uppercase tracking-wider">Gaji Pokok</th>
                            <th class="px-6 py-4.5 text-right text-xs font-bold text-[#0a2219] uppercase tracking-wider">Bonus KPI (Rp)</th>
                            <th class="px-6 py-4.5 text-right text-xs font-bold text-[#0a2219] uppercase tracking-wider">Potongan Lain (Rp)</th>
                            <th class="px-6 py-4.5 text-right text-xs font-bold text-[#0a2219] uppercase tracking-wider">Net Salary</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($payroll->details as $detail)
                            @php
                                $gross = $detail->base_salary + $detail->meal_allowance + $detail->overtime_total + $detail->kpi_bonus;
                                $deduction = $detail->pph21_deduction + $detail->bpjs_tk_deduction + $detail->bpjs_kes_deduction + $detail->other_deduction;
                                $net = $gross - $deduction;
                            @endphp
                            <tr class="hover:bg-[#fcfdfc] transition duration-150">
                                <!-- Employee Name -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-gray-100 text-[#0a2219] rounded-xl flex items-center justify-center font-bold text-xs border border-gray-200">
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
                                
                                <!-- KPI Bonus Input -->
                                <td class="px-6 py-4 text-right">
                                    <input type="hidden" name="details[{{ $detail->id }}][id]" value="{{ $detail->id }}">
                                    <input type="number" name="details[{{ $detail->id }}][kpi_bonus]" value="{{ $detail->kpi_bonus }}" 
                                           class="w-36 px-3 py-2 border border-gray-300 rounded-xl text-sm text-right focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200" min="0" step="1000">
                                </td>
                                
                                <!-- Other Deduction Input -->
                                <td class="px-6 py-4 text-right">
                                    <input type="number" name="details[{{ $detail->id }}][other_deduction]" value="{{ $detail->other_deduction }}" 
                                           class="w-36 px-3 py-2 border border-gray-300 rounded-xl text-sm text-right focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200" min="0" step="1000">
                                </td>
                                
                                <!-- Net Salary Preview -->
                                <td class="px-6 py-4 text-right text-sm font-bold text-emerald-600">
                                    Rp {{ number_format($net, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 font-medium">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <span class="text-gray-800 font-bold mb-1">Data Gaji Belum Dihitung (Digenerate)</span>
                                        <span class="text-xs">Silakan kembali ke daftar payroll dan klik tombol "Generate" terlebih dahulu untuk memproses data gaji karyawan sebelum Anda bisa mengedit bonus atau potongan.</span>
                                        <a href="{{ route('admin.payrolls.index') }}" class="mt-4 bg-[#0a2219] hover:bg-[#123b2c] text-white px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 inline-block shadow-md shadow-emerald-950/10">Kembali ke Daftar Payroll</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-6 border-t border-gray-50 max-w-md ml-auto">
                <button type="submit" class="flex-1 bg-[#0a2219] hover:bg-[#123b2c] text-white py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 shadow-md">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.payrolls.show', $payroll) }}" class="flex-1 bg-[#f0f4f2] text-[#0a2219] border border-[#d2dfd8] py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection