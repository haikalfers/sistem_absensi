@extends('layouts.admin')

@section('title', 'Jadwal Dinas Luar')
@section('page-title', 'Jadwal Dinas Luar Karyawan')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h3 class="text-base font-bold text-gray-800 uppercase tracking-wider">Dinas Luar</h3>
        <p class="text-xs text-gray-500 font-semibold mt-1">Kelola penugasan karyawan di luar area kantor</p>
    </div>
    <a href="{{ route('admin.field-assignments.create') }}"
       class="inline-flex items-center gap-2 bg-[#7f1d1d] hover:bg-[#991b1b] text-white px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition shadow-sm border border-[#450a0a]">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Buat Jadwal Dinas Luar
    </a>
</div>

{{-- Alert --}}
@if (session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl mb-6 shadow-sm flex items-center gap-2 text-sm font-semibold">
        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 shadow-sm">
        <ul class="list-disc list-inside text-sm font-semibold space-y-1">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif

{{-- Filter Bar --}}
<form method="GET" action="{{ route('admin.field-assignments.index') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
    <div class="flex flex-col sm:flex-row gap-3 items-end">
        <div class="flex-1">
            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Filter Tanggal</label>
            <input type="date" name="date" value="{{ $filterDate }}"
                   class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#7f1d1d]">
        </div>
        <div class="flex-1">
            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Filter Karyawan</label>
            <select name="employee_id" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#7f1d1d]">
                <option value="">Semua Karyawan</option>
                @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}" {{ $filterEmp == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-[#7f1d1d] text-white px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-[#991b1b] transition">
                Filter
            </button>
            <a href="{{ route('admin.field-assignments.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-gray-200 transition">
                Reset
            </a>
        </div>
    </div>
</form>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[800px] border-collapse">
            <thead>
                <tr class="bg-red-50/60 border-b border-red-100">
                    <th class="px-6 py-4 text-left text-xs font-bold text-[#7f1d1d] uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-[#7f1d1d] uppercase tracking-wider">Karyawan</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-[#7f1d1d] uppercase tracking-wider">Lokasi Penugasan</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-[#7f1d1d] uppercase tracking-wider">Status Absensi</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-[#7f1d1d] uppercase tracking-wider">Dibuat Oleh</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-[#7f1d1d] uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($assignments as $assignment)
                    @php
                        $isToday  = $assignment->date->isToday();
                        $isPast   = $assignment->date->isPast() && !$isToday;
                        $isFuture = $assignment->date->isFuture();
                        $att      = $assignment->attendance;
                    @endphp
                    <tr class="hover:bg-red-50/20 transition duration-150">
                        {{-- Tanggal --}}
                        <td class="px-6 py-4">
                            <div class="text-sm font-extrabold text-gray-800">
                                {{ $assignment->date->translatedFormat('d M Y') }}
                            </div>
                            <div class="text-[10px] font-bold uppercase tracking-wider mt-0.5
                                {{ $isToday ? 'text-emerald-600' : ($isPast ? 'text-gray-400' : 'text-blue-500') }}">
                                {{ $isToday ? '🟢 Hari Ini' : ($isPast ? 'Sudah Lewat' : '📅 Akan Datang') }}
                            </div>
                        </td>

                        {{-- Karyawan --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 bg-[#7f1d1d] text-white rounded-xl flex items-center justify-center font-extrabold text-sm flex-shrink-0">
                                    {{ substr($assignment->employee->name ?? 'K', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $assignment->employee->name ?? '-' }}</p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">
                                        {{ $assignment->employee->division ?? 'Umum' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Lokasi --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1.5">
                                <span class="text-sm">📍</span>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $assignment->location_name }}</p>
                                    @if ($assignment->location_lat && $assignment->location_lng)
                                        <p class="text-[10px] text-gray-400 font-semibold font-mono">
                                            {{ $assignment->location_lat }}, {{ $assignment->location_lng }}
                                        </p>
                                    @endif
                                    @if ($assignment->notes)
                                        <p class="text-[10px] text-gray-400 mt-0.5 italic max-w-xs truncate" title="{{ $assignment->notes }}">
                                            {{ $assignment->notes }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Status Absensi --}}
                        <td class="px-6 py-4">
                            @if ($att && $att->check_in)
                                <div class="space-y-1">
                                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-500/10 text-emerald-700 text-[10px] font-extrabold rounded-lg border border-emerald-500/20 uppercase tracking-wider">
                                        ✓ Sudah Absen
                                    </span>
                                    <div class="text-[10px] font-bold text-gray-500">
                                        Masuk: {{ $att->check_in->format('H:i') }}
                                        @if ($att->check_out)
                                            · Keluar: {{ $att->check_out->format('H:i') }}
                                        @endif
                                    </div>
                                    {{-- GPS Lokasi Aktual --}}
                                    @if ($att->check_in_lat && $att->check_in_lng)
                                        <a href="https://maps.google.com/?q={{ $att->check_in_lat }},{{ $att->check_in_lng }}"
                                           target="_blank"
                                           class="inline-flex items-center gap-1 text-[10px] text-blue-600 font-bold hover:underline">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Verifikasi Lokasi GPS
                                        </a>
                                    @endif
                                </div>
                            @elseif ($isToday || $isFuture)
                                <span class="inline-flex items-center px-2.5 py-1 bg-amber-500/10 text-amber-700 text-[10px] font-extrabold rounded-lg border border-amber-500/20 uppercase tracking-wider">
                                    ⏳ Belum Absen
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 bg-red-500/10 text-red-700 text-[10px] font-extrabold rounded-lg border border-red-500/20 uppercase tracking-wider">
                                    ✗ Tidak Absen
                                </span>
                            @endif
                        </td>

                        {{-- Assigned By --}}
                        <td class="px-6 py-4">
                            <p class="text-xs font-bold text-gray-700">{{ $assignment->assignedBy->name ?? '-' }}</p>
                            <p class="text-[10px] text-gray-400">{{ $assignment->created_at->translatedFormat('d M Y, H:i') }}</p>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4 text-center">
                            @if (!($att && $att->is_field_assignment))
                                <form action="{{ route('admin.field-assignments.destroy', $assignment->id) }}" method="POST"
                                      onsubmit="return confirmAction(event, 'Batalkan penugasan dinas luar {{ $assignment->employee->name ?? '' }} tanggal {{ $assignment->date->translatedFormat('d M Y') }}?', this)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold uppercase tracking-wider transition">
                                        Batalkan
                                    </button>
                                </form>
                            @else
                                <span class="text-[10px] text-gray-400 font-semibold">Sudah digunakan</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <div>
                                    <p class="font-semibold text-sm">Belum ada jadwal dinas luar</p>
                                    <p class="text-xs mt-1">Klik "Buat Jadwal Dinas Luar" untuk menambahkan penugasan.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $assignments->links() }}
</div>
@endsection
