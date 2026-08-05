@extends('layouts.admin')

@section('title', 'Laporan Penggajian')
@section('page-title', 'Laporan Penggajian Karyawan')

@section('content')
<div class="mb-6 bg-white rounded-lg shadow p-4">
    <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.reports.payroll') }}" id="filterForm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Periode Penggajian</label>
                <select name="payroll_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">-- Semua Periode --</option>
                    @foreach($payrolls as $payroll)
                        <option value="{{ $payroll->id }}" {{ request('payroll_id') == $payroll->id ? 'selected' : '' }}>
                            {{ $payroll->period_name }} ({{ \Carbon\Carbon::parse($payroll->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($payroll->end_date)->format('d M Y') }})
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
        <h3 class="font-bold text-gray-800">Preview Data Penggajian</h3>
        <span class="text-sm text-gray-500">Menampilkan {{ $details->count() }} data per halaman</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 font-semibold text-gray-700">Periode</th>
                    <th class="px-6 py-3 font-semibold text-gray-700">Karyawan</th>
                    <th class="px-6 py-3 font-semibold text-gray-700 text-right">Gaji Pokok</th>
                    <th class="px-6 py-3 font-semibold text-gray-700 text-right">Lembur</th>
                    <th class="px-6 py-3 font-semibold text-gray-700 text-right">Tunjangan</th>
                    <th class="px-6 py-3 font-semibold text-gray-700 text-right">Potongan (PPh/BPJS)</th>
                    <th class="px-6 py-3 font-semibold text-gray-700 text-right">Gaji Bersih</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($details as $detail)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3 text-gray-700">{{ $detail->payroll->period_name }}</td>
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $detail->employee->name }}</td>
                        <td class="px-6 py-3 text-gray-700 text-right">Rp {{ number_format($detail->base_salary, 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-gray-700 text-right">Rp {{ number_format($detail->overtime_total, 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-gray-700 text-right">Rp {{ number_format($detail->meal_allowance, 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-red-600 text-right">- Rp {{ number_format($detail->pph21 + $detail->bpjs_tk + $detail->bpjs_kes, 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-green-700 font-bold text-right">Rp {{ number_format($detail->net_salary, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada data penggajian untuk filter ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $details->links() }}
</div>

<script>
    function exportExcel() {
        const form = document.getElementById('filterForm');
        const url = new URL("{{ route('admin.reports.payroll.export') }}", window.location.origin);
        
        const payrollId = form.elements['payroll_id'].value;
        if (payrollId) url.searchParams.append('payroll_id', payrollId);
        
        window.location.href = url.toString();
    }
</script>
@endsection
