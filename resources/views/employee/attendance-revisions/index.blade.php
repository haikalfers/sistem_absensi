@extends('layouts.employee')

@section('title', 'Pengajuan Presensi Ulang')

@section('content')
<div class="mb-5 flex items-center justify-between border-b border-gray-100 pb-4">
    <div class="flex items-center gap-2">
        <a href="{{ route('employee.attendance.index') }}" class="text-gray-400 hover:text-[#0a2219] transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h2 class="text-base font-extrabold text-[#0a2219] uppercase tracking-wider">Presensi Ulang</h2>
            <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">Riwayat pengajuan Anda</p>
        </div>
    </div>
    <a href="{{ route('employee.attendance-revisions.create') }}"
       class="inline-flex items-center gap-1.5 bg-[#0a2219] hover:bg-[#123b2c] text-white px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition shadow-sm border border-transparent">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Ajukan
    </a>
</div>

{{-- Info Banner --}}
<div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-5 flex gap-3 items-start">
    <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
    </svg>
    <div>
        <p class="text-xs font-bold text-blue-700">Pengajuan Presensi Ulang</p>
        <p class="text-xs text-blue-600 mt-0.5">Gunakan fitur ini jika Anda tidak bisa absen karena kendala teknis (app crash, GPS error, dll). Pengajuan hanya bisa dilakukan untuk <strong>7 hari terakhir</strong>, maks. <strong>1 pengajuan pending</strong> per hari.</p>
    </div>
</div>

{{-- List Pengajuan --}}
<div class="space-y-4">
    @forelse($revisions as $rev)
        <div @class([
            'bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow transition duration-200 border-l-4',
            'border-l-emerald-500' => $rev->status === 'approved',
            'border-l-red-500' => $rev->status === 'rejected',
            'border-l-amber-400' => $rev->status !== 'approved' && $rev->status !== 'rejected'
        ])>

            <div class="flex justify-between items-start mb-3">
                <div>
                    <span class="text-sm font-bold text-gray-800">
                        {{ \Carbon\Carbon::parse($rev->revision_date)->translatedFormat('l, d M Y') }}
                    </span>
                    <p class="text-[11px] text-gray-400 mt-0.5">
                        Diajukan: {{ $rev->created_at->diffForHumans() }}
                    </p>
                </div>

                {{-- Status Badge --}}
                @if($rev->status === 'pending')
                    <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-700 text-[10px] font-extrabold rounded-lg border border-amber-200 uppercase tracking-wider">
                        ⏳ Menunggu
                    </span>
                @elseif($rev->status === 'approved')
                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-extrabold rounded-lg border border-emerald-200 uppercase tracking-wider">
                        ✓ Disetujui
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 bg-red-50 text-red-700 text-[10px] font-extrabold rounded-lg border border-red-200 uppercase tracking-wider">
                        ✗ Ditolak
                    </span>
                @endif
            </div>

            {{-- Requested Times --}}
            <div class="grid grid-cols-2 gap-3 mb-3">
                <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                    <span class="text-[9px] text-gray-400 uppercase tracking-wider block mb-1">Jam Masuk (Diminta)</span>
                    <span class="text-sm font-extrabold text-gray-800">
                        {{ $rev->requested_check_in ? \Carbon\Carbon::parse($rev->requested_check_in)->format('H:i') . ' WIB' : '--:--' }}
                    </span>
                </div>
                <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                    <span class="text-[9px] text-gray-400 uppercase tracking-wider block mb-1">Jam Keluar (Diminta)</span>
                    <span class="text-sm font-extrabold text-gray-800">
                        {{ $rev->requested_check_out ? \Carbon\Carbon::parse($rev->requested_check_out)->format('H:i') . ' WIB' : '--:--' }}
                    </span>
                </div>
            </div>

            {{-- Alasan --}}
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 mb-3">
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">Alasan</p>
                <p class="text-xs text-gray-600 leading-relaxed">{{ $rev->reason }}</p>
            </div>

            {{-- Feedback dari admin (jika ditolak) --}}
            @if($rev->status === 'rejected' && $rev->review_notes)
                <div class="bg-red-50 rounded-xl p-3 border border-red-100 mb-3">
                    <p class="text-[9px] font-bold text-red-500 uppercase tracking-wider mb-1">Alasan Penolakan</p>
                    <p class="text-xs text-red-600 leading-relaxed">{{ $rev->review_notes }}</p>
                </div>
            @endif

            @if($rev->status === 'approved' && $rev->reviewed_at)
                <p class="text-[10px] text-emerald-600 font-semibold">
                    ✓ Disetujui pada {{ $rev->reviewed_at->format('d M Y, H:i') }}
                </p>
            @endif

            {{-- Tombol Batalkan (jika masih pending) --}}
            @if($rev->isPending())
                <div class="mt-3 pt-3 border-t border-gray-100 flex justify-end">
                    <form method="POST" action="{{ route('employee.attendance-revisions.destroy', $rev->id) }}"
                          onsubmit="return confirm('Batalkan pengajuan presensi ulang ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-red-500 hover:text-red-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Batalkan Pengajuan
                        </button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
            <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.657 48.657 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" />
                </svg>
            </div>
            <p class="text-sm font-bold text-gray-500">Belum ada pengajuan presensi ulang</p>
            <p class="text-xs text-gray-400 mt-1">Gunakan tombol "Ajukan" di atas jika ada kendala absensi</p>
        </div>
    @endforelse
</div>

<div class="mt-5 pb-6">
    {{ $revisions->links() }}
</div>
@endsection
