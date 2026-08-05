@extends('layouts.admin')

@section('title', 'Kelola Penggajian')
@section('page-title', 'Kelola Penggajian')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-gray-800 uppercase tracking-wider">Daftar Rekapitulasi Payroll</h3>
            <p class="text-xs text-gray-500 font-semibold mt-1">Total: {{ $payrolls->total() }} periode penggajian</p>
        </div>
        <a href="{{ route('admin.payrolls.create') }}" class="inline-flex items-center justify-center bg-[#0a2219] hover:bg-[#123b2c] text-white px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 border border-transparent focus:ring-2 focus:ring-[#d4af37] shadow-md shadow-emerald-950/10">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Buat Payroll Baru
        </a>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] border-collapse">
                <thead>
                    <tr class="bg-[#f0f4f2] border-b border-gray-100">
                        <th class="px-6 py-4.5 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-4.5 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Tanggal Kerja</th>
                        <th class="px-6 py-4.5 text-center text-xs font-bold text-[#0a2219] uppercase tracking-wider">Jumlah Staf</th>
                        <th class="px-6 py-4.5 text-center text-xs font-bold text-[#0a2219] uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4.5 text-center text-xs font-bold text-[#0a2219] uppercase tracking-wider">Dibuat Oleh</th>
                        <th class="px-6 py-4.5 text-center text-xs font-bold text-[#0a2219] uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($payrolls as $payroll)
                        <tr class="hover:bg-[#fcfdfc] transition duration-150">
                            <!-- Period Name -->
                            <td class="px-6 py-4 text-sm font-bold text-gray-800">
                                {{ $payroll->period_name }}
                            </td>
                            
                            <!-- Date Range -->
                            <td class="px-6 py-4 text-xs font-semibold text-gray-600">
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $payroll->period_start->format('d M Y') }} - {{ $payroll->period_end->format('d M Y') }}
                                </span>
                            </td>
                            
                            <!-- Total Employees Count -->
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 bg-gray-50 border border-gray-200 text-gray-700 text-xs font-bold rounded-lg">
                                    {{ $payroll->details->count() }} Orang
                                </span>
                            </td>
                            
                            <!-- Status -->
                            <td class="px-6 py-4 text-center">
                                @if ($payroll->status === 'draft')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-700 text-[10px] font-extrabold rounded-lg border border-amber-200 uppercase tracking-wider">
                                        Draft
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-extrabold rounded-lg border border-emerald-200 uppercase tracking-wider">
                                        Finalized
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Created By -->
                            <td class="px-6 py-4 text-center text-xs text-gray-500 font-semibold">
                                {{ $payroll->createdBy->name }}
                            </td>
                            
                            <!-- Actions -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-3.5">
                                    <a href="{{ route('admin.payrolls.show', $payroll) }}" class="inline-flex items-center text-xs font-bold text-[#0a2219] hover:text-[#d4af37] transition duration-150" title="Lihat">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Detail
                                    </a>
                                    
                                    @if ($payroll->status === 'draft')
                                        <a href="{{ route('admin.payrolls.edit', $payroll) }}" class="inline-flex items-center text-xs font-bold text-amber-600 hover:text-amber-800 transition duration-150" title="Edit">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                            Edit
                                        </a>
                                        
                                        <form method="POST" action="{{ route('admin.payrolls.generate', $payroll) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-800 transition duration-150" onclick="confirmAction(event, 'Generate payroll untuk semua karyawan?', this);" title="Hitung Gaji">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Generate
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.payrolls.destroy', $payroll) }}" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center text-xs font-bold text-red-600 hover:text-red-800 transition duration-150" onclick="confirmAction(event, 'Apakah Anda yakin ingin menghapus draft payroll ini? Data tidak dapat dikembalikan.', this);" title="Hapus">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.payrolls.export-pdf', $payroll) }}" class="inline-flex items-center text-xs font-bold text-red-500 hover:text-red-700 transition duration-150" title="Unduh PDF">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            PDF
                                        </a>
                                        <a href="{{ route('admin.payrolls.edit', $payroll) }}" class="inline-flex items-center text-xs font-bold text-amber-600 hover:text-amber-800 transition duration-150" title="Edit Bonus/Potongan">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                            Edit
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 font-medium">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5M3.75 20.25zM3.75 20.25H21M3.75 20.25zM21 4.5V18.75m0-14.25a8.997 8.997 0 00-6.002-3.364M21 4.5v14.25m0-14.25a8.997 8.997 0 01-6.002-3.364M18.75 9.75A3.75 3.75 0 0015 6a3.75 3.75 0 00-3.75 3.75A3.75 3.75 0 0015 13.5a3.75 3.75 0 003.75-3.75z" />
                                    </svg>
                                    <span>Belum ada data periode payroll terdaftar.</span>
                                    <a href="{{ route('admin.payrolls.create') }}" class="text-[#0a2219] hover:text-[#d4af37] font-bold text-xs uppercase tracking-wider mt-3">Buat Payroll Pertama</a>
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
        {{ $payrolls->links() }}
    </div>
@endsection