@extends('layouts.admin')

@section('title', 'Pengaturan Penggajian')
@section('page-title', 'Pengaturan Komponen Penggajian')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header Info --}}
    <div class="bg-gradient-to-br from-[#0a2219] to-[#153a2b] rounded-2xl p-6 text-white">
        <h2 class="text-base font-extrabold uppercase tracking-wider mb-1">⚙️ Konfigurasi BPJS & Potongan Penggajian</h2>
        <p class="text-xs text-gray-300 font-semibold leading-relaxed">
            Atur persentase dan nominal semua komponen BPJS serta potongan penggajian PT. Indobismar di sini.
            Nilai yang disimpan akan digunakan secara otomatis pada saat <strong class="text-[#d4af37]">Generate Payroll</strong> berikutnya.
        </p>
        <div class="mt-3 bg-[#d4af37]/10 border border-[#d4af37]/30 rounded-xl px-4 py-3">
            <p class="text-[11px] text-[#d4af37] font-bold">
                ⚠️ Konfirmasi persentase BPJS dan nominal Pot. Pesantren dengan HR PT. Indobismar sebelum melakukan generate payroll untuk pertama kali.
            </p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="font-bold text-sm">{{ session('success') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl">
            <p class="font-bold text-sm mb-1">Terdapat kesalahan:</p>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.payroll.update') }}">
        @csrf

        @foreach ($groups as $groupKey => $groupLabel)
            @php
                $groupSettings = $settings->where('group', $groupKey);
            @endphp
            @if ($groupSettings->count() > 0)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">
                {{-- Group Header --}}
                <div class="bg-[#f0f4f2] border-b border-gray-100 px-6 py-4">
                    <h3 class="text-xs font-extrabold text-[#0a2219] uppercase tracking-wider">{{ $groupLabel }}</h3>
                    @if ($groupKey === 'bpjs')
                        <p class="text-[10px] text-gray-500 font-semibold mt-0.5">
                            Komponen BPJS Ketenagakerjaan & BPJS Kesehatan. Nilai dalam <strong>persen (%)</strong> dari gaji pokok.
                        </p>
                    @elseif ($groupKey === 'deduction')
                        <p class="text-[10px] text-gray-500 font-semibold mt-0.5">
                            Potongan dengan nilai tetap per bulan (dalam <strong>Rupiah</strong>). Isi 0 jika tidak berlaku.
                        </p>
                    @endif
                </div>

                {{-- Settings List --}}
                <div class="divide-y divide-gray-50">
                    @foreach ($groupSettings as $setting)
                        <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-800">{{ $setting->label }}</p>
                                @if ($setting->description)
                                    <p class="text-[11px] text-gray-500 font-semibold mt-0.5 leading-relaxed max-w-xl">{{ $setting->description }}</p>
                                @endif
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1">
                                    Key: <span class="font-mono">{{ $setting->key }}</span>
                                </p>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                {{-- Active Toggle --}}
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="settings[{{ $setting->id }}][is_active]" value="1"
                                           {{ $setting->is_active ? 'checked' : '' }}
                                           class="w-4 h-4 rounded border-gray-300 text-[#0a2219] focus:ring-[#d4af37]">
                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Aktif</span>
                                </label>

                                {{-- Value Input --}}
                                <div class="relative w-36">
                                    <input type="number"
                                           name="settings[{{ $setting->id }}][value]"
                                           value="{{ old('settings.' . $setting->id . '.value', $setting->value) }}"
                                           step="{{ $setting->type === 'percentage' ? '0.01' : '1000' }}"
                                           min="0"
                                           class="w-full px-3 py-2.5 pr-10 border border-gray-300 rounded-xl text-sm font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition text-right"
                                           placeholder="0">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-extrabold text-gray-400">
                                        {{ $setting->type === 'percentage' ? '%' : 'Rp' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach

        {{-- Tabel Ringkasan Perhitungan --}}
        <div class="bg-[#f0f7f3] border border-[#d2dfd8] rounded-2xl p-6 mb-5">
            <h3 class="text-xs font-extrabold text-[#0a2219] uppercase tracking-widest mb-4">Panduan Rumus Perhitungan</h3>
            <div class="space-y-2 text-xs font-semibold text-gray-700">
                <div class="flex items-start gap-3 p-3 bg-white rounded-xl border border-[#d2dfd8]">
                    <span class="text-[#d4af37] font-extrabold text-base leading-none mt-0.5">💰</span>
                    <div>
                        <p class="font-extrabold text-[#0a2219]">Potongan Alpa (Auto-kalkulasi)</p>
                        <p class="text-gray-500 mt-0.5 font-mono text-[11px]">Gaji Pokok ÷ Jumlah Hari Kerja × Jumlah Hari Alpa</p>
                        <p class="text-gray-400 mt-0.5">Dihitung otomatis oleh sistem saat generate payroll — tidak perlu diisi manual.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 bg-white rounded-xl border border-[#d2dfd8]">
                    <span class="text-[#d4af37] font-extrabold text-base leading-none mt-0.5">🏢</span>
                    <div>
                        <p class="font-extrabold text-[#0a2219]">Komponen Porsi Perusahaan (Transparan)</p>
                        <p class="text-gray-500 mt-0.5">BPJS JHT/JKK/JKM/JKN/JP porsi perusahaan ditampilkan di kolom Pendapatan slip gaji untuk transparansi, lalu langsung dipotongan balik — nilainya nol terhadap gaji bersih karyawan.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 rounded-xl border border-gray-200 text-gray-600 font-bold text-xs uppercase tracking-wider hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit" class="bg-[#0a2219] hover:bg-[#123b2c] text-white px-8 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition shadow-sm border border-transparent focus:ring-2 focus:ring-[#d4af37]">
                💾 Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection
