@extends('layouts.employee')

@section('title', 'Riwayat Pengajuan Cuti')

@section('content')
<div class="mb-5 flex justify-between items-center border-b border-gray-100 pb-4">
    <div>
        <h2 class="text-base font-extrabold text-[#0a2219] uppercase tracking-wider">Pengajuan Cuti & Izin</h2>
        <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">Kelola pengajuan libur atau izin sakit Anda</p>
    </div>
    <a href="{{ route('employee.leave-requests.create') }}" class="bg-[#0a2219] hover:bg-[#123b2c] text-white px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition shadow-sm border border-transparent">
        + Ajukan Cuti
    </a>
</div>

@if (session('success'))
    <div class="mb-5 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center shadow-sm">
        <svg class="w-5 h-5 mr-2 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="font-semibold text-xs">{{ session('success') }}</p>
    </div>
@endif

<!-- List Pengajuan Cuti -->
<div class="space-y-4">
    @forelse($leaveRequests as $leave)
        <a href="{{ route('employee.leave-requests.show', $leave->id) }}" class="block bg-white rounded-2xl shadow-sm p-5 border border-gray-100 border-l-4 {{ $leave->status === 'approved' ? 'border-emerald-500' : ($leave->status === 'rejected' ? 'border-red-500' : 'border-amber-500') }} hover:shadow transition duration-200">
            <div class="flex justify-between items-start mb-2.5">
                <div>
                    <h3 class="font-bold text-sm text-gray-800">{{ $leave->leaveType->name }}</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }} ({{ $leave->total_days }} Hari)
                    </p>
                </div>
                
                @if($leave->status === 'approved')
                    <span class="inline-flex items-center px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-extrabold rounded-lg border border-emerald-100 uppercase tracking-wider">
                        Disetujui
                    </span>
                @elseif($leave->status === 'rejected')
                    <span class="inline-flex items-center px-2 py-0.5 bg-red-50 text-red-700 text-[10px] font-extrabold rounded-lg border border-red-100 uppercase tracking-wider">
                        Ditolak
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-extrabold rounded-lg border border-amber-100 uppercase tracking-wider">
                        Pending
                    </span>
                @endif
            </div>
            
            <p class="text-xs text-gray-500 font-semibold truncate mt-2.5 bg-gray-50 p-2.5 rounded-xl border border-gray-100/50">{{ $leave->reason }}</p>
            
            <div class="mt-3.5 text-[9px] text-gray-400 font-bold uppercase tracking-wider flex items-center justify-between border-t border-gray-50 pt-2.5">
                <span>Diajukan: {{ $leave->created_at->format('d M Y, H:i') }} WIB</span>
                <span class="text-[#0a2219] hover:text-[#d4af37] flex items-center transition">
                    Detail
                    <svg class="w-3.5 h-3.5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </span>
            </div>
        </a>
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center text-gray-500 font-medium">
            <svg class="w-12 h-12 text-gray-300 mb-3 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <p class="text-xs text-gray-400 font-semibold mb-4">Anda belum pernah mengajukan cuti atau izin.</p>
            <a href="{{ route('employee.leave-requests.create') }}" class="inline-flex bg-[#0a2219] hover:bg-[#123b2c] text-white px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition shadow-sm">Ajukan Sekarang</a>
        </div>
    @endforelse
</div>

<div class="mt-5 pb-6">
    {{ $leaveRequests->links() }}
</div>
@endsection
