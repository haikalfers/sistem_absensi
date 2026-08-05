@extends('layouts.employee')

@section('title', 'Dashboard')

@section('content')
    <!-- Status Hari Ini -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
        <h2 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">Status Hari Ini</h2>
        <div class="grid grid-cols-2 gap-4">
            <!-- Status Kehadiran -->
            <div class="bg-[#e7f0ec] p-4 rounded-xl border border-[#d2dfd8]">
                <p class="text-[10px] font-bold text-[#0a2219] uppercase tracking-wider">Status Absensi</p>
                <p class="text-lg font-extrabold text-[#0a2219] mt-1.5">
                    @if ($stats['status_hari_ini'] === 'on_time')
                        ✓ Tepat Waktu
                    @elseif ($stats['status_hari_ini'] === 'late')
                        ⚠ Terlambat
                    @elseif ($stats['status_hari_ini'] === 'absent')
                        ✗ Alpha
                    @else
                        Belum Absen
                    @endif
                </p>
            </div>
            <!-- Cuti -->
            <div class="bg-[#faf3e0] p-4 rounded-xl border border-[#f3e7c4]">
                <p class="text-[10px] font-bold text-[#8a6d1c] uppercase tracking-wider">Sisa Cuti Tahunan</p>
                <p class="text-lg font-extrabold text-[#8a6d1c] mt-1.5">{{ $stats['sisa_cuti_tahunan'] }} Hari</p>
            </div>
        </div>
    </div>

    <!-- Quick Check-in/out -->
    @if (!$attendanceToday || !$attendanceToday->check_in)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
            <h2 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">Pencatatan Masuk</h2>
            <button onclick="checkIn()" class="w-full bg-gradient-to-r from-[#0a2219] to-[#123b2c] hover:from-[#123b2c] hover:to-[#0a2219] text-white py-3.5 rounded-xl font-bold text-sm uppercase tracking-wider transition duration-200 shadow-md shadow-emerald-950/10">
                📍 Absen Masuk
            </button>
        </div>
    @elseif (!$attendanceToday->check_out)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
            <h2 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">Pencatatan Keluar</h2>
            <div class="bg-[#e7f0ec]/40 p-4 rounded-xl border border-[#d2dfd8] mb-4 text-xs font-semibold text-gray-700 flex justify-between items-center">
                <span>Jam Absen Masuk Anda:</span>
                <span class="text-sm font-extrabold text-[#0a2219] bg-[#e7f0ec] px-3 py-1 rounded-lg border border-[#d2dfd8]">{{ $attendanceToday->check_in->format('H:i') }} WIB</span>
            </div>
            <button onclick="checkOut()" class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-600 text-white py-3.5 rounded-xl font-bold text-sm uppercase tracking-wider transition duration-200 shadow-md shadow-red-950/10">
                📍 Absen Keluar
            </button>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
            <h2 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">Pencatatan Hari Ini Selesai</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-[#e7f0ec] p-3 rounded-xl border border-[#d2dfd8] text-center">
                    <p class="text-[9px] text-[#0a2219] font-bold uppercase tracking-wider mb-1">Jam Masuk</p>
                    <p class="text-base font-extrabold text-[#0a2219]">{{ $attendanceToday->check_in->format('H:i') }}</p>
                </div>
                <div class="bg-red-50 p-3 rounded-xl border border-red-100 text-center">
                    <p class="text-[9px] text-red-700 font-bold uppercase tracking-wider mb-1">Jam Keluar</p>
                    <p class="text-base font-extrabold text-red-700">{{ $attendanceToday->check_out->format('H:i') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics This Month -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
        <h2 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">Statistik Bulan Ini</h2>
        <div class="space-y-4">
            <!-- Hadir -->
            <div class="flex items-center justify-between text-xs font-bold text-gray-700">
                <span class="w-16">✓ Hadir</span>
                <div class="flex-1 mx-4 bg-gray-50 rounded-full h-2.5 overflow-hidden border border-gray-100">
                    <div class="bg-emerald-500 h-full rounded-full" style="width: {{ ($stats['hadir_bulan_ini'] / 22) * 100 }}%"></div>
                </div>
                <span class="text-emerald-600 w-6 text-right">{{ $stats['hadir_bulan_ini'] }}</span>
            </div>
            <!-- Terlambat -->
            <div class="flex items-center justify-between text-xs font-bold text-gray-700">
                <span class="w-16">⚠ Lambat</span>
                <div class="flex-1 mx-4 bg-gray-50 rounded-full h-2.5 overflow-hidden border border-gray-100">
                    <div class="bg-[#d4af37] h-full rounded-full" style="width: {{ ($stats['terlambat_bulan_ini'] / 22) * 100 }}%"></div>
                </div>
                <span class="text-[#8a6d1c] w-6 text-right">{{ $stats['terlambat_bulan_ini'] }}</span>
            </div>
            <!-- Alpha -->
            <div class="flex items-center justify-between text-xs font-bold text-gray-700">
                <span class="w-16">✗ Alpha</span>
                <div class="flex-1 mx-4 bg-gray-50 rounded-full h-2.5 overflow-hidden border border-gray-100">
                    <div class="bg-red-500 h-full rounded-full" style="width: {{ ($stats['alpha_bulan_ini'] / 22) * 100 }}%"></div>
                </div>
                <span class="text-red-600 w-6 text-right">{{ $stats['alpha_bulan_ini'] }}</span>
            </div>
        </div>
    </div>

    <!-- Pending Leave Requests -->
    @if ($pendingLeaveRequests > 0)
        <div class="bg-[#faf3e0] border border-[#f3e7c4] text-[#8a6d1c] p-4 rounded-2xl mb-5 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
            <div class="flex items-center space-x-2 text-xs font-bold">
                <svg class="w-5 h-5 text-[#8a6d1c] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>📋 {{ $pendingLeaveRequests }} pengajuan cuti Anda sedang ditinjau.</span>
            </div>
            <a href="{{ route('employee.leave-requests.index') }}" class="text-xs font-extrabold uppercase tracking-wider text-[#0a2219] hover:text-[#123b2c] shrink-0">
                Lihat Detail →
            </a>
        </div>
    @endif

    <!-- Latest Payslip -->
    @if ($latestPayslip)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
            <h2 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">Payslip Terakhir</h2>
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-2.5 text-xs font-semibold text-gray-600">
                <div class="flex justify-between">
                    <span>Periode Slip</span>
                    <span class="text-gray-800 font-bold">{{ $latestPayslip->payroll->period_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Gaji Pokok</span>
                    <span class="text-gray-800 font-bold">Rp {{ number_format($latestPayslip->base_salary, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Potongan Pajak & BPJS</span>
                    <span class="text-red-500">
                        - Rp {{ number_format($latestPayslip->base_salary + $latestPayslip->meal_allowance + $latestPayslip->overtime_total - $latestPayslip->net_salary, 0, ',', '.') }}
                    </span>
                </div>
                <div class="border-t border-gray-200 pt-2.5 flex justify-between items-center text-sm">
                    <span class="text-gray-800 font-bold">Gaji Bersih Diterima</span>
                    <span class="text-emerald-700 font-extrabold">Rp {{ number_format($latestPayslip->net_salary, 0, ',', '.') }}</span>
                </div>
            </div>
            
            <div class="mt-4 text-right">
                <a href="{{ route('employee.payslip.show', $latestPayslip->id) }}" class="inline-flex items-center text-xs font-bold text-[#0a2219] hover:text-[#d4af37] uppercase tracking-wider transition">
                    Rincian & Slip PDF
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>
    @endif
@endsection

@section('js')
    <script>
        let userLat = null, userLng = null;

        // Get GPS Location
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                pos => {
                    userLat = pos.coords.latitude;
                    userLng = pos.coords.longitude;
                },
                err => console.error('GPS Error:', err),
                { enableHighAccuracy: true, maximumAge: 10000 }
            );
        }

        async function checkIn() {
            if (!userLat || !userLng) {
                Swal.fire({
                    title: 'Gagal!',
                    text: 'GPS tidak terdeteksi. Pastikan izin lokasi HP Anda aktif.',
                    icon: 'warning',
                    confirmButtonColor: '#0a2219',
                    borderRadius: '1rem'
                });
                return;
            }

            try {
                const response = await fetch('{{ route("employee.attendance.check-in") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        latitude: userLat,
                        longitude: userLng
                    })
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#0a2219',
                        borderRadius: '1rem'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonColor: '#0a2219',
                        borderRadius: '1rem'
                    });
                }
            } catch (error) {
                Swal.fire({
                    title: 'Terjadi Kesalahan!',
                    text: error.message,
                    icon: 'error',
                    confirmButtonColor: '#0a2219',
                    borderRadius: '1rem'
                });
            }
        }

        async function checkOut() {
            try {
                const response = await fetch('{{ route("employee.attendance.check-out") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        latitude: userLat || 0,
                        longitude: userLng || 0
                    })
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#0a2219',
                        borderRadius: '1rem'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonColor: '#0a2219',
                        borderRadius: '1rem'
                    });
                }
            } catch (error) {
                Swal.fire({
                    title: 'Terjadi Kesalahan!',
                    text: error.message,
                    icon: 'error',
                    confirmButtonColor: '#0a2219',
                    borderRadius: '1rem'
                });
            }
        }
    </script>
@endsection