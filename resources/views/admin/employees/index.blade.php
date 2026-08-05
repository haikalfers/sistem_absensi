@extends('layouts.admin')

@section('title', 'Kelola Karyawan')
@section('page-title', 'Kelola Karyawan')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-gray-800 uppercase tracking-wider">Daftar Karyawan</h3>
            <p class="text-xs text-gray-500 font-semibold mt-1">Total: {{ $employees->total() }} karyawan terdaftar</p>
        </div>
        <a href="{{ route('admin.employees.create') }}" class="inline-flex items-center justify-center bg-[#0a2219] hover:bg-[#123b2c] text-white px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 border border-transparent focus:ring-2 focus:ring-[#d4af37] shadow-md shadow-emerald-950/10">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Karyawan
        </a>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] border-collapse">
                <thead>
                    <tr class="bg-[#f0f4f2] border-b border-gray-100">
                        <th class="px-6 py-4.5 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">ID / Nama</th>
                        <th class="px-6 py-4.5 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Jabatan</th>
                        <th class="px-6 py-4.5 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Departemen</th>
                        <th class="px-6 py-4.5 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Gaji Pokok</th>
                        <th class="px-6 py-4.5 text-center text-xs font-bold text-[#0a2219] uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($employees as $employee)
                        <tr class="hover:bg-[#fcfdfc] transition duration-150">
                            <!-- Code / Name -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 bg-[#e7f0ec] text-[#0a2219] rounded-xl flex items-center justify-center font-bold text-sm border border-[#d2dfd8]">
                                        {{ substr($employee->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-extrabold text-gray-400 uppercase tracking-wider">{{ $employee->employee_code }}</p>
                                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $employee->name }}</p>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Position -->
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-700 font-medium">{{ $employee->position }}</span>
                            </td>
                            
                            <!-- Department -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 bg-[#e7f0ec] text-[#0a2219] text-xs font-bold rounded-lg border border-[#d2dfd8]">
                                    {{ $employee->department }}
                                </span>
                            </td>
                            
                            <!-- Base Salary -->
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-800">
                                    Rp {{ number_format($employee->base_salary, 0, ',', '.') }}
                                </span>
                            </td>
                            
                            <!-- Actions -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-3">
                                    <a href="{{ route('admin.employees.show', $employee) }}" class="inline-flex items-center text-xs font-bold text-[#0a2219] hover:text-[#d4af37] transition duration-150" title="Lihat">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Detail
                                    </a>
                                    
                                    <a href="{{ route('admin.employees.edit', $employee) }}" class="inline-flex items-center text-xs font-bold text-amber-600 hover:text-amber-800 transition duration-150" title="Edit">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                        Edit
                                    </a>
                                    
                                    <form method="POST" action="{{ route('admin.employees.destroy', $employee) }}" class="inline-block" onsubmit="confirmAction(event, 'Yakin ingin menghapus data karyawan ini?', this);">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center text-xs font-bold text-red-500 hover:text-red-700 transition duration-150" title="Hapus">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 font-medium">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.386 11.386 0 0110.089 20.5a11.378 11.378 0 01-4.94-1.263v-.11a11.353 11.353 0 010-3.187m0 4.382v-.003c0-1.113.285-2.16.786-3.07M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm6.375 2.25a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zM13.5 8.25a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                    </svg>
                                    <span>Belum ada data karyawan terdaftar.</span>
                                    <a href="{{ route('admin.employees.create') }}" class="text-[#0a2219] hover:text-[#d4af37] font-bold text-xs uppercase tracking-wider mt-3">Tambah Karyawan Sekarang</a>
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
        {{ $employees->links() }}
    </div>
@endsection