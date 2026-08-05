@extends('layouts.admin')

@section('title', 'Buat Payroll Baru')
@section('page-title', 'Buat Payroll Baru')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Action Bar -->
        <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-5">
            <div>
                <span class="text-[10px] font-extrabold text-[#d4af37] tracking-widest uppercase">Pembuatan Periode</span>
                <h2 class="text-xl font-extrabold text-[#0a2219] mt-1">Form Payroll Baru</h2>
            </div>
            <a href="{{ route('admin.payrolls.index') }}" class="inline-flex items-center text-xs font-bold text-gray-400 hover:text-[#0a2219] uppercase tracking-wider transition duration-150">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Batal
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
            <form method="POST" action="{{ route('admin.payrolls.store') }}" class="space-y-6">
                @csrf

                <!-- Period Name -->
                <div>
                    <label for="period_name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Periode *</label>
                    <input type="text" name="period_name" id="period_name" value="{{ old('period_name') }}" 
                           placeholder="e.g., April 2025"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200"
                           required>
                    @error('period_name') <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Period Start -->
                    <div>
                        <label for="period_start" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tanggal Mulai *</label>
                        <input type="date" name="period_start" id="period_start" value="{{ old('period_start', $suggestedStart->toDateString()) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200"
                               required>
                        <p class="text-[10px] text-gray-400 font-semibold mt-1">Default: {{ $suggestedStart->format('d M Y') }}</p>
                        @error('period_start') <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <!-- Period End -->
                    <div>
                        <label for="period_end" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tanggal Selesai *</label>
                        <input type="date" name="period_end" id="period_end" value="{{ old('period_end', $suggestedEnd->toDateString()) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200"
                               required>
                        <p class="text-[10px] text-gray-400 font-semibold mt-1">Default: {{ $suggestedEnd->format('d M Y') }}</p>
                        @error('period_end') <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-[#faf3e0] border border-[#f3e7c4] text-[#8a6d1c] p-5 rounded-2xl">
                    <h4 class="font-bold text-xs uppercase tracking-wider mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-[#8a6d1c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Informasi Pembuatan
                    </h4>
                    <ul class="text-xs space-y-1 leading-relaxed font-semibold">
                        <li>✓ Periode baru akan dibuat dalam status <strong class="text-[#0a2219]">Draft</strong>.</li>
                        <li>✓ Anda dapat menyesuaikan nominal bonus KPI dan potongan lainnya secara individual sebelum divalidasi.</li>
                        <li>✓ Gunakan tombol "Generate" untuk memicu perhitungan otomatis (Gaji pokok, tunjangan, lembur, pajak PPh21, BPJS).</li>
                    </ul>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-4 border-t border-gray-50">
                    <button type="submit" class="flex-1 bg-[#0a2219] hover:bg-[#123b2c] text-white py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 shadow-md">
                        Buat Payroll
                    </button>
                    <a href="{{ route('admin.payrolls.index') }}" class="flex-1 bg-[#f0f4f2] text-[#0a2219] border border-[#d2dfd8] py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection