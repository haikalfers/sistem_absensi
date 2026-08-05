@extends('layouts.admin')

@section('title', 'Data Absensi')
@section('page-title', 'Monitor Absensi Karyawan')

@section('content')

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-start gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="font-bold text-sm">{{ session('success') }}</p>
                @if(session('import_errors') && count(session('import_errors')) > 0)
                    <ul class="mt-2 space-y-0.5">
                        @foreach(session('import_errors') as $err)
                            <li class="text-xs text-emerald-700 font-semibold">⚠ {{ $err }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-start gap-3 shadow-sm">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="font-bold text-sm mb-1">Gagal import:</p>
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-gray-800 uppercase tracking-wider">Rekapitulasi Absensi</h3>
            <p class="text-xs text-gray-500 font-semibold mt-1">Gunakan filter untuk meninjau log absen tertentu</p>
        </div>
        <button onclick="openImportModal()"
            class="inline-flex items-center gap-2 bg-[#0a2219] hover:bg-[#123b2c] text-white px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition shadow-sm border border-transparent">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Import CSV Fingerprint
        </button>
    </div>

    {{-- Import Modal --}}
    <div id="importModal" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md border border-gray-100 overflow-hidden">
            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-[#0a2219] to-[#153a2b] px-6 py-5 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-extrabold text-white uppercase tracking-wider">Import Data Absensi</h3>
                    <p class="text-[10px] text-[#d4af37] font-semibold uppercase tracking-widest mt-0.5">CSV dari Mesin Fingerprint Rungkut</p>
                </div>
                <button onclick="closeImportModal()"
                    class="text-gray-400 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6">
                {{-- Format Info --}}
                <div class="bg-[#f0f7f3] border border-[#d2dfd8] rounded-xl p-4 mb-5">
                    <p class="text-[10px] font-extrabold text-[#0a2219] uppercase tracking-widest mb-2">Format CSV yang Didukung</p>
                    <code class="block text-[11px] text-gray-600 font-mono leading-relaxed">
                        employee_code, tanggal, jam_masuk, jam_keluar<br>
                        EMP001, 14/07/2026, 08:15, 16:30<br>
                        EMP002, 14/07/2026, 08:45, 16:35
                    </code>
                    <div class="mt-3 flex flex-wrap gap-2 text-[10px] font-bold text-[#0a2219]">
                        <span class="bg-white border border-[#d2dfd8] px-2 py-0.5 rounded">✓ Duplikat dilewati</span>
                        <span class="bg-white border border-[#d2dfd8] px-2 py-0.5 rounded">✓ Status otomatis</span>
                        <span class="bg-white border border-[#d2dfd8] px-2 py-0.5 rounded">✓ Terlambat jika &gt;08:30</span>
                    </div>
                </div>

                {{-- Upload Form --}}
                <form method="POST" action="{{ route('admin.attendance.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div id="dropZone"
                        class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center cursor-pointer hover:border-[#d4af37] hover:bg-[#faf8f0] transition duration-200 mb-4"
                        onclick="document.getElementById('csvFileInput').click()"
                        ondragover="event.preventDefault(); this.classList.add('border-[#d4af37]','bg-[#faf8f0]')"
                        ondragleave="this.classList.remove('border-[#d4af37]','bg-[#faf8f0]')"
                        ondrop="handleDrop(event)">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p id="dropText" class="text-xs font-bold text-gray-500">Klik atau drag & drop file CSV di sini</p>
                        <p class="text-[10px] text-gray-400 mt-1 font-semibold">Format: .csv, .xlsx, .xls — Maks. 5MB</p>
                    </div>
                    <input type="file" id="csvFileInput" name="file" accept=".csv,.xlsx,.xls" class="hidden"
                        onchange="updateDropText(this)">

                    <div class="flex gap-3">
                        <button type="button"
                            onclick="closeImportModal()"
                            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-bold text-xs uppercase tracking-wider hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 bg-[#0a2219] hover:bg-[#123b2c] text-white py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition shadow-sm">
                            Upload & Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateDropText(input) {
            if (input.files && input.files[0]) {
                document.getElementById('dropText').textContent = '✓ ' + input.files[0].name;
                document.getElementById('dropText').classList.add('text-[#0a2219]');
            }
        }
        function handleDrop(event) {
            event.preventDefault();
            const dt = event.dataTransfer;
            const input = document.getElementById('csvFileInput');
            if (dt.files.length) {
                input.files = dt.files;
                updateDropText(input);
            }
            event.currentTarget.classList.remove('border-[#d4af37]','bg-[#faf8f0]');
        }
        function openImportModal() {
            const modal = document.getElementById('importModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeImportModal() {
            const modal = document.getElementById('importModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Auto-close modal on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImportModal();
            }
        });
    </script>
    @endpush

    <!-- Filter Form Container -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('admin.attendance.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Karyawan Select -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Karyawan</label>
                    <select name="employee_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] bg-white transition duration-200">
                        <option value="">-- Semua Karyawan --</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Select -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Status Kehadiran</label>
                    <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] bg-white transition duration-200">
                        <option value="">-- Semua Status --</option>
                        <option value="on_time" {{ request('status') === 'on_time' ? 'selected' : '' }}>Tepat Waktu</option>
                        <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Terlambat</option>
                        <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Alpha</option>
                    </select>
                </div>

                <!-- Dari Tanggal -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200">
                </div>

                <!-- Sampai Tanggal & Button -->
                <div class="flex items-end gap-3">
                    <div class="flex-1">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200">
                    </div>
                    <button type="submit"
                        class="bg-[#0a2219] hover:bg-[#123b2c] text-white px-5 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 border border-transparent focus:ring-2 focus:ring-[#d4af37] shadow-sm">
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] border-collapse">
                <thead>
                    <tr class="bg-[#f0f4f2] border-b border-gray-100">
                        <th class="px-6 py-4.5 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4.5 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Karyawan</th>
                        <th class="px-6 py-4.5 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Jam Masuk</th>
                        <th class="px-6 py-4.5 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Jam Keluar</th>
                        <th class="px-6 py-4.5 text-center text-xs font-bold text-[#0a2219] uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4.5 text-center text-xs font-bold text-[#0a2219] uppercase tracking-wider">Metode</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($attendances as $attendance)
                        <tr class="hover:bg-[#fcfdfc] transition duration-150">
                            <!-- Date -->
                            <td class="px-6 py-4 text-sm font-semibold text-gray-600">
                                {{ $attendance->date->format('d M Y') }}
                            </td>
                            
                            <!-- Employee Name -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-gray-100 text-[#0a2219] rounded-xl flex items-center justify-center font-bold text-xs border border-gray-200">
                                        {{ substr($attendance->employee->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">{{ $attendance->employee->name }}</p>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $attendance->employee->employee_code }}</p>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Check In Time -->
                            <td class="px-6 py-4 text-sm font-semibold text-gray-700">
                                {{ $attendance->check_in?->format('H:i') ?? '-' }}
                            </td>
                            
                            <!-- Check Out Time -->
                            <td class="px-6 py-4 text-sm font-semibold text-gray-700">
                                {{ $attendance->check_out?->format('H:i') ?? '-' }}
                            </td>
                            
                            <!-- Status Badges -->
                            <td class="px-6 py-4 text-center">
                                @if ($attendance->status === 'on_time')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-500/10 text-emerald-700 text-xs font-extrabold rounded-lg border border-emerald-500/20 uppercase tracking-wider">
                                        Tepat Waktu
                                    </span>
                                @elseif ($attendance->status === 'late')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-amber-500/10 text-amber-700 text-xs font-extrabold rounded-lg border border-amber-500/20 uppercase tracking-wider">
                                        Terlambat
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 bg-red-500/10 text-red-700 text-xs font-extrabold rounded-lg border border-red-500/20 uppercase tracking-wider">
                                        Mangkir / Alpha
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Source Methods -->
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 bg-gray-50 border border-gray-200 text-gray-600 text-[10px] font-bold rounded uppercase tracking-wider">
                                    {{ $attendance->source === 'pwa' ? 'GPS PWA' : 'Fingerprint' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-medium">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span>Tidak ada log data absensi terdeteksi.</span>
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
        {{ $attendances->links() }}
    </div>

    <script>
        function openImportModal() {
            document.getElementById('importModal').classList.remove('hidden');
            document.getElementById('importModal').classList.add('flex');
        }

        function closeImportModal() {
            document.getElementById('importModal').classList.remove('flex');
            document.getElementById('importModal').classList.add('hidden');
        }

        function updateDropText(input) {
            if (input.files && input.files[0]) {
                const dropText = document.getElementById('dropText');
                dropText.textContent = '✓ ' + input.files[0].name;
                dropText.classList.remove('text-gray-500');
                dropText.classList.add('text-[#0a2219]', 'text-sm');
            }
        }

        function handleDrop(event) {
            event.preventDefault();
            const dt = event.dataTransfer;
            const input = document.getElementById('csvFileInput');
            
            if (dt.files.length) {
                input.files = dt.files;
                updateDropText(input);
            }
            event.currentTarget.classList.remove('border-[#d4af37]', 'bg-[#faf8f0]');
        }

        // Auto-close modal on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('importModal').classList.add('hidden');
            }
        });
    </script>
@endsection
