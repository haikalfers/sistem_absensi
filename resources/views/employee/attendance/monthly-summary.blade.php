@extends('layouts.employee')

@section('title', 'Rekap Bulanan')

@section('content')
<div class="mb-5 flex justify-between items-center border-b border-gray-100 pb-4">
    <div class="flex items-center gap-2">
        <a href="{{ route('employee.attendance.index') }}" class="text-[#0a2219] hover:text-[#123b2c] transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h2 class="text-base font-extrabold text-[#0a2219] uppercase tracking-wider">Rekapitulasi Bulanan</h2>
            <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">Bulan: {{ $stats['period_name'] }}</p>
        </div>
    </div>
    <a href="{{ route('employee.attendance.summary.export', ['month' => $month, 'year' => $year]) }}" class="bg-[#e7f0ec] text-[#0a2219] hover:bg-[#d2dfd8] px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border border-[#d2dfd8]">
        <svg class="w-4 h-4 text-[#0a2219]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <span>PDF</span>
    </a>
</div>

<!-- Filter Box -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
    <form method="GET" action="{{ route('employee.attendance.summary') }}">
        <div class="flex gap-3">
            <div class="flex-1">
                <select name="month" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-xs font-bold uppercase tracking-wider bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month', $month) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="flex-1">
                <select name="year" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-xs font-bold uppercase tracking-wider bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200">
                    @for($y = \Carbon\Carbon::now()->year - 2; $y <= \Carbon\Carbon::now()->year; $y++)
                        <option value="{{ $y }}" {{ request('year', $year) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="bg-[#0a2219] hover:bg-[#123b2c] text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition duration-150 shrink-0 border border-transparent shadow-sm">
                Lihat
            </button>
        </div>
    </form>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm p-4 border border-gray-100 border-t-4 border-t-emerald-500">
        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Tepat Waktu</p>
        <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $stats['on_time'] }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-4 border border-gray-100 border-t-4 border-t-amber-500">
        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Terlambat</p>
        <p class="text-2xl font-extrabold text-amber-600 mt-1">{{ $stats['late'] }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-4 border border-gray-100 border-t-4 border-t-red-500">
        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Alpha / Mangkir</p>
        <p class="text-2xl font-extrabold text-red-600 mt-1">{{ $stats['absent'] }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-4 border border-gray-100 border-t-4 border-t-[#0a2219]">
        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Persen Kehadiran</p>
        <p class="text-2xl font-extrabold text-[#0a2219] mt-1">{{ $stats['attendance_percentage'] }}%</p>
    </div>
</div>

<!-- Detail Data per Hari -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
    <h3 class="text-xs font-extrabold text-gray-700 uppercase tracking-widest mb-4 border-b border-gray-50 pb-3">Daftar Kehadiran Harian</h3>
    
    <div class="divide-y divide-gray-50 text-xs font-semibold text-gray-700">
        @foreach($chartData as $dayData)
            <div class="flex justify-between items-center py-3">
                <div class="w-20">
                    <span class="block text-xs font-extrabold text-gray-800">{{ $dayData['date'] }}</span>
                    <span class="block text-[9px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">{{ $dayData['day'] }}</span>
                </div>
                
                @if($dayData['status'] === 'no_data')
                    <div class="flex-1 text-center text-gray-300 text-[10px] font-bold uppercase tracking-widest italic">
                        Tanpa Log
                    </div>
                @else
                    <div class="flex-1 flex gap-2 justify-center font-bold text-gray-600">
                        <span class="bg-gray-50 border border-gray-100 px-2 py-0.5 rounded text-[10px]">{{ $dayData['check_in'] ? \Carbon\Carbon::parse($dayData['check_in'])->format('H:i') : '--:--' }}</span>
                        <span class="text-gray-300">-</span>
                        <span class="bg-gray-50 border border-gray-100 px-2 py-0.5 rounded text-[10px]">{{ $dayData['check_out'] ? \Carbon\Carbon::parse($dayData['check_out'])->format('H:i') : '--:--' }}</span>
                    </div>
                @endif
                
                <div class="w-16 text-right">
                    @if($dayData['status'] === 'on_time')
                        <span class="text-emerald-600 font-black text-sm">✓</span>
                    @elseif($dayData['status'] === 'late')
                        <span class="text-amber-500 font-black text-sm">⚠</span>
                    @elseif($dayData['status'] === 'absent')
                        <span class="text-red-500 font-black text-sm">✗</span>
                    @else
                        <span class="text-gray-200 font-semibold">-</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
