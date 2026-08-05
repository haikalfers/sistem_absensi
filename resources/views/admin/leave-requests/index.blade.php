@extends('layouts.admin')

@section('title', 'Kelola Pengajuan Cuti & Izin')
@section('page-title', 'Kelola Pengajuan Cuti & Izin')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-gray-800 uppercase tracking-wider">Daftar Pengajuan Cuti & Izin</h3>
            <p class="text-xs text-gray-500 font-semibold mt-1">Total: {{ $leaveRequests->total() }} pengajuan terdaftar</p>
        </div>
    </div>

    <!-- Filter Form Container -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('admin.leave-requests.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Filter Status</label>
                    <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] bg-white transition duration-200">
                        <option value="">-- Semua Status --</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full md:w-auto bg-[#0a2219] hover:bg-[#123b2c] text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 border border-transparent focus:ring-2 focus:ring-[#d4af37] shadow-sm">
                        Filter
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
                        <th class="px-6 py-4.5 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Karyawan</th>
                        <th class="px-6 py-4.5 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Jenis Cuti</th>
                        <th class="px-6 py-4.5 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Tanggal Pelaksanaan</th>
                        <th class="px-6 py-4.5 text-center text-xs font-bold text-[#0a2219] uppercase tracking-wider">Durasi</th>
                        <th class="px-6 py-4.5 text-center text-xs font-bold text-[#0a2219] uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4.5 text-center text-xs font-bold text-[#0a2219] uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($leaveRequests as $leave)
                        <tr class="hover:bg-[#fcfdfc] transition duration-150">
                            <!-- Employee Name -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 bg-[#e7f0ec] text-[#0a2219] rounded-xl flex items-center justify-center font-bold text-sm border border-[#d2dfd8]">
                                        {{ substr($leave->employee->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">{{ $leave->employee->name }}</p>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $leave->employee->employee_code }}</p>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Leave Type -->
                            <td class="px-6 py-4 text-sm font-medium text-gray-700">
                                {{ $leave->leaveType->name }}
                            </td>
                            
                            <!-- Date Range -->
                            <td class="px-6 py-4 text-sm text-gray-600 font-semibold">
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M Y') }}
                                </span>
                            </td>
                            
                            <!-- Total Days -->
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 bg-gray-50 border border-gray-200 text-gray-700 text-xs font-bold rounded-lg">
                                    {{ $leave->total_days }} Hari
                                </span>
                            </td>
                            
                            <!-- Status -->
                            <td class="px-6 py-4 text-center">
                                @if ($leave->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-extrabold rounded-lg border border-amber-200 uppercase tracking-wider">
                                        Pending
                                    </span>
                                @elseif ($leave->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-extrabold rounded-lg border border-emerald-200 uppercase tracking-wider">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 bg-red-50 text-red-700 text-xs font-extrabold rounded-lg border border-red-200 uppercase tracking-wider">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Actions -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-3.5">
                                    <a href="{{ route('admin.leave-requests.show', $leave) }}" class="inline-flex items-center text-xs font-bold text-[#0a2219] hover:text-[#d4af37] transition duration-150" title="Lihat Detail">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Detail
                                    </a>
                                    
                                    @if ($leave->status === 'pending')
                                        <button type="button" onclick="approveLeave({{ $leave->id }})" class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-800 transition duration-150" title="Setujui">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Setujui
                                        </button>
                                        <button type="button" onclick="rejectLeave({{ $leave->id }})" class="inline-flex items-center text-xs font-bold text-red-500 hover:text-red-700 transition duration-150" title="Tolak">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Tolak
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 font-medium">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span>Tidak ada pengajuan cuti & izin.</span>
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
        {{ $leaveRequests->links() }}
    </div>

    <!-- Approve/Reject Form (Hidden) -->
    <form id="approveForm" method="POST" style="display:none;">
        @csrf
        @method('PUT')
    </form>

    <script>
        function approveLeave(leaveId) {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Setujui pengajuan cuti ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0a2219',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal',
                borderRadius: '1rem'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('approveForm');
                    form.action = `/admin/leave-requests/${leaveId}/approve`;
                    form.submit();
                }
            });
        }

        function rejectLeave(leaveId) {
            Swal.fire({
                title: 'Tolak Pengajuan',
                text: 'Masukkan alasan penolakan:',
                input: 'textarea',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0a2219',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Tolak',
                cancelButtonText: 'Batal',
                borderRadius: '1rem',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Alasan penolakan wajib diisi!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('approveForm');
                    let notesInput = form.querySelector('input[name="notes"]');
                    if (!notesInput) {
                        notesInput = document.createElement('input');
                        notesInput.type = 'hidden';
                        notesInput.name = 'notes';
                        form.appendChild(notesInput);
                    }
                    notesInput.value = result.value;
                    form.action = `/admin/leave-requests/${leaveId}/reject`;
                    form.submit();
                }
            });
        }
    </script>
@endsection