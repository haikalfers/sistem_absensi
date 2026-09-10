@extends('layouts.employee')

@section('title', 'Absensi')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #attendance-map {
        height: 220px;
        width: 100%;
        border-radius: 0.75rem;
        z-index: 1;
    }
    .custom-div-icon, .custom-user-icon {
        background: none;
        border: none;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 0.75rem;
        font-family: inherit;
        font-size: 11px;
    }
</style>
@endsection

@section('content')
    {{-- Status Waktu Server --}}
    <div class="bg-gradient-to-br from-[#0a2219] to-[#123b2c] text-white rounded-2xl p-6 mb-5 border border-[#1d523e] shadow-sm flex flex-col items-center text-center">
        <h2 class="text-xs font-extrabold text-[#d4af37] uppercase tracking-widest mb-1.5">Waktu Server Realtime</h2>
        <p id="current-time" class="text-3xl font-black tracking-wider text-white">{{ now()->format('H:i:s') }}</p>
        <p class="text-[10px] text-gray-300 font-bold uppercase tracking-widest mt-1">WIB (Waktu Indonesia Barat)</p>
    </div>

    {{-- GPS Status & Peta Interaktif --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest">Lokasi Absensi (GPS Map)</h3>
            <div id="distance-badge" class="hidden">
                <span id="radius-status-pill" class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider">
                    Mengecek Radius...
                </span>
            </div>
        </div>

        {{-- Leaflet Map Container --}}
        <div id="attendance-map" class="mb-3 border border-gray-100 shadow-inner"></div>

        <div id="location-status" class="text-center py-1">
            <div class="animate-spin inline-block w-5 h-5 border-2 border-[#d4af37] border-t-transparent rounded-full mb-1"></div>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Mendeteksi lokasi GPS Anda...</p>
        </div>
        @if ($office)
            <div class="border-t border-gray-50 pt-3 flex justify-between items-center text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                <span>Acuan Kantor: <strong class="text-gray-700">{{ $office->name }}</strong></span>
                <span class="text-[#0a2219] bg-[#e7f0ec] px-2.5 py-1 rounded-lg border border-[#d2dfd8]">Radius {{ $office->radius_meters }}m</span>
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
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // ============================================================
    // STATE & OFFICE DATA
    // ============================================================
    const officeData = {
        name: @json($office?->name ?? 'Kantor PT. Indobismar'),
        lat: {{ $office?->latitude ?? -7.3193 }},
        lng: {{ $office?->longitude ?? 112.7483 }},
        radius: {{ $office?->radius_meters ?? 50 }}
    };

    let userLat      = null;
    let userLng      = null;
    let gpsAccuracy  = 999;
    let selfieBase64 = null; // Data URI foto selfie yang sudah diambil
    let cameraStream = null; // MediaStream dari kamera

    let map          = null;
    let userMarker   = null;
    let officeMarker = null;
    let officeCircle = null;

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
    // HAVERSINE DISTANCE FORMULA (Meters)
    // ============================================================
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // ============================================================
    // MAP INITIALIZATION
    // ============================================================
    function initMap() {
        if (map) return;
        const mapEl = document.getElementById('attendance-map');
        if (!mapEl) return;

        map = L.map('attendance-map', {
            zoomControl: false
        }).setView([officeData.lat, officeData.lng], 17);

        L.control.zoom({ position: 'bottomright' }).addTo(map);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap &copy; CARTO'
        }).addTo(map);

        // Marker Kantor PT. Indobismar
        const officeIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background-color: #0a2219; color: #d4af37; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; border: 2px solid #d4af37; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.3);" title="${officeData.name}">🏢</div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });

        officeMarker = L.marker([officeData.lat, officeData.lng], { icon: officeIcon }).addTo(map)
            .bindPopup(`<b>${officeData.name}</b><br>Radius Absen: ${officeData.radius}m`);

        // Lingkaran Radius Kantor
        officeCircle = L.circle([officeData.lat, officeData.lng], {
            color: '#10b981',
            fillColor: '#10b981',
            fillOpacity: 0.15,
            radius: officeData.radius
        }).addTo(map);
    }

    document.addEventListener('DOMContentLoaded', () => {
        initMap();
    });

    function updateUserMapAndLocation(lat, lng, accuracy) {
        if (!map) initMap();

        const userIcon = L.divIcon({
            className: 'custom-user-icon',
            html: `<div style="background-color: #2563eb; color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; border: 2px solid white; box-shadow: 0 0 0 4px rgba(37,99,235,0.3);" title="Lokasi Anda">📍</div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });

        if (userMarker) {
            userMarker.setLatLng([lat, lng]);
        } else if (map) {
            userMarker = L.marker([lat, lng], { icon: userIcon }).addTo(map)
                .bindPopup(`<b>Lokasi Anda</b><br>Akurasi: ±${accuracy.toFixed(1)}m`);
        }

        const distance = calculateDistance(lat, lng, officeData.lat, officeData.lng);
        const isWithinRadius = distance <= officeData.radius;

        if (officeCircle) {
            officeCircle.setStyle({
                color: isWithinRadius ? '#10b981' : '#ef4444',
                fillColor: isWithinRadius ? '#10b981' : '#ef4444'
            });
        }

        const distanceBadge = document.getElementById('distance-badge');
        const radiusStatusPill = document.getElementById('radius-status-pill');

        if (distanceBadge && radiusStatusPill) {
            distanceBadge.classList.remove('hidden');
            if (isWithinRadius) {
                radiusStatusPill.className = 'px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider bg-emerald-500/10 text-emerald-700 border border-emerald-500/20';
                radiusStatusPill.innerHTML = `✓ Dalam Radius (${distance.toFixed(0)}m)`;
            } else {
                radiusStatusPill.className = 'px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider bg-red-500/10 text-red-700 border border-red-500/20';
                radiusStatusPill.innerHTML = `⚠️ Luar Radius (${distance.toFixed(0)}m dari kantor)`;
            }
        }

        if (map) {
            const bounds = L.latLngBounds([
                [lat, lng],
                [officeData.lat, officeData.lng]
            ]);
            map.fitBounds(bounds, { padding: [35, 35], maxZoom: 18 });
        }

        statusEl.innerHTML = `
            <div class="text-emerald-700 font-extrabold text-xs uppercase tracking-wider mb-1 flex items-center justify-center gap-2">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                ✓ GPS AKTIF TERKUNCI
            </div>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Jarak: ${distance.toFixed(0)}m • Akurasi GPS: ±${accuracy.toFixed(1)}m</p>
        `;

        if (btnCheckout) btnCheckout.disabled = false;
        updateCheckinButton();
    }

    // ============================================================
    // GPS — watchPosition (realtime update)
    // ============================================================
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(
            pos => {
                userLat     = pos.coords.latitude;
                userLng     = pos.coords.longitude;
                gpsAccuracy = pos.coords.accuracy;

                updateUserMapAndLocation(userLat, userLng, gpsAccuracy);
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