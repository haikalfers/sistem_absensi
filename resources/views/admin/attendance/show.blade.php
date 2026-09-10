@extends('layouts.admin')

@section('title', 'Detail Absensi')
@section('page-title', 'Detail Absensi Karyawan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Back Button --}}
    <div>
        <a href="{{ route('admin.attendance.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-[#0a2219] uppercase tracking-wider transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Absensi
        </a>
    </div>

    {{-- ===== HEADER CARD ===== --}}
    <div class="bg-gradient-to-br from-[#0a2219] to-[#153a2b] rounded-2xl p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[10px] font-bold text-[#d4af37] uppercase tracking-widest mb-1">Record Absensi</p>
            <h1 class="text-xl font-extrabold">{{ $attendance->employee->name }}</h1>
            <p class="text-xs text-gray-300 font-semibold mt-1">{{ $attendance->employee->employee_code }} &mdash; {{ $attendance->employee->position }}</p>
        </div>
        <div class="text-right">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tanggal</p>
            <p class="text-lg font-extrabold text-[#d4af37]">{{ $attendance->date->translatedFormat('d F Y') }}</p>
            <p class="text-[10px] text-gray-400 font-semibold mt-1">{{ $attendance->date->translatedFormat('l') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===== KOLOM KIRI: Info Absensi ===== --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Status & Jam --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">Informasi Absensi</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-[#f0f7f3] rounded-xl p-4">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Jam Masuk</p>
                        <p class="text-2xl font-extrabold text-[#0a2219]">{{ $attendance->check_in?->format('H:i') ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Jam Keluar</p>
                        <p class="text-2xl font-extrabold text-gray-700">{{ $attendance->check_out?->format('H:i') ?? '—' }}</p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <p class="font-bold text-gray-400 uppercase tracking-wider text-[10px] mb-1">Status Ketepatan</p>
                        @if ($attendance->status === 'on_time')
                            <span class="inline-flex items-center px-2.5 py-1 bg-emerald-500/10 text-emerald-700 font-extrabold rounded-lg border border-emerald-500/20 uppercase tracking-wider">Tepat Waktu</span>
                        @elseif ($attendance->status === 'late')
                            <span class="inline-flex items-center px-2.5 py-1 bg-amber-500/10 text-amber-700 font-extrabold rounded-lg border border-amber-500/20 uppercase tracking-wider">Terlambat</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 bg-red-500/10 text-red-700 font-extrabold rounded-lg border border-red-500/20 uppercase tracking-wider">Mangkir / Alpha</span>
                        @endif
                    </div>
                    <div>
                        <p class="font-bold text-gray-400 uppercase tracking-wider text-[10px] mb-1">Metode Absensi</p>
                        <span class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-700 font-extrabold rounded-lg border border-gray-200 uppercase tracking-wider text-xs">
                            {{ $attendance->source === 'pwa' ? '📱 GPS PWA' : '🖐 Fingerprint' }}
                        </span>
                    </div>
                </div>

                @if ($attendance->notes)
                    <div class="mt-4 bg-gray-50 rounded-xl p-4">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Catatan</p>
                        <p class="text-sm text-gray-700 font-semibold">{{ $attendance->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Koordinat GPS --}}
            @if ($attendance->check_in_lat && $attendance->check_in_lng)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">Koordinat GPS Check-in</h3>
                <div class="grid grid-cols-2 gap-4 text-xs font-semibold text-gray-700 mb-4">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Latitude</p>
                        <p class="font-mono font-bold">{{ $attendance->check_in_lat }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Longitude</p>
                        <p class="font-mono font-bold">{{ $attendance->check_in_lng }}</p>
                    </div>
                    @if ($attendance->gps_accuracy)
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Akurasi GPS</p>
                            <p class="font-mono font-bold {{ $attendance->gps_accuracy < 3 ? 'text-amber-600' : 'text-gray-700' }}">
                                {{ number_format($attendance->gps_accuracy, 1) }} meter
                                @if ($attendance->gps_accuracy < 3)
                                    <span class="text-[10px] text-amber-600 font-extrabold">(Terlalu Sempurna)</span>
                                @endif
                            </p>
                        </div>
                    @endif
                    @if ($attendance->ip_address)
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">IP Address</p>
                            <p class="font-mono font-bold">{{ $attendance->ip_address }}</p>
                        </div>
                    @endif
                </div>
                <a href="https://www.google.com/maps/search/?api=1&query={{ $attendance->check_in_lat }},{{ $attendance->check_in_lng }}" target="_blank"
                   class="inline-flex items-center gap-2 text-xs font-bold text-[#0a2219] hover:text-[#d4af37] transition uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Buka di Google Maps
                </a>
            </div>
            @endif

            {{-- ===== FAKE GPS DETECTION RESULT ===== --}}
            @if ($attendance->source === 'pwa')
            <div class="bg-white rounded-2xl border shadow-sm p-6 {{ $attendance->is_suspect ? 'border-amber-200' : 'border-gray-100' }}">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest">Hasil Deteksi Fake GPS</h3>
                    @if ($attendance->is_suspect)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-100 text-amber-700 text-[10px] font-extrabold rounded-lg border border-amber-300 uppercase tracking-wider">
                            ⚠️ Mencurigakan — Perlu Verifikasi HR
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-extrabold rounded-lg border border-emerald-200 uppercase tracking-wider">
                            ✓ GPS Normal
                        </span>
                    @endif
                </div>

                @if ($attendance->fake_gps_flags && count($attendance->fake_gps_flags) > 0)
                    <div class="space-y-3">
                        @foreach ($attendance->fake_gps_flags as $flag)
                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <span class="text-amber-500 font-extrabold text-lg leading-none">⚠</span>
                                    <div>
                                        <p class="text-[10px] font-extrabold text-amber-800 uppercase tracking-wider">
                                            Lapis {{ $flag['layer'] ?? '?' }} — {{ $flag['code'] ?? 'FLAG' }}
                                        </p>
                                        <p class="text-xs text-amber-700 font-semibold mt-1">{{ $flag['message'] ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 font-semibold">Tidak ada flag yang terdeteksi. GPS dianggap valid.</p>
                @endif

                @if ($attendance->is_suspect)
                    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <p class="text-[10px] font-extrabold text-blue-800 uppercase tracking-wider mb-1">Panduan Verifikasi HR</p>
                        <p class="text-xs text-blue-700 font-semibold">
                            Sistem <strong>tidak menolak</strong> absensi ini secara otomatis. HR bertindak sebagai pemegang keputusan akhir.
                            Verifikasi dengan membandingkan foto selfie, menghubungi karyawan, atau memeriksa presensi fisik.
                        </p>
                    </div>
                @endif
            </div>
            @endif

        </div>

        {{-- ===== KOLOM KANAN: Selfie ===== --}}
        <div class="space-y-5">

            {{-- Selfie Photo --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">Foto Selfie Kehadiran</h3>

                @if ($attendance->selfie_photo)
                    <div class="relative">
                        <img src="{{ asset('storage/' . $attendance->selfie_photo) }}"
                             alt="Selfie {{ $attendance->employee->name }}"
                             class="w-full rounded-xl object-cover aspect-square border-2 border-[#e7f0ec]"
                             onerror="this.closest('.relative').innerHTML='<div class=\'w-full aspect-square bg-gray-100 rounded-xl flex flex-col items-center justify-center\'><p class=\'text-xs text-gray-400 font-bold\'>Foto tidak dapat dimuat</p></div>'">
                    </div>

                    {{-- Expiry Info --}}
                    <div class="mt-4 bg-gray-50 rounded-xl p-3">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Batas Penyimpanan</p>
                        @if ($attendance->selfie_expires_at)
                            @if ($attendance->selfie_expires_at->isPast())
                                <p class="text-xs text-red-600 font-bold">Foto sudah seharusnya dihapus ({{ $attendance->selfie_expires_at->translatedFormat('d F Y') }})</p>
                            @else
                                <p class="text-xs text-gray-700 font-semibold">
                                    Dihapus otomatis pada <strong>{{ $attendance->selfie_expires_at->translatedFormat('d F Y') }}</strong>
                                    <span class="text-gray-400">({{ $attendance->selfie_expires_at->diffForHumans() }})</span>
                                </p>
                            @endif
                        @else
                            <p class="text-xs text-gray-500 font-semibold">Informasi tidak tersedia</p>
                        @endif
                    </div>
                @else
                    <div class="w-full aspect-square bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center">
                        <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider text-center">
                            @if ($attendance->source === 'fingerprint')
                                Absensi via Fingerprint<br>tidak ada selfie
                            @else
                                Foto tidak tersedia<br>atau sudah dihapus otomatis
                            @endif
                        </p>
                    </div>
                    @if ($attendance->source === 'pwa' && !$attendance->selfie_photo)
                        <p class="text-[10px] text-amber-600 font-semibold mt-3 text-center">
                            Foto sudah melewati masa simpan 7 hari dan dihapus otomatis oleh sistem. Data absensi tetap valid.
                        </p>
                    @endif
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
