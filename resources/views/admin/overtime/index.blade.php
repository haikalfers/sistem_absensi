@extends('layouts.admin')

@section('title', 'Data Lembur')
@section('page-title', 'Validasi Lembur Karyawan')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h3 class="text-base font-bold text-gray-800 uppercase tracking-wider">Rekapitulasi Lembur</h3>
        <p class="text-xs text-gray-500 font-semibold mt-1">Kelola data lembur harian dan validasi volume kerja/tonase</p>
    </div>
</div>

<!-- Filter Form Container -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('admin.overtime.index') }}">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <!-- Status Select -->
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Status Validasi</label>
                <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] bg-white transition duration-200">
                    <option value="">-- Semua Status --</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Belum Validasi)</option>
                    <option value="validated" {{ request('status') === 'validated' ? 'selected' : '' }}>Tervalidasi</option>
                </select>
            </div>

            <!-- Tipe Lembur Select -->
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Tipe Lembur</label>
                <select name="type" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] bg-white transition duration-200">
                    <option value="">-- Semua Tipe --</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Button -->
            <div class="flex items-end">
                <button type="submit" class="w-full md:w-auto bg-[#0a2219] hover:bg-[#123b2c] text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 border border-transparent focus:ring-2 focus:ring-[#d4af37] shadow-sm">
                    Filter Data
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Table Container -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[800px] border-collapse">
            <thead>
                <tr class="bg-[#f0f4f2] border-b border-gray-100">
                    <th class="px-6 py-4.5 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-4.5 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Karyawan</th>
                    <th class="px-6 py-4.5 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Tipe Lembur</th>
                    <th class="px-6 py-4.5 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Volume / Durasi</th>
                    <th class="px-6 py-4.5 text-center text-xs font-bold text-[#0a2219] uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4.5 text-center text-xs font-bold text-[#0a2219] uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($overtimes as $overtime)
                    <tr class="hover:bg-[#fcfdfc] transition duration-150">
                        <!-- Date -->
                        <td class="px-6 py-4 text-sm font-semibold text-gray-600">
                            {{ \Carbon\Carbon::parse($overtime->date)->format('d M Y') }}
                        </td>
                        
                        <!-- Employee -->
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gray-100 text-[#0a2219] rounded-xl flex items-center justify-center font-bold text-xs border border-gray-200">
                                    {{ substr($overtime->employee->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $overtime->employee->name }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $overtime->employee->employee_code }}</p>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Type -->
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700 font-medium capitalize">
                                {{ str_replace('_', ' ', $overtime->type) }}
                            </span>
                        </td>
                        
                        <!-- Duration / Amount -->
                        <td class="px-6 py-4 text-sm font-bold text-gray-800">
                            @if(in_array($overtime->type, ['office', 'admin_production']))
                                {{ $overtime->hours ?? 0 }} Jam
                            @else
                                {{ $overtime->kg_amount ?? 0 }} KG
                            @endif
                        </td>
                        
                        <!-- Status Badge -->
                        <td class="px-6 py-4 text-center">
                            @if ($overtime->validated_by)
                                <span class="inline-flex items-center px-2.5 py-1 bg-emerald-500/10 text-emerald-700 text-xs font-extrabold rounded-lg border border-emerald-500/20 uppercase tracking-wider">
                                    Tervalidasi
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 bg-amber-500/10 text-amber-700 text-xs font-extrabold rounded-lg border border-amber-500/20 uppercase tracking-wider">
                                    Pending
                                </span>
                            @endif
                        </td>
                        
                        <!-- Actions -->
                        <td class="px-6 py-4 text-center">
                            @if (!$overtime->validated_by)
                                <a href="{{ route('admin.overtime.edit', $overtime->id) }}" class="inline-flex items-center text-xs font-bold text-amber-600 hover:text-amber-800 transition duration-150" title="Validasi">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Validasi
                                </a>
                            @else
                                <a href="{{ route('admin.overtime.edit', $overtime->id) }}" class="inline-flex items-center text-xs font-bold text-[#0a2219] hover:text-[#d4af37] transition duration-150" title="Detail">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Detail
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-medium">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Tidak ada log data lembur terdeteksi.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $overtimes->links() }}
</div>
@endsection
