@extends('layouts.admin')

@section('title', 'Pengaturan Jam Kerja')
@section('page-title', 'Pengaturan Jam Kerja Divisi & Standar')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    {{-- Alert Feedback --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl shadow-sm">
            <div class="font-bold mb-1 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Gagal Menyimpan:
            </div>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm font-semibold">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-gray-800">Manajemen Jam Kerja Perusahaan</h2>
            <p class="text-gray-500 text-xs mt-1">
                Atur jam masuk, jam pulang, dan toleransi keterlambatan secara khusus per divisi (misal: <strong>Marketing masuk jam 09:00</strong>), atau gunakan jam kerja umum (default) jika tidak ditentukan secara spesifik.
            </p>
        </div>
        <button onclick="toggleModal('modal-add-schedule', true)" 
                class="inline-flex items-center justify-center gap-2 bg-[#0a2219] hover:bg-[#123b2c] text-[#d4af37] px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition shadow-sm border border-[#1d523e]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Jam Kerja Divisi
        </button>
    </div>

    {{-- Schedule Update Form --}}
    <form method="POST" action="{{ route('admin.settings.schedules.update') }}">
        @csrf
        
        <div class="space-y-6">
            @foreach($schedules as $schedule)
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden transition hover:shadow-md">
                    {{-- Header Card --}}
                    <div class="bg-gray-50/80 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <h3 class="font-bold text-gray-800 text-base">
                                {{ $schedule->name }}
                            </h3>
                            @if(empty($schedule->division))
                                <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                                    ⭐ Default / Semua Divisi
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider rounded-lg bg-amber-50 text-amber-800 border border-amber-200">
                                    🏢 Divisi: {{ $schedule->division }}
                                </span>
                            @endif
                        </div>

                        {{-- Action Delete for Custom Schedules --}}
                        @if($schedule->id > 2 || !empty($schedule->division))
                            <button type="button" 
                                    onclick="confirmDeleteSchedule({{ $schedule->id }}, '{{ $schedule->name }}')" 
                                    class="text-red-500 hover:text-red-700 hover:bg-red-50 p-1.5 rounded-lg transition" 
                                    title="Hapus Jam Kerja Ini">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                    
                    {{-- Body Card --}}
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Nama Jadwal</label>
                                <input type="text" name="schedules[{{ $schedule->id }}][name]" value="{{ old("schedules.{$schedule->id}.name", $schedule->name) }}" required class="w-full px-3.5 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#0a2219] focus:border-[#0a2219]">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Berlaku Untuk Divisi</label>
                                <select name="schedules[{{ $schedule->id }}][division]" class="w-full px-3.5 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#0a2219] focus:border-[#0a2219]">
                                    <option value="" {{ empty($schedule->division) ? 'selected' : '' }}>-- Semua Divisi (Default/Umum) --</option>
                                    @foreach($divisions as $div)
                                        <option value="{{ $div }}" {{ strcasecmp($schedule->division ?? '', $div) === 0 ? 'selected' : '' }}>
                                            Divisi {{ $div }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Jam Masuk</label>
                                    <input type="time" name="schedules[{{ $schedule->id }}][check_in_time]" value="{{ old("schedules.{$schedule->id}.check_in_time", \Carbon\Carbon::parse($schedule->check_in_time)->format('H:i')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-[#0a2219]">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Jam Pulang</label>
                                    <input type="time" name="schedules[{{ $schedule->id }}][check_out_time]" value="{{ old("schedules.{$schedule->id}.check_out_time", \Carbon\Carbon::parse($schedule->check_out_time)->format('H:i')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-[#0a2219]">
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Hari Kerja Efektif</label>
                            @php
                                $selectedDays = old("schedules.{$schedule->id}.working_days", $schedule->working_days ?? []);
                            @endphp
                            <div class="grid grid-cols-2 md:grid-cols-7 gap-2">
                                @foreach($daysOfWeek as $dayValue => $dayName)
                                    <label class="inline-flex items-center justify-between bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 cursor-pointer hover:bg-gray-100 transition">
                                        <span class="text-xs font-medium text-gray-700">{{ $dayName }}</span>
                                        <input type="checkbox" name="schedules[{{ $schedule->id }}][working_days][]" value="{{ $dayValue }}" {{ in_array($dayValue, $selectedDays) ? 'checked' : '' }} class="form-checkbox h-4 w-4 text-[#0a2219] rounded focus:ring-0">
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-gradient-to-r from-[#0a2219] to-[#123b2c] hover:from-[#123b2c] hover:to-[#0a2219] text-white px-8 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition shadow-md">
                💾 Simpan Semua Perubahan
            </button>
        </div>
    </form>
</div>

{{-- MODAL TAMBAH JADWAL DIVISI BARU --}}
<div id="modal-add-schedule" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-100">
        <div class="flex items-center justify-between pb-4 border-b">
            <h3 class="text-lg font-extrabold text-gray-800">Tambah Jam Kerja Divisi Baru</h3>
            <button onclick="toggleModal('modal-add-schedule', false)" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.settings.schedules.store') }}" class="mt-4 space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Nama Jadwal Kerja</label>
                <input type="text" name="name" placeholder="Misal: Jam Kerja Marketing Shift Pagi" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#0a2219]">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Pilih Divisi Sasaran</label>
                <input type="text" name="division" list="division-options" placeholder="Ketik nama divisi (misal: Marketing, Produksi, Sales)" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#0a2219]">
                <datalist id="division-options">
                    @foreach($divisions as $div)
                        <option value="{{ $div }}"></option>
                    @endforeach
                </datalist>
                <p class="text-[11px] text-gray-400 mt-1">Karyawan pada divisi ini akan otomatis menggunakan jam kerja khusus ini.</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Jam Masuk</label>
                    <input type="time" name="check_in_time" value="09:00" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm font-bold focus:ring-2 focus:ring-[#0a2219]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Jam Pulang</label>
                    <input type="time" name="check_out_time" value="17:00" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm font-bold focus:ring-2 focus:ring-[#0a2219]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Hari Kerja Efektif</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach($daysOfWeek as $dayValue => $dayName)
                        <label class="inline-flex items-center bg-gray-50 border rounded-xl px-2.5 py-1.5 cursor-pointer hover:bg-gray-100">
                            <input type="checkbox" name="working_days[]" value="{{ $dayValue }}" {{ in_array($dayValue, [1,2,3,4,5]) ? 'checked' : '' }} class="form-checkbox h-3.5 w-3.5 text-[#0a2219] rounded">
                            <span class="ml-2 text-xs text-gray-700 font-medium">{{ $dayName }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-4 border-t flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modal-add-schedule', false)" class="px-4 py-2.5 text-xs font-bold text-gray-500 uppercase tracking-wider hover:bg-gray-100 rounded-xl">Batal</button>
                <button type="submit" class="px-6 py-2.5 bg-[#0a2219] text-[#d4af37] text-xs font-bold uppercase tracking-wider rounded-xl shadow hover:bg-[#123b2c]">Simpan Jadwal Divisi</button>
            </div>
        </form>
    </div>
</div>

{{-- Hidden Form for Delete --}}
<form id="form-delete-schedule" method="POST" action="" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('js')
<script>
    function toggleModal(id, show) {
        const modal = document.getElementById(id);
        if (modal) {
            if (show) modal.classList.remove('hidden');
            else modal.classList.add('hidden');
        }
    }

    function confirmDeleteSchedule(id, name) {
        Swal.fire({
            title: 'Hapus Jam Kerja?',
            text: `Apakah Anda yakin ingin menghapus jadwal "${name}"? Karyawan divisi terkait akan kembali menggunakan jam kerja standar.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('form-delete-schedule');
                form.action = `{{ url('/admin/settings/schedules') }}/${id}`;
                form.submit();
            }
        });
    }
</script>
@endsection
