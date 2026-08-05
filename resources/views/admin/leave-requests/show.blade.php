@extends('layouts.admin')

@section('title', 'Detail Pengajuan Cuti')
@section('page-title', 'Detail Pengajuan Cuti')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-b border-gray-100 pb-5">
        <div>
            <span class="text-[10px] font-extrabold text-[#d4af37] tracking-widest uppercase">Detail Pengajuan</span>
            <h2 class="text-xl font-extrabold text-[#0a2219] mt-1">Cuti/Izin - {{ $leaveRequest->employee->name }}</h2>
        </div>
        <a href="{{ route('admin.leave-requests.index') }}" class="inline-flex items-center text-xs font-bold text-gray-400 hover:text-[#0a2219] uppercase tracking-wider transition duration-150">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali
        </a>
    </div>

    <!-- Error/Success Flash Messages -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-100 text-red-600 p-4 rounded-xl mb-6 shadow-sm">
            <p class="font-bold text-sm mb-1">Terjadi kesalahan:</p>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="font-semibold text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 space-y-8">
        
        <!-- Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Left Panel (Employee & Leave Specs) -->
            <div class="space-y-6">
                <!-- Karyawan -->
                <div>
                    <h4 class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1.5">Nama Karyawan</h4>
                    <div class="flex items-center space-x-3 bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <div class="w-10 h-10 bg-[#e7f0ec] text-[#0a2219] rounded-xl flex items-center justify-center font-bold text-sm border border-[#d2dfd8]">
                            {{ substr($leaveRequest->employee->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">{{ $leaveRequest->employee->name }}</p>
                            <p class="text-[10px] font-bold text-[#d4af37] tracking-wider uppercase">{{ $leaveRequest->employee->employee_code }}</p>
                        </div>
                    </div>
                </div>

                <!-- Jenis Cuti -->
                <div>
                    <h4 class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1.5">Jenis Cuti</h4>
                    <p class="text-base font-bold text-[#0a2219] capitalize">{{ $leaveRequest->leaveType->name }}</p>
                </div>

                <!-- Durasi -->
                <div>
                    <h4 class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1.5">Durasi Pengajuan</h4>
                    <p class="text-base font-bold text-[#d4af37]">{{ $leaveRequest->total_days }} Hari</p>
                </div>
            </div>

            <!-- Right Panel (Dates & Attachment) -->
            <div class="space-y-6">
                <!-- Tanggal Pelaksanaan -->
                <div>
                    <h4 class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1.5">Tanggal Pelaksanaan</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider block">Mulai</span>
                            <span class="text-xs font-bold text-gray-700 mt-1 block">{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }}</span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider block">Selesai</span>
                            <span class="text-xs font-bold text-gray-700 mt-1 block">{{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Status & Review -->
                <div>
                    <h4 class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1.5">Status Pengajuan</h4>
                    @if ($leaveRequest->status === 'approved')
                        <span class="inline-flex items-center px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-extrabold rounded-lg border border-emerald-200 uppercase tracking-wider">
                            Disetujui
                        </span>
                    @elseif ($leaveRequest->status === 'rejected')
                        <span class="inline-flex items-center px-3 py-1 bg-red-50 text-red-700 text-xs font-extrabold rounded-lg border border-red-200 uppercase tracking-wider">
                            Ditolak
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 bg-amber-50 text-amber-700 text-xs font-extrabold rounded-lg border border-amber-200 uppercase tracking-wider">
                            Pending
                        </span>
                    @endif
                </div>

                <!-- Attachment -->
                @if($leaveRequest->document_path)
                    <div>
                        <h4 class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1.5">Lampiran Dokumen</h4>
                        <a href="{{ asset('storage/' . $leaveRequest->document_path) }}" target="_blank" class="inline-flex items-center justify-center bg-[#e7f0ec] hover:bg-[#d2dfd8] text-[#0a2219] border border-[#d2dfd8] px-4 py-2 rounded-xl text-xs font-bold transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Lihat Lampiran
                        </a>
                    </div>
                @endif
            </div>

            <!-- Full Width Alasan / Keterangan -->
            <div class="md:col-span-2">
                <h4 class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1.5">Alasan / Keterangan</h4>
                <p class="text-sm text-gray-700 bg-gray-50 p-4 rounded-xl border border-gray-100 leading-relaxed">{{ $leaveRequest->reason }}</p>
            </div>
            
            <!-- Review Details (If Reviewed) -->
            @if($leaveRequest->reviewed_at)
                <div class="md:col-span-2 border-t border-gray-50 pt-6">
                    <h3 class="text-xs font-extrabold text-[#0a2219] uppercase tracking-wider mb-4">Hasil Review Admin</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Tanggal Review</span>
                            <p class="text-xs text-gray-700 font-semibold mt-1">{{ \Carbon\Carbon::parse($leaveRequest->reviewed_at)->format('d M Y, H:i') }} WIB</p>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Catatan Review</span>
                            <p class="text-xs text-gray-700 font-semibold mt-1">{{ $leaveRequest->review_notes ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Action Review Form (If Pending) -->
        @if ($leaveRequest->status === 'pending')
            <div class="border-t border-gray-50 pt-8">
                <h3 class="text-sm font-extrabold text-[#0a2219] uppercase tracking-wider mb-6">Review Keputusan Pengajuan</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Form Approve -->
                    <form method="POST" action="{{ route('admin.leave-requests.approve', $leaveRequest->id) }}" class="bg-[#e7f0ec]/40 p-6 rounded-2xl border border-[#d2dfd8] flex flex-col justify-between">
                        @csrf
                        @method('PUT')
                        <div>
                            <div class="flex items-center space-x-2 text-emerald-800 font-bold text-sm mb-3">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Setujui Pengajuan Cuti</span>
                            </div>
                            <div class="mb-5">
                                <label class="block text-[10px] font-bold text-emerald-800 uppercase tracking-wider mb-1.5">Catatan Persetujuan (Opsional)</label>
                                <input type="text" name="notes" placeholder="e.g. Selamat berlibur, jaga kesehatan" class="w-full px-4 py-2.5 border border-emerald-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white transition duration-200">
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-[#0a2219] hover:bg-[#123b2c] text-white px-5 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 border border-transparent shadow-sm">
                            ✓ Setujui Cuti
                        </button>
                    </form>

                    <!-- Form Reject -->
                    <form method="POST" action="{{ route('admin.leave-requests.reject', $leaveRequest->id) }}" class="bg-red-50/50 p-6 rounded-2xl border border-red-100 flex flex-col justify-between">
                        @csrf
                        @method('PUT')
                        <div>
                            <div class="flex items-center space-x-2 text-red-800 font-bold text-sm mb-3">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Tolak Pengajuan Cuti</span>
                            </div>
                            <div class="mb-5">
                                <label class="block text-[10px] font-bold text-red-800 uppercase tracking-wider mb-1.5">Alasan Penolakan (Wajib)</label>
                                <input type="text" name="notes" required placeholder="e.g. Kuota staf berhalangan penuh" class="w-full px-4 py-2.5 border border-red-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white transition duration-200">
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-5 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 border border-transparent shadow-sm">
                            ✗ Tolak Cuti
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
