@extends('layouts.admin')

@section('title', 'Laporan Absensi')
@section('page-title', 'Laporan Absensi Karyawan')

@section('content')
<div class="mb-6 bg-white rounded-lg shadow p-4">
    <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.reports.attendance') }}" id="filterForm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
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
        <h3 class="font-bold text-gray-800">Preview Data Absensi</h3>
        <span class="text-sm text-gray-500">Menampilkan {{ $attendances->count() }} data per halaman</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 font-semibold text-gray-700">Tanggal</th>
                    <th class="px-6 py-3 font-semibold text-gray-700">Kode Karyawan</th>
                    <th class="px-6 py-3 font-semibold text-gray-700">Nama</th>
                    <th class="px-6 py-3 font-semibold text-gray-700">Departemen</th>
                    <th class="px-6 py-3 font-semibold text-gray-700 text-center">Jam Masuk</th>
                    <th class="px-6 py-3 font-semibold text-gray-700 text-center">Jam Keluar</th>
                    <th class="px-6 py-3 font-semibold text-gray-700 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($attendances as $attendance)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3 text-gray-700">{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>
                        <td class="px-6 py-3 text-gray-700">{{ $attendance->employee->employee_code }}</td>
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $attendance->employee->name }}</td>
                        <td class="px-6 py-3 text-gray-700">{{ $attendance->employee->department }}</td>
                        <td class="px-6 py-3 text-center text-gray-700">{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}</td>
                        <td class="px-6 py-3 text-center text-gray-700">{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}</td>
                        <td class="px-6 py-3 text-center">
                            @if ($attendance->status === 'on_time')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">Tepat Waktu</span>
                            @elseif ($attendance->status === 'late')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-semibold">Terlambat</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-semibold">Alpha/Absen</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada data absensi untuk periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $attendances->links() }}
</div>

<script>
    function exportExcel() {
        const form = document.getElementById('filterForm');
        const url = new URL("{{ route('admin.reports.attendance.export') }}", window.location.origin);
        
        const dateFrom = form.elements['date_from'].value;
        const dateTo = form.elements['date_to'].value;
        
        if (dateFrom) url.searchParams.append('date_from', dateFrom);
        if (dateTo) url.searchParams.append('date_to', dateTo);
        
        window.location.href = url.toString();
    }
</script>
@endsection
