@extends('layouts.admin')

@section('title', 'Laporan Cuti')
@section('page-title', 'Laporan Cuti & Izin Karyawan')

@section('content')
<div class="mb-6 bg-white rounded-lg shadow p-4">
    <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.reports.leave') }}" id="filterForm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status Pengajuan</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">-- Semua Status --</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition w-full md:w-auto">
                    Preview Data
                </button>
                <button type="button" onclick="exportExcel()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition w-full md:w-auto flex items-center gap-1">
                    <span>Export Excel</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Table Preview -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Preview Data Cuti</h3>
        <span class="text-sm text-gray-500">Menampilkan {{ $leaveRequests->count() }} data per halaman</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 font-semibold text-gray-700">Tgl Pengajuan</th>
                    <th class="px-6 py-3 font-semibold text-gray-700">Karyawan</th>
                    <th class="px-6 py-3 font-semibold text-gray-700">Jenis Cuti</th>
                    <th class="px-6 py-3 font-semibold text-gray-700 text-center">Periode</th>
                    <th class="px-6 py-3 font-semibold text-gray-700 text-center">Lama Cuti</th>
                    <th class="px-6 py-3 font-semibold text-gray-700 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($leaveRequests as $leave)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3 text-gray-700">{{ $leave->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $leave->employee->name }}</td>
                        <td class="px-6 py-3 text-gray-700">{{ $leave->leaveType->name }}</td>
                        <td class="px-6 py-3 text-center text-gray-700">
                            {{ \Carbon\Carbon::parse($leave->start_date)->format('d M y') }} - 
                            {{ \Carbon\Carbon::parse($leave->end_date)->format('d M y') }}
                        </td>
                        <td class="px-6 py-3 text-center text-gray-700">{{ $leave->total_days }} Hari</td>
                        <td class="px-6 py-3 text-center">
                            @if ($leave->status === 'approved')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">Disetujui</span>
                            @elseif ($leave->status === 'rejected')
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-semibold">Ditolak</span>
                            @else
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-semibold">Pending</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada data pengajuan cuti untuk filter ini.
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

<script>
    function exportExcel() {
        const form = document.getElementById('filterForm');
        const url = new URL("{{ route('admin.reports.leave.export') }}", window.location.origin);
        
        const status = form.elements['status'].value;
        if (status) url.searchParams.append('status', status);
        
        window.location.href = url.toString();
    }
</script>
@endsection
