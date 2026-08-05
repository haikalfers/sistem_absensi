@extends('layouts.admin')

@section('title', 'Detail Pengajuan Presensi Ulang')
@section('page-title', 'Detail Pengajuan Presensi Ulang')

@section('content')
    {{-- Back Button + Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.attendance-revisions.index') }}"
           class="p-2 rounded-xl border border-gray-200 text-gray-500 hover:text-[#0a2219] hover:border-[#0a2219] hover:bg-[#f0f4f2] transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h3 class="text-base font-bold text-gray-800 uppercase tracking-wider">Detail Pengajuan #{{ $revision->id }}</h3>
            <p class="text-xs text-gray-400 font-semibold mt-0.5">Diajukan {{ $revision->created_at->diffForHumans() }} · {{ $revision->created_at->format('d M Y, H:i') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── LEFT: Info Karyawan + Status ────────────────────── --}}
        <div class="space-y-5">

            {{-- Kartu Karyawan --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Informasi Karyawan</p>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-[#e7f0ec] text-[#0a2219] rounded-2xl flex items-center justify-center font-extrabold text-lg border border-[#d2dfd8] flex-shrink-0">
                        {{ substr($revision->employee->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-extrabold text-gray-800">{{ $revision->employee->name }}</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">{{ $revision->employee->employee_code }}</p>
                        <p class="text-[10px] text-gray-500 mt-0.5">{{ $revision->employee->position ?? '-' }}</p>
                    </div>
                </div>
                <div class="border-t border-gray-50 pt-3 space-y-2">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-400 font-semibold">Tanggal Absensi</span>
                        <span class="font-extrabold text-gray-800">
                            {{ \Carbon\Carbon::parse($revision->revision_date)->translatedFormat('d M Y') }}
                        </span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-400 font-semibold">Hari</span>
                        <span class="font-bold text-gray-700">
                            {{ \Carbon\Carbon::parse($revision->revision_date)->translatedFormat('l') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Status Pengajuan --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Status Pengajuan</p>

                @if($revision->status === 'pending')
                    <div class="flex items-center gap-3 p-3 bg-amber-50 rounded-xl border border-amber-200">
                        <span class="w-3 h-3 bg-amber-400 rounded-full animate-pulse flex-shrink-0"></span>
                        <div>
                            <p class="text-sm font-extrabold text-amber-700">Menunggu Review</p>
                            <p class="text-[10px] text-amber-600 mt-0.5">Belum ada tindakan dari admin</p>
                        </div>
                    </div>
                @elseif($revision->status === 'approved')
                    <div class="flex items-center gap-3 p-3 bg-emerald-50 rounded-xl border border-emerald-200">
                        <svg class="w-6 h-6 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-extrabold text-emerald-700">Disetujui</p>
                            <p class="text-[10px] text-emerald-600 mt-0.5">
                                oleh {{ $revision->reviewedBy?->name ?? 'Admin' }} ·
                                {{ $revision->reviewed_at?->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3 p-3 bg-red-50 rounded-xl border border-red-200">
                        <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-extrabold text-red-700">Ditolak</p>
                            <p class="text-[10px] text-red-600 mt-0.5">
                                oleh {{ $revision->reviewedBy?->name ?? 'Admin' }} ·
                                {{ $revision->reviewed_at?->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                    @if($revision->review_notes)
                        <div class="mt-3 p-3 bg-red-50 rounded-xl border border-red-100">
                            <p class="text-[9px] font-bold text-red-400 uppercase tracking-wider mb-1">Alasan Penolakan</p>
                            <p class="text-xs text-red-700 leading-relaxed">{{ $revision->review_notes }}</p>
                        </div>
                    @endif
                @endif
            </div>

            {{-- Alasan Pengajuan --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Alasan Pengajuan</p>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $revision->reason }}</p>
                </div>
            </div>

        </div>

        {{-- ── RIGHT: Comparison + Aksi ─────────────────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- ── Perbandingan Data ── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-[#f8faf9]">
                    <p class="text-xs font-extrabold text-[#0a2219] uppercase tracking-widest">Perbandingan Data Absensi</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Data original vs data yang diajukan karyawan</p>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-2 gap-5">

                        {{-- ORIGINAL --}}
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                <p class="text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">Data Original</p>
                            </div>
                            @if($revision->attendance)
                                <div class="space-y-3">
                                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                                        <p class="text-[9px] text-gray-400 uppercase tracking-wider font-bold mb-1">Jam Masuk</p>
                                        <p class="text-xl font-extrabold {{ $revision->attendance->check_in ? 'text-gray-800' : 'text-gray-300' }}">
                                            {{ $revision->attendance->check_in
                                                ? \Carbon\Carbon::parse($revision->attendance->check_in)->format('H:i')
                                                : '--:--' }}
                                        </p>
                                        <p class="text-[9px] text-gray-400 mt-0.5">WIB</p>
                                    </div>
                                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                                        <p class="text-[9px] text-gray-400 uppercase tracking-wider font-bold mb-1">Jam Keluar</p>
                                        <p class="text-xl font-extrabold {{ $revision->attendance->check_out ? 'text-gray-800' : 'text-gray-300' }}">
                                            {{ $revision->attendance->check_out
                                                ? \Carbon\Carbon::parse($revision->attendance->check_out)->format('H:i')
                                                : '--:--' }}
                                        </p>
                                        <p class="text-[9px] text-gray-400 mt-0.5">WIB</p>
                                    </div>
                                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3">
                                        <p class="text-[9px] text-gray-400 uppercase tracking-wider font-bold mb-1">Status</p>
                                        @if($revision->attendance->status === 'on_time')
                                            <span class="inline-flex items-center px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-extrabold rounded-lg border border-emerald-200 uppercase">Tepat Waktu</span>
                                        @elseif($revision->attendance->status === 'late')
                                            <span class="inline-flex items-center px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-extrabold rounded-lg border border-amber-200 uppercase">Terlambat</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 bg-red-50 text-red-700 text-[10px] font-extrabold rounded-lg border border-red-200 uppercase">Mangkir</span>
                                        @endif
                                    </div>
                                    @if($revision->attendance->notes)
                                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-3">
                                            <p class="text-[9px] text-gray-400 uppercase tracking-wider font-bold mb-1">Catatan</p>
                                            <p class="text-xs text-gray-600">{{ $revision->attendance->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="h-full flex flex-col items-center justify-center bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl p-6 text-center">
                                    <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                    <p class="text-xs font-bold text-gray-400">Tidak Ada Data</p>
                                    <p class="text-[10px] text-gray-400 mt-1">Karyawan belum memiliki record absensi di tanggal ini</p>
                                </div>
                            @endif
                        </div>

                        {{-- REQUESTED --}}
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-2 h-2 bg-[#d4af37] rounded-full"></div>
                                <p class="text-[10px] font-extrabold text-[#d4af37] uppercase tracking-widest">Diajukan Karyawan</p>
                            </div>
                            <div class="space-y-3">
                                <div class="bg-[#fdfaf2] border-2 border-[#d4af37]/40 rounded-xl p-4 relative overflow-hidden">
                                    <div class="absolute top-0 right-0 w-8 h-8 bg-[#d4af37]/10 rounded-bl-2xl"></div>
                                    <p class="text-[9px] text-[#a88a20] uppercase tracking-wider font-bold mb-1">Jam Masuk</p>
                                    <p class="text-xl font-extrabold {{ $revision->requested_check_in ? 'text-[#0a2219]' : 'text-gray-300' }}">
                                        {{ $revision->requested_check_in
                                            ? \Carbon\Carbon::parse($revision->requested_check_in)->format('H:i')
                                            : '--:--' }}
                                    </p>
                                    <p class="text-[9px] text-[#a88a20] mt-0.5">WIB</p>

                                    {{-- Diff indicator --}}
                                    @if($revision->attendance && $revision->requested_check_in && $revision->attendance->check_in)
                                        @php
                                            $origIn = \Carbon\Carbon::parse($revision->attendance->check_in);
                                            $reqIn  = \Carbon\Carbon::parse($revision->requested_check_in);
                                            $diff   = $origIn->diffInMinutes($reqIn, false);
                                        @endphp
                                        @if($diff !== 0)
                                            <span class="mt-1.5 inline-block text-[9px] font-extrabold {{ $diff < 0 ? 'text-emerald-600' : 'text-red-500' }} uppercase">
                                                {{ $diff < 0 ? '▲ ' . abs($diff) . ' mnt lebih awal' : '▼ ' . $diff . ' mnt lebih lambat' }}
                                            </span>
                                        @endif
                                    @endif
                                </div>

                                <div class="bg-[#fdfaf2] border-2 border-[#d4af37]/40 rounded-xl p-4 relative overflow-hidden">
                                    <div class="absolute top-0 right-0 w-8 h-8 bg-[#d4af37]/10 rounded-bl-2xl"></div>
                                    <p class="text-[9px] text-[#a88a20] uppercase tracking-wider font-bold mb-1">Jam Keluar</p>
                                    <p class="text-xl font-extrabold {{ $revision->requested_check_out ? 'text-[#0a2219]' : 'text-gray-300' }}">
                                        {{ $revision->requested_check_out
                                            ? \Carbon\Carbon::parse($revision->requested_check_out)->format('H:i')
                                            : '--:--' }}
                                    </p>
                                    <p class="text-[9px] text-[#a88a20] mt-0.5">WIB</p>

                                    @if($revision->attendance && $revision->requested_check_out && $revision->attendance->check_out)
                                        @php
                                            $origOut = \Carbon\Carbon::parse($revision->attendance->check_out);
                                            $reqOut  = \Carbon\Carbon::parse($revision->requested_check_out);
                                            $diffOut = $origOut->diffInMinutes($reqOut, false);
                                        @endphp
                                        @if($diffOut !== 0)
                                            <span class="mt-1.5 inline-block text-[9px] font-extrabold {{ $diffOut > 0 ? 'text-emerald-600' : 'text-red-500' }} uppercase">
                                                {{ $diffOut > 0 ? '▲ ' . $diffOut . ' mnt lebih lama' : '▼ ' . abs($diffOut) . ' mnt lebih cepat' }}
                                            </span>
                                        @endif
                                    @endif
                                </div>

                                <div class="bg-[#fdfaf2] border border-[#d4af37]/30 rounded-xl p-3">
                                    <p class="text-[9px] text-[#a88a20] uppercase tracking-wider font-bold mb-1">Status (setelah approve)</p>
                                    <span class="inline-flex items-center px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-extrabold rounded-lg border border-blue-200 uppercase">
                                        Dihitung ulang otomatis
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Arrow Indicator --}}
                    <div class="flex justify-center mt-4">
                        <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-full px-4 py-1.5">
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                            <span class="w-1.5 h-1.5 bg-[#d4af37] rounded-full"></span>
                            <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Perubahan yang Diminta</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Panel Aksi (hanya jika masih pending) ── --}}
            @if($revision->isPending())
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-[#f8faf9]">
                        <p class="text-xs font-extrabold text-[#0a2219] uppercase tracking-widest">Ambil Tindakan</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">Setujui untuk memperbarui data absensi, atau tolak dengan memberikan alasan.</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            {{-- Approve --}}
                            <form method="POST" action="{{ route('admin.attendance-revisions.approve', $revision->id) }}"
                                  onsubmit="confirmAction(event, 'Setujui pengajuan ini? Data absensi karyawan akan diperbarui secara otomatis.', this)">
                                @csrf
                                @method('PUT')
                                <button type="submit"
                                        class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white py-3.5 rounded-xl font-bold text-sm uppercase tracking-wider transition shadow-sm active:scale-95">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Setujui Pengajuan
                                </button>
                            </form>

                            {{-- Reject --}}
                            <button type="button" onclick="document.getElementById('rejectPanel').classList.toggle('hidden')"
                                    class="w-full flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 py-3.5 rounded-xl font-bold text-sm uppercase tracking-wider transition active:scale-95">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Tolak Pengajuan
                            </button>
                        </div>

                        {{-- Reject Form (expandable) --}}
                        <div id="rejectPanel" class="hidden mt-4 pt-4 border-t border-gray-100">
                            <form method="POST" action="{{ route('admin.attendance-revisions.reject', $revision->id) }}">
                                @csrf
                                @method('PUT')
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">
                                    Alasan Penolakan <span class="text-red-500">*</span>
                                </label>
                                <textarea name="review_notes" rows="3" required minlength="5" maxlength="300"
                                          placeholder="Berikan alasan yang jelas mengapa pengajuan ini ditolak..."
                                          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 resize-none transition">{{ old('review_notes') }}</textarea>
                                @error('review_notes')
                                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                                @enderror
                                <button type="submit"
                                        class="mt-3 w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-xl font-bold text-sm uppercase tracking-wider transition">
                                    Konfirmasi Penolakan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
