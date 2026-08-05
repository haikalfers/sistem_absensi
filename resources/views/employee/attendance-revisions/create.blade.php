@extends('layouts.employee')

@section('title', 'Ajukan Presensi Ulang')

@section('content')
<div class="mb-5 flex items-center gap-2 border-b border-gray-100 pb-4">
    <a href="{{ route('employee.attendance-revisions.index') }}" class="text-gray-400 hover:text-[#0a2219] transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <div>
        <h2 class="text-base font-extrabold text-[#0a2219] uppercase tracking-wider">Ajukan Presensi Ulang</h2>
        <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">Untuk 7 hari terakhir</p>
    </div>
</div>

{{-- Info Card --}}
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-5 flex gap-3 items-start">
    <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
    </svg>
    <div>
        <p class="text-xs font-bold text-amber-700">Petunjuk Pengajuan</p>
        <ul class="text-xs text-amber-600 mt-1 space-y-0.5 list-disc list-inside">
            <li>Hanya untuk hari yang sudah ada kendala absensi</li>
            <li>Maksimal <strong>1 pengajuan pending</strong> per hari</li>
            <li>Pengajuan diproses oleh admin, harap tunggu konfirmasi</li>
            <li>Isi alasan sejelas mungkin (app crash, GPS error, dll)</li>
        </ul>
    </div>
</div>

{{-- Form --}}
<form method="POST" action="{{ route('employee.attendance-revisions.store') }}" id="revisionForm">
    @csrf

    {{-- Pilih Tanggal --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-4">
        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-3">
            Pilih Tanggal Absensi <span class="text-red-500">*</span>
        </label>

        @if($availableDates->isEmpty())
            <p class="text-sm text-gray-500 italic">Tidak ada tanggal yang tersedia untuk diajukan.</p>
        @else
            <div class="space-y-2">
                @foreach($availableDates as $date)
                    @php
                        $dateStr = $date->toDateString();
                        $isPending = in_array($dateStr, $pendingDates);
                        $existingAtt = $existingAttendances->get($dateStr);
                    @endphp
                    <label @class([
                        'flex items-start gap-3 p-3.5 rounded-xl border transition',
                        'bg-gray-50 border-gray-200 opacity-60 cursor-not-allowed' => $isPending,
                        'bg-[#f0faf5] border-[#0a2219] cursor-pointer' => !$isPending && old('revision_date') === $dateStr,
                        'border-gray-200 hover:border-[#0a2219] hover:bg-[#f9fdfb] cursor-pointer' => !$isPending && old('revision_date') !== $dateStr
                    ])>
                        <input type="radio" name="revision_date" value="{{ $dateStr }}"
                               class="mt-0.5 accent-[#0a2219]"
                               {{ $isPending ? 'disabled' : '' }}
                               {{ old('revision_date') === $dateStr ? 'checked' : '' }}>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <span class="text-sm font-bold text-gray-800">
                                    {{ $date->translatedFormat('l, d M Y') }}
                                </span>
                                @if($isPending)
                                    <span class="text-[9px] font-extrabold text-amber-600 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full uppercase tracking-wider">
                                        Pending
                                    </span>
                                @elseif($existingAtt)
                                    @if($existingAtt->status === 'on_time')
                                        <span class="text-[9px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-full uppercase">Tepat Waktu</span>
                                    @elseif($existingAtt->status === 'late')
                                        <span class="text-[9px] font-bold text-amber-600 bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-full uppercase">Terlambat</span>
                                    @else
                                        <span class="text-[9px] font-bold text-red-500 bg-red-50 border border-red-100 px-2 py-0.5 rounded-full uppercase">Mangkir</span>
                                    @endif
                                @else
                                    <span class="text-[9px] font-bold text-gray-400 bg-gray-50 border border-gray-100 px-2 py-0.5 rounded-full uppercase">Belum Ada Data</span>
                                @endif
                            </div>
                            {{-- Info absensi existing --}}
                            @if($existingAtt && !$isPending)
                                <p class="text-[10px] text-gray-400 mt-1">
                                    Masuk: {{ $existingAtt->check_in ? \Carbon\Carbon::parse($existingAtt->check_in)->format('H:i') : '--:--' }}
                                    &nbsp;·&nbsp;
                                    Keluar: {{ $existingAtt->check_out ? \Carbon\Carbon::parse($existingAtt->check_out)->format('H:i') : '--:--' }}
                                </p>
                            @endif
                            @if($isPending)
                                <p class="text-[10px] text-amber-500 mt-0.5">Ada pengajuan pending untuk hari ini</p>
                            @endif
                        </div>
                    </label>
                @endforeach
            </div>
        @endif

        @error('revision_date')
            <p class="text-red-500 text-xs font-semibold mt-2">{{ $message }}</p>
        @enderror
    </div>

    {{-- Jam yang Diajukan --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-4">
        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-3">
            Jam Kehadiran yang Diminta
        </p>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Jam Masuk</label>
                <input type="time" name="requested_check_in" id="requested_check_in"
                       value="{{ old('requested_check_in') }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] bg-gray-50 transition">
                @error('requested_check_in')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Jam Keluar</label>
                <input type="time" name="requested_check_out" id="requested_check_out"
                       value="{{ old('requested_check_out') }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] bg-gray-50 transition">
                @error('requested_check_out')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <p class="text-[10px] text-gray-400 mt-2">Kosongkan jika tidak ingat / tidak perlu diubah.</p>
    </div>

    {{-- Alasan --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
        <label for="reason" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-3">
            Alasan Pengajuan <span class="text-red-500">*</span>
        </label>
        <textarea name="reason" id="reason" rows="4"
                  placeholder="Contoh: Aplikasi mengalami crash saat saya mencoba absen masuk pukul 07.45. GPS tidak dapat mendeteksi lokasi padahal saya sudah berada di area kantor..."
                  maxlength="500"
                  class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] bg-gray-50 transition resize-none leading-relaxed">{{ old('reason') }}</textarea>
        <div class="flex justify-between mt-1.5">
            @error('reason')
                <p class="text-red-500 text-xs font-semibold">{{ $message }}</p>
            @else
                <p class="text-[10px] text-gray-400">Minimal 10 karakter. Jelaskan kendala yang terjadi.</p>
            @enderror
            <span class="text-[10px] text-gray-400" id="charCount">0/500</span>
        </div>
    </div>

    {{-- Submit --}}
    <button type="submit"
            class="w-full bg-[#0a2219] hover:bg-[#123b2c] text-white py-3.5 rounded-2xl font-bold text-sm uppercase tracking-wider transition shadow-sm border border-transparent active:scale-95">
        Kirim Pengajuan Presensi Ulang
    </button>
</form>

@endsection

@section('js')
<script>
    // Character counter for reason
    const reasonInput = document.getElementById('reason');
    const charCount = document.getElementById('charCount');
    if (reasonInput && charCount) {
        reasonInput.addEventListener('input', () => {
            charCount.textContent = reasonInput.value.length + '/500';
        });
        // Init
        charCount.textContent = reasonInput.value.length + '/500';
    }
</script>
@endsection
