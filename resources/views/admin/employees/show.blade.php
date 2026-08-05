@extends('layouts.admin')

@section('title', 'Detail Karyawan')
@section('page-title', 'Profil Karyawan')

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 max-w-4xl mx-auto">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 border-b border-gray-50 pb-6">
        <div>
            <span class="text-[10px] font-extrabold text-[#d4af37] tracking-widest uppercase">Detail Karyawan</span>
            <h2 class="text-2xl font-extrabold text-[#0a2219] mt-1">{{ $employee->name }}</h2>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.employees.index') }}" class="inline-flex items-center text-xs font-bold text-gray-400 hover:text-[#0a2219] uppercase tracking-wider transition duration-150">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali
            </a>
            
            <a href="{{ route('admin.employees.edit', $employee) }}" class="inline-flex items-center justify-center bg-[#0a2219] hover:bg-[#123b2c] text-white px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 border border-transparent focus:ring-2 focus:ring-[#d4af37] shadow-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
                Edit Profil
            </a>
        </div>
    </div>

    <!-- Main Content Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Informasi Akun -->
        <div class="space-y-5">
            <div class="border-b border-gray-50 pb-2">
                <h3 class="text-sm font-bold text-[#0a2219] uppercase tracking-wider">Informasi Akun</h3>
            </div>
            
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <h4 class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">ID / Kode Karyawan</h4>
                    <p class="text-sm font-bold text-[#0a2219] mt-0.5">{{ $employee->employee_code }}</p>
                </div>
                <div>
                    <h4 class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Nama Lengkap</h4>
                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $employee->name }}</p>
                </div>
                <div>
                    <h4 class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Alamat Email</h4>
                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $employee->user->email ?? '-' }}</p>
                </div>
                <div>
                    <h4 class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Nomor Telepon</h4>
                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $employee->phone ?? '-' }}</p>
                </div>
                <div>
                    <h4 class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Alamat Tinggal</h4>
                    <p class="text-sm font-semibold text-gray-800 mt-0.5 leading-relaxed">{{ $employee->address ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Informasi Pekerjaan -->
        <div class="space-y-5">
            <div class="border-b border-gray-50 pb-2">
                <h3 class="text-sm font-bold text-[#0a2219] uppercase tracking-wider">Informasi Pekerjaan</h3>
            </div>
            
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <h4 class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Departemen / Lokasi Kantor</h4>
                    <p class="text-sm font-bold text-[#0a2219] mt-0.5">{{ $employee->department }}</p>
                </div>
                <div>
                    <h4 class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Divisi Kerja</h4>
                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $employee->division ?? '-' }}</p>
                </div>
                <div>
                    <h4 class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Jabatan Staf</h4>
                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $employee->position }}</p>
                </div>
                <div>
                    <h4 class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Gaji Pokok bulanan</h4>
                    <p class="text-sm font-bold text-emerald-600 mt-0.5">Rp {{ number_format($employee->base_salary, 0, ',', '.') }}</p>
                </div>
                <div>
                    <h4 class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Mulai Bergabung</h4>
                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('d F Y') : '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Status & Cuti (Sage Green Theme Card) -->
        <div class="md:col-span-2 mt-4 bg-[#e7f0ec] p-6 rounded-2xl border border-[#d2dfd8] grid grid-cols-1 sm:grid-cols-3 gap-6 text-center shadow-sm">
            <div>
                <h4 class="text-[10px] font-bold text-[#0a2219] uppercase tracking-wider mb-1">Sisa Cuti Tahunan</h4>
                <p class="text-3xl font-extrabold text-[#d4af37]">{{ $employee->annual_leave_balance ?? 12 }} <span class="text-xs font-semibold text-gray-500">Hari</span></p>
            </div>
            <div>
                <h4 class="text-[10px] font-bold text-[#0a2219] uppercase tracking-wider mb-1">Status Keaktifan</h4>
                <span class="inline-flex items-center px-3 py-1 mt-2.5 bg-emerald-500/10 text-emerald-700 text-xs font-extrabold rounded-lg border border-emerald-500/20 uppercase tracking-widest">
                    Aktif Kerja
                </span>
            </div>
            <div>
                <h4 class="text-[10px] font-bold text-[#0a2219] uppercase tracking-wider mb-1">Tanggal Terdaftar</h4>
                <p class="text-xs font-bold text-gray-600 mt-3">{{ $employee->created_at->format('d M Y') }}</p>
            </div>
        </div>
        
    </div>
</div>
@endsection
