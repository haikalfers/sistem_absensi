@extends('layouts.employee')

@section('title', 'Form Pengajuan Lembur')

@section('content')
    <div class="mb-5 flex items-center gap-2 border-b border-gray-100 pb-4">
        <a href="{{ route('employee.overtime.index') }}" class="text-gray-400 hover:text-[#0a2219] transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h2 class="text-base font-extrabold text-[#0a2219] uppercase tracking-wider">Form Pengajuan Lembur</h2>
            <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">Isi rencana lembur Anda untuk diajukan ke HRD</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('employee.overtime.store') }}" class="space-y-5">
            @csrf

            <!-- Tanggal Lembur -->
            <div>
                <label for="date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tanggal Lembur *</label>
                <input type="date" name="date" id="date" value="{{ old('date', now()->format('Y-m-d')) }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200" required>
                @error('date')
                    <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipe Lembur -->
            <div>
                <label for="type" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tipe Lembur *</label>
                <select name="type" id="type" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] bg-white transition duration-200" required>
                    <option value="office" {{ old('type', $defaultType) === 'office' ? 'selected' : '' }}>Lembur Kantor (Maksimal 1 Jam)</option>
                    <option value="admin_production" {{ old('type', $defaultType) === 'admin_production' ? 'selected' : '' }}>Lembur Admin Produksi (Maksimal 2 Jam)</option>
                    <option value="production_aka" {{ old('type', $defaultType) === 'production_aka' ? 'selected' : '' }}>Lembur Produksi AKA (Maksimal 3 Jam)</option>
                    <option value="production_export" {{ old('type', $defaultType) === 'production_export' ? 'selected' : '' }}>Lembur Produksi Ekspor (Bonus per Kg)</option>
                </select>
                @error('type')
                    <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jam Mulai & Selesai Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Jam Mulai Lembur *</label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', '17:00') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200 font-semibold" required>
                    @error('start_time')
                        <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Jam Selesai Lembur *</label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', '18:00') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200 font-semibold" required>
                    @error('end_date')
                        <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Input Khusus Ekspor (Kg Amount) -->
            <div id="export-kg-container" class="hidden">
                <label for="kg_amount" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Estimasi Jumlah Pengerjaan (Kg)</label>
                <input type="number" step="0.1" name="kg_amount" id="kg_amount" value="{{ old('kg_amount') }}" placeholder="Misal: 50.5"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200">
                <p class="text-[10px] text-gray-400 mt-1 font-semibold">Digunakan untuk menghitung bonus lembur ekspor per kilo.</p>
            </div>

            <!-- Alasan Pekerjaan Lembur -->
            <div>
                <label for="reason" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Detail Pekerjaan Lembur *</label>
                <textarea name="reason" id="reason" rows="3" maxlength="500" placeholder="Jelaskan target / pekerjaan yang akan Anda selesaikan..."
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200 resize-none" required>{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box Rules -->
            <div class="bg-[#e7f0ec] border border-[#d2dfd8] p-4 rounded-xl text-xs font-bold text-[#0a2219] flex items-start gap-2.5">
                <svg class="w-5 h-5 text-[#0a2219] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <strong>Perhatian:</strong>
                    <p class="mt-0.5 text-[11px] text-gray-600">Pengajuan lembur yang telah dikirim harus disetujui terlebih dahulu oleh HRD sebelum nominal lembur dihitung ke slip gaji.</p>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-2">
                <button type="submit" class="flex-1 bg-[#0a2219] hover:bg-[#123b2c] text-white py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 border border-transparent shadow-sm">
                    Kirim Pengajuan Lembur
                </button>
                <a href="{{ route('employee.overtime.index') }}" class="flex-1 bg-[#f0f4f2] text-[#0a2219] border border-[#d2dfd8] py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection

@section('js')
<script>
    const typeSelect = document.getElementById('type');
    const kgContainer = document.getElementById('export-kg-container');

    function toggleKgField() {
        if (typeSelect.value === 'production_export') {
            kgContainer.classList.remove('hidden');
        } else {
            kgContainer.classList.add('hidden');
        }
    }

    typeSelect.addEventListener('change', toggleKgField);
    document.addEventListener('DOMContentLoaded', toggleKgField);
</script>
@endsection
