@extends('layouts.employee')

@section('title', 'Absensi')

@section('content')
    <!-- Status Hari Ini -->
    <div class="bg-gradient-to-br from-[#0a2219] to-[#123b2c] text-white rounded-2xl p-6 mb-5 border border-[#1d523e] shadow-sm flex flex-col items-center text-center">
        <h2 class="text-xs font-extrabold text-[#d4af37] uppercase tracking-widest mb-1.5">Waktu Server Realtime</h2>
        <p id="current-time" class="text-3xl font-black tracking-wider text-white">{{ now()->format('H:i:s') }}</p>
        <p class="text-[10px] text-gray-300 font-bold uppercase tracking-widest mt-1">WIB (Waktu Indonesia Barat)</p>
    </div>

    <!-- GPS Status -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
        <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">Lokasi Absensi (GPS)</h3>
        <div id="location-status" class="text-center py-4">
            <div class="animate-spin inline-block w-7 h-7 border-3 border-[#d4af37] border-t-transparent rounded-full mb-3"></div>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Mendeteksi koordinat GPS...</p>
        </div>
        @if ($office)
            <div class="border-t border-gray-50 pt-4 flex justify-between items-center text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                <span>Zona Radius Kantor:</span>
                <span class="text-[#0a2219] bg-[#e7f0ec] px-2.5 py-1 rounded-lg border border-[#d2dfd8]">{{ $office->radius_meters }} Meter</span>
            </div>
        @endif
    </div>

    <!-- Check-in/out Buttons -->
    <div class="space-y-4">
        @if (!$attendanceToday || !$attendanceToday->check_in)
            <button id="btn-checkin" onclick="checkIn()" disabled
                    class="w-full bg-gradient-to-r from-[#0a2219] to-[#123b2c] hover:from-[#123b2c] hover:to-[#0a2219] text-white py-4 rounded-xl font-bold text-sm uppercase tracking-wider disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-lg transition-all duration-200">
                📍 Absen Masuk Kerja
            </button>
            <p class="text-[10px] text-gray-400 font-bold text-center uppercase tracking-wider">Posisikan diri Anda di area kantor agar tombol aktif</p>
        @elseif (!$attendanceToday->check_out)
            <div class="bg-[#e7f0ec] border border-[#d2dfd8] p-5 rounded-2xl mb-4 space-y-2">
                <div class="flex justify-between items-center text-xs font-bold text-[#0a2219]">
                    <span class="uppercase tracking-wider">Jam Masuk Kerja</span>
                    <span class="text-sm font-extrabold bg-white px-3 py-1 rounded-lg border border-[#d2dfd8]">{{ $attendanceToday->check_in->format('H:i') }} WIB</span>
                </div>
                <div class="flex justify-between items-center text-xs font-bold text-[#0a2219] border-t border-[#d2dfd8]/50 pt-2">
                    <span class="uppercase tracking-wider">Status Ketepatan</span>
                    @if ($attendanceToday->status === 'on_time')
                        <span class="inline-flex items-center px-2 py-0.5 bg-emerald-500/10 text-emerald-700 rounded-md border border-emerald-500/20 uppercase tracking-wider">Tepat Waktu</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 bg-amber-500/10 text-amber-700 rounded-md border border-amber-500/20 uppercase tracking-wider">Terlambat</span>
                    @endif
                </div>
            </div>

            <button id="btn-checkout" onclick="checkOut()" disabled
                    class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-600 text-white py-4 rounded-xl font-bold text-sm uppercase tracking-wider disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-lg transition-all duration-200">
                📍 Absen Keluar Kerja
            </button>
        @else
            <div class="bg-[#e7f0ec] border border-[#d2dfd8] p-6 rounded-2xl text-center space-y-4 shadow-sm">
                <div class="w-12 h-12 bg-[#0a2219] text-[#d4af37] border border-[#d2dfd8] rounded-full flex items-center justify-center font-extrabold text-xl mx-auto">
                    ✓
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-[#0a2219] uppercase tracking-wider">Pencatatan Absensi Selesai</h3>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-1">Sampai jumpa di shift berikutnya!</p>
                </div>
                <div class="grid grid-cols-2 gap-4 border-t border-[#d2dfd8]/50 pt-4 text-xs font-bold text-gray-600">
                    <div class="bg-white p-3 rounded-xl border border-gray-100">
                        <span class="block text-[9px] text-gray-400 uppercase tracking-wider mb-1">Masuk</span>
                        <span class="text-sm font-extrabold text-gray-700">{{ $attendanceToday->check_in->format('H:i') }}</span>
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-gray-100">
                        <span class="block text-[9px] text-gray-400 uppercase tracking-wider mb-1">Keluar</span>
                        <span class="text-sm font-extrabold text-gray-700">{{ $attendanceToday->check_out->format('H:i') }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Navigation Links -->
    <div class="mt-8 grid grid-cols-2 gap-4">
        <a href="{{ route('employee.attendance.history') }}" class="bg-white hover:bg-gray-50 text-[#0a2219] border border-gray-100 py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider text-center transition flex items-center justify-center gap-1.5 shadow-sm">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Riwayat Absen
        </a>
        <a href="{{ route('employee.attendance.summary') }}" class="bg-white hover:bg-gray-50 text-[#0a2219] border border-gray-100 py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider text-center transition flex items-center justify-center gap-1.5 shadow-sm">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
            </svg>
            Rekap Bulanan
        </a>
    </div>
@endsection

@section('js')
    <script>
        let userLat = null, userLng = null;
        const statusEl = document.getElementById('location-status');
        const btnCheckin = document.getElementById('btn-checkin');
        const btnCheckout = document.getElementById('btn-checkout');
        const timeEl = document.getElementById('current-time');

        // Update jam real-time
        setInterval(() => {
            const now = new Date();
            timeEl.textContent = now.toLocaleTimeString('id-ID');
        }, 1000);

        // Get GPS Location
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                pos => {
                    userLat = pos.coords.latitude;
                    userLng = pos.coords.longitude;
                    const accuracy = pos.coords.accuracy;

                    statusEl.innerHTML = `
                        <div class="text-emerald-700 font-extrabold text-xs uppercase tracking-wider mb-1 flex items-center justify-center">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping mr-2"></span>
                            ✓ GPS AKTIF TERKUNCI
                        </div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Margin Akurasi: ${accuracy.toFixed(1)}m</p>
                    `;

                    if (btnCheckin) btnCheckin.disabled = false;
                    if (btnCheckout) btnCheckout.disabled = false;
                },
                err => {
                    statusEl.innerHTML = `
                        <div class="text-red-600 font-extrabold text-xs uppercase tracking-wider mb-1">
                            ✗ GPS TIDAK MAKSIMAL / MATI
                        </div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Harap aktifkan GPS & Izin Lokasi browser</p>
                    `;
                },
                { enableHighAccuracy: true, maximumAge: 10000, timeout: 5000 }
            );
        }

        async function checkIn() {
            if (!userLat || !userLng) {
                Swal.fire({
                    title: 'Gagal!',
                    text: 'GPS belum mendeteksi lokasi koordinat Anda.',
                    icon: 'warning',
                    confirmButtonColor: '#0a2219',
                    borderRadius: '1rem'
                });
                return;
            }

            btnCheckin.disabled = true;
            btnCheckin.textContent = '⏳ Memproses Absen Masuk...';

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
                    btnCheckin.disabled = false;
                    btnCheckin.textContent = '📍 Absen Masuk Kerja';
                }
            } catch (error) {
                Swal.fire({
                    title: 'Terjadi Kesalahan!',
                    text: error.message,
                    icon: 'error',
                    confirmButtonColor: '#0a2219',
                    borderRadius: '1rem'
                });
                btnCheckin.disabled = false;
                btnCheckin.textContent = '📍 Absen Masuk Kerja';
            }
        }

        async function checkOut() {
            const confirmResult = await Swal.fire({
                title: 'Konfirmasi',
                text: 'Yakin ingin melakukan absen keluar kerja sekarang?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0a2219',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Absen Keluar',
                cancelButtonText: 'Batal',
                borderRadius: '1rem'
            });

            if (!confirmResult.isConfirmed) return;

            btnCheckout.disabled = true;
            btnCheckout.textContent = '⏳ Memproses Absen Keluar...';

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
                    btnCheckout.disabled = false;
                    btnCheckout.textContent = '📍 Absen Keluar Kerja';
                }
            } catch (error) {
                Swal.fire({
                    title: 'Terjadi Kesalahan!',
                    text: error.message,
                    icon: 'error',
                    confirmButtonColor: '#0a2219',
                    borderRadius: '1rem'
                });
                btnCheckout.disabled = false;
                btnCheckout.textContent = '📍 Absen Keluar Kerja';
            }
        }
    </script>
@endsection