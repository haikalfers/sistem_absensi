@extends('layouts.employee')

@section('title', 'Absensi')

@section('content')
    {{-- Status Waktu Server --}}
    <div class="bg-gradient-to-br from-[#0a2219] to-[#123b2c] text-white rounded-2xl p-6 mb-5 border border-[#1d523e] shadow-sm flex flex-col items-center text-center">
        <h2 class="text-xs font-extrabold text-[#d4af37] uppercase tracking-widest mb-1.5">Waktu Server Realtime</h2>
        <p id="current-time" class="text-3xl font-black tracking-wider text-white">{{ now()->format('H:i:s') }}</p>
        <p class="text-[10px] text-gray-300 font-bold uppercase tracking-widest mt-1">WIB (Waktu Indonesia Barat)</p>
    </div>

    {{-- GPS Status --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
        <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">Lokasi Absensi (GPS)</h3>
        <div id="location-status" class="text-center py-3">
            <div class="animate-spin inline-block w-6 h-6 border-2 border-[#d4af37] border-t-transparent rounded-full mb-2"></div>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Mendeteksi koordinat GPS...</p>
        </div>
        @if ($office)
            <div class="border-t border-gray-50 pt-4 flex justify-between items-center text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                <span>Zona Radius Kantor:</span>
                <span class="text-[#0a2219] bg-[#e7f0ec] px-2.5 py-1 rounded-lg border border-[#d2dfd8]">{{ $office->radius_meters }} Meter</span>
            </div>
        @endif
    </div>

    {{-- Selfie Capture Section — hanya tampil jika belum check-in --}}
    @if (!$attendanceToday || !$attendanceToday->check_in)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5" id="selfie-section">
            <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">Foto Selfie (Bukti Kehadiran)</h3>

            {{-- Camera View --}}
            <div id="camera-container" class="relative">
                <video id="camera-video" autoplay playsinline muted
                       class="w-full rounded-xl bg-gray-900 aspect-square object-cover hidden"></video>
                <canvas id="camera-canvas" class="hidden"></canvas>

                {{-- Placeholder saat kamera belum aktif --}}
                <div id="camera-placeholder" class="w-full aspect-square rounded-xl bg-gray-100 border-2 border-dashed border-gray-200 flex flex-col items-center justify-center">
                    <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Kamera belum aktif</p>
                </div>

                {{-- Foto yang sudah diambil --}}
                <div id="selfie-preview" class="hidden relative">
                    <img id="selfie-img" src="" alt="Selfie" class="w-full rounded-xl aspect-square object-cover border-4 border-emerald-400"/>
                    <div class="absolute top-2 right-2">
                        <span class="bg-emerald-500 text-white text-[10px] font-extrabold px-2 py-1 rounded-lg uppercase tracking-wider">✓ Foto Terkunci</span>
                    </div>
                </div>
            </div>

            {{-- Selfie Controls --}}
            <div class="mt-4 space-y-2" id="selfie-controls">
                <button id="btn-open-camera" onclick="openCamera()"
                        class="w-full bg-[#0a2219] hover:bg-[#123b2c] text-white py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Buka Kamera
                </button>

                <button id="btn-take-photo" onclick="takePhoto()" class="hidden w-full bg-[#d4af37] hover:bg-[#c8a02e] text-[#0a2219] py-3 rounded-xl font-extrabold text-xs uppercase tracking-wider transition items-center justify-center gap-2">
                    📸 Ambil Foto
                </button>

                <button id="btn-retake" onclick="retakePhoto()" class="hidden w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition">
                    🔄 Ambil Ulang
                </button>
            </div>

            <p class="text-[10px] text-gray-400 text-center mt-3 font-semibold">Foto diambil <strong>live dari kamera</strong> — tidak bisa upload dari galeri.<br>Foto akan disimpan sebagai bukti kehadiran selama <strong>7 hari</strong>.</p>
        </div>
    @endif

    {{-- Check-in/out Buttons --}}
    <div class="space-y-4">
        @if (!$attendanceToday || !$attendanceToday->check_in)
            <button id="btn-checkin" onclick="checkIn()" disabled
                    class="w-full bg-gradient-to-r from-[#0a2219] to-[#123b2c] hover:from-[#123b2c] hover:to-[#0a2219] text-white py-4 rounded-xl font-bold text-sm uppercase tracking-wider disabled:opacity-40 disabled:cursor-not-allowed hover:shadow-lg transition-all duration-200">
                📍 Absen Masuk Kerja
            </button>
            <p class="text-[10px] text-gray-400 font-bold text-center uppercase tracking-wider" id="checkin-hint">
                Aktifkan GPS & ambil foto selfie terlebih dahulu
            </p>
        @elseif (!$attendanceToday->check_out)
            <div class="bg-[#e7f0ec] border border-[#d2dfd8] p-5 rounded-2xl mb-4 space-y-2">
                <div class="flex justify-between items-center text-xs font-bold text-[#0a2219]">
                    <span class="uppercase tracking-wider">Jam Masuk Kerja</span>
                    <span class="text-sm font-extrabold bg-white px-3 py-1 rounded-lg border border-[#d2dfd8]">{{ $attendanceToday->check_in->format('H:i') }} WIB</span>
                </div>
                <div class="flex justify-between items-center text-xs font-bold text-[#0a2219] border-t border-[#d2dfd8]/50 pt-2">
                    <span class="uppercase tracking-wider">Status Ketepatan</span>
                    @if ($attendanceToday->status === 'on_time')
                        <span class="inline-flex items-center px-2 py-0.5 bg-emerald-500/10 text-emerald-700 rounded-md border border-emerald-500/20 uppercase tracking-wider text-[10px]">Tepat Waktu</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 bg-amber-500/10 text-amber-700 rounded-md border border-amber-500/20 uppercase tracking-wider text-[10px]">Terlambat</span>
                    @endif
                </div>
                @if ($attendanceToday->selfie_photo)
                    <div class="border-t border-[#d2dfd8]/50 pt-2 flex items-center gap-2 text-[10px] font-bold text-[#0a2219]">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Foto selfie tersimpan</span>
                    </div>
                @endif
                @if ($attendanceToday->is_suspect)
                    <div class="border-t border-amber-200 pt-2 flex items-center gap-2 text-[10px] font-extrabold text-amber-700 bg-amber-50 -mx-5 -mb-5 px-5 pb-4 rounded-b-2xl mt-2">
                        ⚠️ GPS Anda terdeteksi mencurigakan — HR akan memverifikasi
                    </div>
                @endif
            </div>

            <button id="btn-checkout" onclick="checkOut()" disabled
                    class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-600 text-white py-4 rounded-xl font-bold text-sm uppercase tracking-wider disabled:opacity-40 disabled:cursor-not-allowed hover:shadow-lg transition-all duration-200">
                📍 Absen Keluar Kerja
            </button>
        @else
            <div class="bg-[#e7f0ec] border border-[#d2dfd8] p-6 rounded-2xl text-center space-y-4 shadow-sm">
                <div class="w-12 h-12 bg-[#0a2219] text-[#d4af37] border border-[#d2dfd8] rounded-full flex items-center justify-center font-extrabold text-xl mx-auto">✓</div>
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

    {{-- Quick Navigation --}}
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
    // ============================================================
    // STATE
    // ============================================================
    let userLat      = null;
    let userLng      = null;
    let gpsAccuracy  = 999;
    let selfieBase64 = null; // Data URI foto selfie yang sudah diambil
    let cameraStream = null; // MediaStream dari kamera

    const statusEl   = document.getElementById('location-status');
    const btnCheckin = document.getElementById('btn-checkin');
    const btnCheckout= document.getElementById('btn-checkout');
    const timeEl     = document.getElementById('current-time');
    const hintEl     = document.getElementById('checkin-hint');

    // ============================================================
    // CLOCK — Update setiap detik
    // ============================================================
    setInterval(() => {
        timeEl.textContent = new Date().toLocaleTimeString('id-ID');
    }, 1000);

    // ============================================================
    // GPS — watchPosition (realtime update)
    // ============================================================
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(
            pos => {
                userLat     = pos.coords.latitude;
                userLng     = pos.coords.longitude;
                gpsAccuracy = pos.coords.accuracy;

                statusEl.innerHTML = `
                    <div class="text-emerald-700 font-extrabold text-xs uppercase tracking-wider mb-1 flex items-center justify-center gap-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                        ✓ GPS AKTIF TERKUNCI
                    </div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Margin Akurasi: ${gpsAccuracy.toFixed(1)}m</p>
                `;

                // Checkout button selalu aktif setelah GPS ok
                if (btnCheckout) btnCheckout.disabled = false;

                // Checkin button aktif hanya jika sudah ada selfie
                updateCheckinButton();
            },
            err => {
                statusEl.innerHTML = `
                    <div class="text-red-600 font-extrabold text-xs uppercase tracking-wider mb-1">
                        ✗ GPS TIDAK AKTIF
                    </div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Harap aktifkan GPS & izin lokasi browser</p>
                `;
            },
            { enableHighAccuracy: true, maximumAge: 10000, timeout: 10000 }
        );
    }

    // ============================================================
    // SELFIE CAMERA
    // ============================================================

    async function openCamera() {
        try {
            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 480 }, height: { ideal: 480 } },
                audio: false
            });

            const video = document.getElementById('camera-video');
            video.srcObject = cameraStream;
            video.classList.remove('hidden');
            document.getElementById('camera-placeholder').classList.add('hidden');

            document.getElementById('btn-open-camera').classList.add('hidden');
            const btnTake = document.getElementById('btn-take-photo');
            btnTake.classList.remove('hidden');
            btnTake.classList.add('flex');
        } catch (err) {
            Swal.fire({
                title: 'Kamera Tidak Dapat Diakses',
                text: 'Pastikan browser diizinkan mengakses kamera. Perlu HTTPS atau localhost.',
                icon: 'warning',
                confirmButtonColor: '#0a2219',
            });
        }
    }

    function takePhoto() {
        const video  = document.getElementById('camera-video');
        const canvas = document.getElementById('camera-canvas');

        // Set canvas ke ukuran video
        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;

        const ctx = canvas.getContext('2d');

        // Mirror (flip horizontal) agar selfie tidak terbalik
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0);

        // Kompres ke JPEG 70% (hemat bandwidth)
        selfieBase64 = canvas.toDataURL('image/jpeg', 0.7);

        // Tampilkan preview
        document.getElementById('selfie-img').src = selfieBase64;
        document.getElementById('selfie-preview').classList.remove('hidden');
        video.classList.add('hidden');
        const btnTake = document.getElementById('btn-take-photo');
        btnTake.classList.add('hidden');
        btnTake.classList.remove('flex');
        document.getElementById('btn-retake').classList.remove('hidden');

        // Hentikan stream kamera
        if (cameraStream) {
            cameraStream.getTracks().forEach(t => t.stop());
            cameraStream = null;
        }

        // Update tombol absen masuk
        updateCheckinButton();
    }

    function retakePhoto() {
        selfieBase64 = null;
        document.getElementById('selfie-preview').classList.add('hidden');
        document.getElementById('btn-retake').classList.add('hidden');
        document.getElementById('btn-open-camera').classList.remove('hidden');
        updateCheckinButton();
    }

    /**
     * Tombol Absen Masuk aktif hanya jika GPS AKTIF + SELFIE sudah diambil
     */
    function updateCheckinButton() {
        if (!btnCheckin) return;
        const gpsOk    = userLat !== null && userLng !== null;
        const selfieOk = selfieBase64 !== null;

        btnCheckin.disabled = !(gpsOk && selfieOk);

        if (hintEl) {
            if (!gpsOk && !selfieOk) {
                hintEl.textContent = 'Aktifkan GPS & ambil foto selfie terlebih dahulu';
            } else if (!gpsOk) {
                hintEl.textContent = 'GPS belum aktif';
            } else if (!selfieOk) {
                hintEl.textContent = 'Ambil foto selfie terlebih dahulu';
            } else {
                hintEl.textContent = 'GPS & selfie siap — klik tombol di atas untuk absen';
            }
        }
    }

    // ============================================================
    // CHECK-IN
    // ============================================================
    async function checkIn() {
        if (!userLat || !userLng) {
            Swal.fire({ title: 'GPS Belum Aktif', text: 'GPS belum mendeteksi lokasi Anda.', icon: 'warning', confirmButtonColor: '#0a2219' });
            return;
        }
        if (!selfieBase64) {
            Swal.fire({ title: 'Foto Selfie Belum Ada', text: 'Ambil foto selfie terlebih dahulu.', icon: 'warning', confirmButtonColor: '#0a2219' });
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
                    latitude:     userLat,
                    longitude:    userLng,
                    gps_accuracy: gpsAccuracy,
                    selfie:       selfieBase64,
                })
            });

            const data = await response.json();

            if (data.success) {
                let icon = 'success';
                let extra = '';
                if (data.is_suspect) {
                    icon  = 'warning';
                    extra = '\n\n⚠️ GPS Anda terdeteksi mencurigakan. HR akan memverifikasi absensi ini.';
                }
                Swal.fire({
                    title: 'Absen Masuk Berhasil!',
                    text: data.message + extra,
                    icon: icon,
                    confirmButtonColor: '#0a2219',
                }).then(() => location.reload());
            } else {
                Swal.fire({ title: 'Gagal!', text: data.message, icon: 'error', confirmButtonColor: '#0a2219' });
                btnCheckin.disabled = false;
                btnCheckin.textContent = '📍 Absen Masuk Kerja';
            }
        } catch (error) {
            Swal.fire({ title: 'Terjadi Kesalahan!', text: error.message, icon: 'error', confirmButtonColor: '#0a2219' });
            btnCheckin.disabled = false;
            btnCheckin.textContent = '📍 Absen Masuk Kerja';
        }
    }

    // ============================================================
    // CHECK-OUT
    // ============================================================
    async function checkOut() {
        const confirmResult = await Swal.fire({
            title: 'Konfirmasi Absen Keluar',
            text: 'Yakin ingin melakukan absen keluar kerja sekarang?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0a2219',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Absen Keluar',
            cancelButtonText: 'Batal',
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
                body: JSON.stringify({ latitude: userLat || 0, longitude: userLng || 0 })
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({ title: 'Absen Keluar Berhasil!', text: data.message, icon: 'success', confirmButtonColor: '#0a2219' })
                    .then(() => location.reload());
            } else {
                Swal.fire({ title: 'Gagal!', text: data.message, icon: 'error', confirmButtonColor: '#0a2219' });
                btnCheckout.disabled = false;
                btnCheckout.textContent = '📍 Absen Keluar Kerja';
            }
        } catch (error) {
            Swal.fire({ title: 'Terjadi Kesalahan!', text: error.message, icon: 'error', confirmButtonColor: '#0a2219' });
            btnCheckout.disabled = false;
            btnCheckout.textContent = '📍 Absen Keluar Kerja';
        }
    }
</script>
@endsection