@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Ringkasan')

@section('content')
    <!-- Stat Cards Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        
        <!-- Total Karyawan -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Karyawan</p>
                    <p class="text-3xl font-extrabold text-[#0a2219] mt-2">{{ $totalEmployees }}</p>
                    <p class="text-[10px] text-gray-500 font-semibold mt-1">Aktif terdaftar</p>
                </div>
                <div class="w-12 h-12 bg-[#e7f0ec] rounded-xl flex items-center justify-center text-[#0a2219] border border-[#d2dfd8]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.386 11.386 0 0110.089 20.5a11.378 11.378 0 01-4.94-1.263v-.11a11.353 11.353 0 010-3.187m0 4.382v-.003c0-1.113.285-2.16.786-3.07M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm6.375 2.25a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zM13.5 8.25a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Hadir Hari Ini -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Hadir Hari Ini</p>
                    <p class="text-3xl font-extrabold text-[#10b981] mt-2">{{ $presentToday }}</p>
                    <p class="text-[10px] text-gray-500 font-semibold mt-1">dari {{ $totalAttendanceToday }} absen masuk</p>
                </div>
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 border border-emerald-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Alpha Hari Ini -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Absen / Mangkir</p>
                    <p class="text-3xl font-extrabold text-red-600 mt-2">{{ $absentToday }}</p>
                    <p class="text-[10px] text-gray-500 font-semibold mt-1">dari {{ $totalEmployees }} karyawan</p>
                </div>
                <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-red-600 border border-red-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Cuti Pending -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Cuti Pending</p>
                    <p class="text-3xl font-extrabold text-amber-500 mt-2">{{ $pendingLeaveRequests }}</p>
                    <p class="text-[10px] text-gray-500 font-semibold mt-1">Butuh persetujuan</p>
                </div>
                <div class="w-12 h-12 bg-[#faf3e0] rounded-xl flex items-center justify-center text-[#8a6d1c] border border-[#f3e7c4]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        <!-- Attendance Chart (7 hari terakhir) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Statistik Kehadiran 7 Hari</h3>
                <span class="text-[10px] font-bold text-[#0a2219] bg-[#e7f0ec] px-2 py-1 rounded-md">Realtime</span>
            </div>
            
            <div class="space-y-4">
                @foreach ($attendanceChart as $data)
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 w-16">{{ $data['date'] }}</span>
                        
                        <div class="flex-1 flex items-center gap-3 mx-4">
                            <!-- Present Bar -->
                            <div class="w-full bg-gray-50 rounded-full h-3 overflow-hidden flex">
                                <div class="bg-[#10b981] h-full" style="width: {{ $data['present'] > 0 || $data['absent'] > 0 ? ($data['present'] / ($data['present'] + $data['absent'])) * 100 : 0 }}%"></div>
                                <div class="bg-red-400 h-full" style="width: {{ $data['present'] > 0 || $data['absent'] > 0 ? ($data['absent'] / ($data['present'] + $data['absent'])) * 100 : 0 }}%"></div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 text-xs w-20 justify-end">
                            <span class="text-[#10b981] font-bold">H: {{ $data['present'] }}</span>
                            <span class="text-red-500 font-bold">A: {{ $data['absent'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="flex justify-start space-x-4 mt-6 pt-4 border-t border-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                <div class="flex items-center"><span class="w-2.5 h-2.5 bg-[#10b981] rounded-full mr-1.5"></span> Hadir</div>
                <div class="flex items-center"><span class="w-2.5 h-2.5 bg-red-400 rounded-full mr-1.5"></span> Absen / Tanpa Keterangan</div>
            </div>
        </div>

        <!-- Lateness Chart -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Keterlambatan 7 Hari</h3>
                <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-md">Kasus</span>
            </div>
            
            <div class="space-y-4">
                @php
                    $maxLate = collect($lateChart)->max('late') ?: 1;
                @endphp
                @foreach ($lateChart as $data)
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 w-16">{{ $data['date'] }}</span>
                        
                        <div class="flex-1 mx-4">
                            <div class="w-full bg-gray-50 rounded-full h-3 overflow-hidden">
                                <div class="bg-amber-400 h-full rounded-full" style="width: {{ ($data['late'] / $maxLate) * 100 }}%"></div>
                            </div>
                        </div>
                        
                        <span class="text-xs font-extrabold text-amber-500 w-8 text-right">{{ $data['late'] }} Karyawan</span>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-start mt-6 pt-4 border-t border-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                <div class="flex items-center"><span class="w-2.5 h-2.5 bg-amber-400 rounded-full mr-1.5"></span> Terlambat (Menit masuk lewat dari toleransi)</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-5">Menu Aksi Cepat</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <a href="{{ route('admin.employees.create') }}" class="group flex items-center p-4 bg-[#e7f0ec] rounded-xl hover:bg-[#d8e7e1] transition duration-150 border border-[#d2dfd8]/50">
                <div class="w-10 h-10 bg-[#0a2219] text-white rounded-lg flex items-center justify-center mr-3 group-hover:scale-105 transition duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <div>
                    <span class="block text-xs font-bold text-[#0a2219] uppercase tracking-wider">Tambah Karyawan</span>
                    <span class="text-[10px] text-gray-500 font-semibold">Registrasi staf baru</span>
                </div>
            </a>

            <a href="{{ route('admin.attendance.index') }}" class="group flex items-center p-4 bg-[#faf3e0] rounded-xl hover:bg-[#f3e7c4] transition duration-150 border border-[#f3e7c4]/50">
                <div class="w-10 h-10 bg-[#8a6d1c] text-white rounded-lg flex items-center justify-center mr-3 group-hover:scale-105 transition duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <span class="block text-xs font-bold text-[#8a6d1c] uppercase tracking-wider">Monitor Absensi</span>
                    <span class="text-[10px] text-gray-500 font-semibold">Tinjau kehadiran harian</span>
                </div>
            </a>

            <a href="{{ route('admin.payrolls.create') }}" class="group flex items-center p-4 bg-orange-50 rounded-xl hover:bg-orange-100/70 transition duration-150 border border-orange-100">
                <div class="w-10 h-10 bg-orange-600 text-white rounded-lg flex items-center justify-center mr-3 group-hover:scale-105 transition duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="block text-xs font-bold text-orange-700 uppercase tracking-wider">Buat Payroll</span>
                    <span class="text-[10px] text-gray-500 font-semibold">Gaji otomatis & slip</span>
                </div>
            </a>

            <a href="{{ route('admin.leave-requests.index') }}" class="group flex items-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100/70 transition duration-150 border border-blue-100">
                <div class="w-10 h-10 bg-blue-600 text-white rounded-lg flex items-center justify-center mr-3 group-hover:scale-105 transition duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="block text-xs font-bold text-blue-700 uppercase tracking-wider">Validasi Cuti</span>
                    <span class="text-[10px] text-gray-500 font-semibold">Setujui permohonan cuti</span>
                </div>
            </a>

        </div>
    </div>
@endsection