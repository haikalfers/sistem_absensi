@extends('layouts.admin')

@section('title', 'Pengaturan Lokasi')
@section('page-title', 'Pengaturan Lokasi & GPS Kantor')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header Banner --}}
    <div class="bg-gradient-to-br from-[#0a2219] to-[#153a2b] rounded-2xl p-6 text-white shadow-sm">
        <div class="flex items-center space-x-3 mb-2">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-xl">
                📍
            </div>
            <div>
                <h2 class="text-base font-extrabold uppercase tracking-wider text-white">Lokasi &amp; GPS Kantor PT. Indobismar</h2>
                <p class="text-xs text-gray-300 font-semibold mt-0.5">Titik acuan koordinat tunggal untuk validasi radius absensi karyawan melalui PWA</p>
            </div>
        </div>
        <div class="mt-4 bg-[#d4af37]/10 border border-[#d4af37]/30 rounded-xl px-4 py-3 text-[11px] text-[#d4af37] font-bold leading-relaxed">
            ℹ️ Sistem absensi PT. Indobismar menggunakan 1 lokasi kantor acuan. Karyawan yang melakukan presensi mandiri (bukan dinas luar) wajib berada di dalam radius lingkaran lokasi ini.
        </div>
    </div>

    {{-- Flash & Error Messages --}}
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

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="font-bold text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.locations.update') }}" class="space-y-6">
        @csrf
        
        @foreach($locations as $index => $location)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                {{-- Card Header --}}
                <div class="bg-[#f0f4f2] border-b border-gray-100 px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <h3 class="text-xs font-extrabold text-[#0a2219] uppercase tracking-wider">
                            {{ $locations->count() === 1 ? 'Kantor Acuan Presensi' : 'Kantor #' . ($index + 1) }}
                        </h3>
                        @if($loop->first)
                            <span class="text-[9px] font-bold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full uppercase tracking-wider">Utama (Aktif)</span>
                        @endif
                    </div>

                    <div class="flex items-center space-x-3">
                        @if($location->latitude && $location->longitude)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $location->latitude }},{{ $location->longitude }}" 
                               target="_blank" 
                               class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition duration-150">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Buka di Google Maps
                            </a>
                        @endif

                        @if($locations->count() > 1)
                            <button type="button" 
                                    onclick="if(confirm('Hapus lokasi kantor ini agar hanya tersisa 1 kantor Indobismar?')) { document.getElementById('delete-loc-{{ $location->id }}').submit(); }"
                                    class="text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition duration-150">
                                Hapus
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Card Body Inputs --}}
                <div class="p-6 space-y-4">
                    {{-- Nama Kantor --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Kantor / Lokasi</label>
                        <input type="text" 
                               name="locations[{{ $location->id }}][name]" 
                               value="{{ old("locations.{$location->id}.name", $location->name) }}" 
                               required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition">
                        <p class="text-[10px] text-gray-400 mt-1 font-medium">Contoh: Kantor PT. Indobismar</p>
                    </div>

                    {{-- Koordinat & Radius --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Latitude</label>
                            <input type="number" 
                                   step="any" 
                                   name="locations[{{ $location->id }}][latitude]" 
                                   value="{{ old("locations.{$location->id}.latitude", $location->latitude) }}" 
                                   required 
                                   placeholder="-7.325061"
                                   class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition font-mono">
                            <p class="text-[10px] text-gray-400 mt-1">Garis Lintang</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Longitude</label>
                            <input type="number" 
                                   step="any" 
                                   name="locations[{{ $location->id }}][longitude]" 
                                   value="{{ old("locations.{$location->id}.longitude", $location->longitude) }}" 
                                   required 
                                   placeholder="112.711200"
                                   class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition font-mono">
                            <p class="text-[10px] text-gray-400 mt-1">Garis Bujur</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Radius Absensi (Meter)</label>
                            <div class="relative">
                                <input type="number" 
                                       name="locations[{{ $location->id }}][radius_meters]" 
                                       value="{{ old("locations.{$location->id}.radius_meters", $location->radius_meters) }}" 
                                       required 
                                       min="1" 
                                       placeholder="30"
                                       class="w-full px-3.5 py-2.5 pr-10 border border-gray-300 rounded-xl text-sm font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition text-right">
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">m</span>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1">Toleransi jarak presensi (default: 30–50 m)</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Submit Button --}}
        <div class="flex justify-end pt-2">
            <button type="submit" class="bg-[#0a2219] hover:bg-[#123b2c] text-white px-8 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 shadow-md border border-transparent">
                💾 Simpan Pengaturan Lokasi
            </button>
        </div>
    </form>

    {{-- Hidden Delete Forms for extra locations --}}
    @if($locations->count() > 1)
        @foreach($locations as $location)
            <form id="delete-loc-{{ $location->id }}" 
                  method="POST" 
                  action="{{ route('admin.settings.locations.destroy', $location->id) }}" 
                  class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    @endif

</div>
@endsection
