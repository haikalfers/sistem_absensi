@extends('layouts.admin')

@section('title', 'Buat Jadwal Dinas Luar')
@section('page-title', 'Buat Jadwal Dinas Luar')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    #loc-map { height: 300px; border-radius: 0.75rem; z-index: 1; }
    .employee-chip {
        display: inline-flex; align-items: center; gap: 6px;
        background: #7f1d1d; color: #ffffff;
        padding: 4px 10px 4px 8px; border-radius: 8px;
        font-size: 11px; font-weight: 700; letter-spacing: 0.05em;
        text-transform: uppercase; border: 1px solid #450a0a;
    }
    .employee-chip button { color: #ffffff; opacity: 0.8; line-height: 1; }
    .employee-chip button:hover { opacity: 1; }
</style>
@endsection

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.field-assignments.index') }}" class="text-gray-400 hover:text-[#7f1d1d] transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <h3 class="text-base font-bold text-gray-800 uppercase tracking-wider">Buat Jadwal Dinas Luar</h3>
        <p class="text-xs text-gray-500 font-semibold mt-0.5">Pilih karyawan, tanggal, dan lokasi penugasan</p>
    </div>
</div>

@if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 shadow-sm">
        <ul class="list-disc list-inside text-sm font-semibold space-y-1">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif

<form id="form-field-assignment" method="POST" action="{{ route('admin.field-assignments.store') }}">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ==== Left Column: Karyawan & Tanggal ==== --}}
        <div class="space-y-5">

            {{-- Pilih Karyawan (Multi Select) --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h4 class="text-xs font-extrabold text-[#7f1d1d] uppercase tracking-wider border-b border-gray-100 pb-3 mb-4">
                    Pilih Karyawan yang Dinas Luar
                </h4>

                {{-- Search Input --}}
                <div class="relative mb-3">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input id="search-employee" type="text" placeholder="Cari nama karyawan..."
                           class="pl-9 w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#7f1d1d] focus:border-transparent">
                </div>

                {{-- Selected chips --}}
                <div id="selected-chips" class="flex flex-wrap gap-2 mb-3 min-h-[32px]"></div>

                {{-- Employee list --}}
                <div id="employee-list" class="border border-gray-100 rounded-xl overflow-y-auto max-h-64">
                    @foreach ($employees as $emp)
                        <label id="emp-row-{{ $emp->id }}"
                               class="flex items-center gap-3 px-4 py-2.5 hover:bg-red-50/50 cursor-pointer border-b border-gray-50 last:border-0 transition">
                            <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}"
                                   id="emp-check-{{ $emp->id }}"
                                   class="emp-checkbox rounded border-gray-300 text-[#7f1d1d] focus:ring-[#7f1d1d]"
                                   {{ is_array(old('employee_ids')) && in_array($emp->id, old('employee_ids')) ? 'checked' : '' }}>
                            <div class="w-8 h-8 bg-[#7f1d1d] text-white rounded-lg flex items-center justify-center font-extrabold text-xs flex-shrink-0">
                                {{ substr($emp->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-800 truncate">{{ $emp->name }}</p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                    {{ $emp->division ?? 'Umum' }} · {{ $emp->position ?? '-' }}
                                </p>
                            </div>
                        </label>
                    @endforeach
                </div>

                <p id="count-label" class="text-[10px] text-gray-400 font-semibold mt-2">0 karyawan dipilih</p>
            </div>

            {{-- Tanggal Penugasan --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h4 class="text-xs font-extrabold text-[#7f1d1d] uppercase tracking-wider border-b border-gray-100 pb-3 mb-4">
                    Tanggal Penugasan
                </h4>
                <input type="date" name="date" id="input-date"
                       value="{{ old('date', now()->addDay()->toDateString()) }}"
                       min="{{ now()->toDateString() }}"
                       class="w-full px-3.5 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#7f1d1d] focus:border-transparent"
                       required>
                <p class="text-[10px] text-amber-600 font-semibold mt-2">
                    ⚠️ Jadwal dapat dibuat untuk hari ini atau ke depan saja.
                </p>
            </div>
        </div>

        {{-- ==== Right Column: Lokasi ==== --}}
        <div class="space-y-5">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h4 class="text-xs font-extrabold text-[#7f1d1d] uppercase tracking-wider border-b border-gray-100 pb-3 mb-4">
                    Lokasi Penugasan
                </h4>

                {{-- Nama Lokasi --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">
                        Nama Lokasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="location_name" value="{{ old('location_name') }}"
                           placeholder="cth: Gudang Sidoarjo, Pameran Surabaya, Klien PT. ABC"
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#7f1d1d] focus:border-transparent"
                           required>
                </div>

                {{-- Map untuk pick koordinat acuan --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">
                        Koordinat Acuan Lokasi
                        <span class="text-gray-400 font-normal">(opsional — klik peta untuk pin lokasi)</span>
                    </label>
                    <div id="loc-map" class="border border-gray-200 shadow-inner mb-2"></div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">Latitude</label>
                            <input type="number" step="any" name="location_lat" id="input-lat" value="{{ old('location_lat') }}"
                                   placeholder="-7.3193"
                                   class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs font-mono focus:ring-2 focus:ring-[#7f1d1d]">
                        </div>
                        <div>
                            <label class="block text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">Longitude</label>
                            <input type="number" step="any" name="location_lng" id="input-lng" value="{{ old('location_lng') }}"
                                   placeholder="112.7483"
                                   class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs font-mono focus:ring-2 focus:ring-[#7f1d1d]">
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1.5 font-semibold">
                        Koordinat acuan digunakan HR untuk verifikasi posisi karyawan di peta.
                    </p>
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Catatan HR (Opsional)</label>
                    <textarea name="notes" rows="3" placeholder="Contoh: Mewakili perusahaan di pameran APII, harap mengenakan seragam resmi."
                              class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#7f1d1d] focus:border-transparent resize-none">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full bg-gradient-to-r from-[#7f1d1d] to-[#991b1b] hover:from-[#991b1b] hover:to-[#7f1d1d] text-white py-3.5 rounded-xl font-extrabold text-sm uppercase tracking-wider shadow-lg transition duration-200 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Simpan Jadwal Dinas Luar
            </button>
        </div>
    </div>
</form>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // ── Employee Multi-Select ──────────────────────────────────────
    const checkboxes   = document.querySelectorAll('.emp-checkbox');
    const chipsEl      = document.getElementById('selected-chips');
    const countLabel   = document.getElementById('count-label');
    const searchInput  = document.getElementById('search-employee');

    // Map empId → name untuk chip display
    const empNames = {
        @foreach ($employees as $emp)
            {{ $emp->id }}: @json($emp->name),
        @endforeach
    };

    function updateChips() {
        chipsEl.innerHTML = '';
        let count = 0;
        checkboxes.forEach(cb => {
            if (cb.checked) {
                count++;
                const chip = document.createElement('div');
                chip.className = 'employee-chip';
                chip.id = `chip-${cb.value}`;
                chip.innerHTML = `<span>${empNames[cb.value]}</span>
                    <button type="button" onclick="uncheckEmp(${cb.value})" title="Hapus">×</button>`;
                chipsEl.appendChild(chip);
            }
        });
        countLabel.textContent = `${count} karyawan dipilih`;
    }

    function uncheckEmp(empId) {
        const cb = document.getElementById(`emp-check-${empId}`);
        if (cb) { cb.checked = false; updateChips(); }
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateChips));

    // Search filter
    searchInput.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#employee-list label').forEach(row => {
            const name = row.querySelector('p')?.textContent.toLowerCase() ?? '';
            row.style.display = name.includes(q) ? '' : 'none';
        });
    });

    // Init chips on load (for old() values)
    updateChips();


    // ── Leaflet Map (Koordinat Acuan) ──────────────────────────────
    const map = L.map('loc-map').setView([-7.3193, 112.7483], 13);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        maxZoom: 19, attribution: '© OpenStreetMap © CARTO'
    }).addTo(map);

    let pinMarker = null;
    const inputLat = document.getElementById('input-lat');
    const inputLng = document.getElementById('input-lng');

    function setPin(lat, lng) {
        if (pinMarker) map.removeLayer(pinMarker);
        pinMarker = L.marker([lat, lng], {
            icon: L.divIcon({
                className: '',
                html: `<div style="background:#7f1d1d;color:#ffffff;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;border:2px solid #ffffff;box-shadow:0 4px 6px -1px rgba(0,0,0,.4)">📍</div>`,
                iconSize: [32,32], iconAnchor: [16,32]
            })
        }).addTo(map).bindPopup('<b>Lokasi Acuan Penugasan</b>').openPopup();
        inputLat.value = lat.toFixed(7);
        inputLng.value = lng.toFixed(7);
    }

    // Click map to pick
    map.on('click', function(e) { setPin(e.latlng.lat, e.latlng.lng); });

    // Sync manual input → map
    function syncFromInputs() {
        const lat = parseFloat(inputLat.value);
        const lng = parseFloat(inputLng.value);
        if (!isNaN(lat) && !isNaN(lng)) {
            setPin(lat, lng);
            map.setView([lat, lng], 15);
        }
    }
    inputLat.addEventListener('change', syncFromInputs);
    inputLng.addEventListener('change', syncFromInputs);

    // Init if old() value exists
    const initLat = parseFloat(inputLat.value);
    const initLng = parseFloat(inputLng.value);
    if (!isNaN(initLat) && !isNaN(initLng)) setPin(initLat, initLng);


    // ── Form Validation ───────────────────────────────────────────
    document.getElementById('form-field-assignment').addEventListener('submit', function(e) {
        const anyChecked = Array.from(checkboxes).some(c => c.checked);
        if (!anyChecked) {
            e.preventDefault();
            Swal.fire({
                title: 'Pilih Karyawan',
                text: 'Pilih minimal 1 karyawan untuk dijadwalkan dinas luar.',
                icon: 'warning',
                confirmButtonColor: '#0a2219'
            });
        }
    });
</script>
@endsection
