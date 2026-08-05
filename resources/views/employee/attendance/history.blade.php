@extends('layouts.employee')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="mb-5 flex items-center gap-2 border-b border-gray-100 pb-4">
    <a href="{{ route('employee.attendance.index') }}" class="text-gray-400 hover:text-[#0a2219] transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <div>
        <h2 class="text-base font-extrabold text-[#0a2219] uppercase tracking-wider">Riwayat Kehadiran</h2>
        <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">Seluruh log kehadiran absensi Anda</p>
    </div>
</div>

<!-- Filter Box -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
    <form method="GET" action="{{ route('employee.attendance.history') }}">
        <div class="flex gap-3">
            <div class="flex-1">
                <select name="month" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-xs font-bold uppercase tracking-wider bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month', $currentMonth) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="flex-1">
                <select name="year" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-xs font-bold uppercase tracking-wider bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200">
                    @for($y = \Carbon\Carbon::now()->year - 2; $y <= \Carbon\Carbon::now()->year; $y++)
                        <option value="{{ $y }}" {{ request('year', $currentYear) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="bg-[#0a2219] hover:bg-[#123b2c] text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition duration-150 shrink-0 shadow-sm border border-transparent">
                Cari
            </button>
        </div>
    </form>
</div>

<!-- List Riwayat -->
<div class="space-y-4">
    @forelse($attendances as $att)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 border-l-4 {{ $att->status === 'on_time' ? 'border-emerald-500' : ($att->status === 'late' ? 'border-amber-500' : 'border-red-500') }} hover:shadow transition duration-200">
            <div class="flex justify-between items-center mb-3">
                <span class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</span>
                
                @if($att->status === 'on_time')
                    <span class="inline-flex items-center px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-extrabold rounded-lg border border-emerald-100 uppercase tracking-wider">
                        Tepat Waktu
                    </span>
                @elseif($att->status === 'late')
                    <span class="inline-flex items-center px-2.5 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-extrabold rounded-lg border border-amber-100 uppercase tracking-wider">
                        Terlambat
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 bg-red-50 text-red-700 text-[10px] font-extrabold rounded-lg border border-red-100 uppercase tracking-wider">
                        Mangkir
                    </span>
                @endif
            </div>
            
            <div class="grid grid-cols-2 gap-4 text-xs font-bold text-gray-600 pt-3 border-t border-gray-50">
                <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                    <span class="text-[9px] text-gray-400 uppercase tracking-wider block mb-1">Jam Masuk</span>
                    <span class="text-sm font-extrabold {{ $att->check_in ? 'text-gray-800' : 'text-gray-300' }}">
                        {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') . ' WIB' : '--:--' }}
                    </span>
                </div>
                <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                    <span class="text-[9px] text-gray-400 uppercase tracking-wider block mb-1">Jam Keluar</span>
                    <span class="text-sm font-extrabold {{ $att->check_out ? 'text-gray-800' : 'text-gray-300' }}">
                        {{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') . ' WIB' : '--:--' }}
                    </span>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center text-gray-500 font-medium">
            <svg class="w-12 h-12 text-gray-300 mb-3 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <span>Tidak ada riwayat absensi pada bulan ini.</span>
        </div>
    @endforelse
</div>

<div class="mt-5 pb-6">
    {{ $attendances->links() }}
</div>
@endsection
