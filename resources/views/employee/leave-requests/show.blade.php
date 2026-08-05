@extends('layouts.employee')

@section('title', 'Detail Pengajuan Cuti')

@section('content')
<div class="mb-5 flex items-center gap-2 border-b border-gray-100 pb-4">
    <a href="{{ route('employee.leave-requests.index') }}" class="text-gray-400 hover:text-[#0a2219] transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <div>
        <h2 class="text-base font-extrabold text-[#0a2219] uppercase tracking-wider">Detail Pengajuan Cuti</h2>
        <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">Status peninjauan cuti & izin Anda</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
    <div class="flex justify-between items-start border-b border-gray-50 pb-4 mb-4">
        <div>
            <h3 class="font-extrabold text-sm text-gray-800">{{ $leaveRequest->leaveType->name }}</h3>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">Diajukan: {{ $leaveRequest->created_at->format('d M Y, H:i') }} WIB</p>
        </div>
        <div>
            @if($leaveRequest->status === 'approved')
                <span class="inline-flex items-center px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-extrabold rounded-lg border border-emerald-100 uppercase tracking-wider">
                    Disetujui
                </span>
            @elseif($leaveRequest->status === 'rejected')
                <span class="inline-flex items-center px-2.5 py-0.5 bg-red-50 text-red-700 text-[10px] font-extrabold rounded-lg border border-red-100 uppercase tracking-wider">
                    Ditolak
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-extrabold rounded-lg border border-amber-100 uppercase tracking-wider">
                    Pending
                </span>
            @endif
        </div>
    </div>

    <div class="space-y-4">
        <div>
            <h4 class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Periode Cuti</h4>
            <p class="text-sm text-gray-800 font-extrabold flex items-center">
                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }} s/d 
                {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }}
            </p>
            <p class="text-xs text-[#d4af37] font-bold uppercase tracking-wider mt-1">Total Durasi: {{ $leaveRequest->total_days }} Hari Kerja</p>
        </div>

        <div>
            <h4 class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Alasan / Keterangan</h4>
            <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100 text-xs font-semibold text-gray-700 leading-relaxed">
                {{ $leaveRequest->reason }}
            </div>
        </div>

        @if($leaveRequest->document_path)
            <div>
                <h4 class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Dokumen Lampiran</h4>
                <a href="{{ asset('storage/' . $leaveRequest->document_path) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-[#0a2219] hover:text-[#d4af37] font-bold uppercase tracking-wider transition">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Lihat Dokumen
                </a>
            </div>
        @endif
    </div>

    @if($leaveRequest->reviewed_at)
        <div class="mt-6 pt-5 border-t border-gray-50">
            <h4 class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-2">Keputusan Review HRD / Admin</h4>
            <div class="{{ $leaveRequest->status === 'approved' ? 'bg-[#e7f0ec] text-[#0a2219] border-[#d2dfd8]' : 'bg-red-50 text-red-800 border-red-100' }} p-4 rounded-xl border text-xs font-semibold space-y-1">
                <p><span class="opacity-70 font-bold uppercase tracking-wider">Tanggal Di-review:</span> {{ \Carbon\Carbon::parse($leaveRequest->reviewed_at)->format('d M Y, H:i') }} WIB</p>
                <p><span class="opacity-70 font-bold uppercase tracking-wider">Catatan:</span> {{ $leaveRequest->review_notes ?? '-' }}</p>
            </div>
        </div>
    @endif
</div>

@if($leaveRequest->status === 'pending')
    <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-6">
        <h3 class="text-sm font-extrabold text-red-800 uppercase tracking-wider mb-1">Batalkan Pengajuan Cuti</h3>
        <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider mb-4">Pengajuan hanya dapat dibatalkan saat statusnya masih dalam antrean (Pending).</p>
        
        <form action="{{ route('employee.leave-requests.destroy', $leaveRequest->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan cuti ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-600 font-bold py-3 px-4 rounded-xl transition text-xs uppercase tracking-wider border border-red-100/50">
                Batalkan Pengajuan Sekarang
            </button>
        </form>
    </div>
@endif
@endsection
