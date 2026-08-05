@extends('layouts.employee')

@section('title', 'Ajukan Cuti')

@section('content')
    <div class="mb-5 flex items-center gap-2 border-b border-gray-100 pb-4">
        <a href="{{ route('employee.leave-requests.index') }}" class="text-gray-400 hover:text-[#0a2219] transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h2 class="text-base font-extrabold text-[#0a2219] uppercase tracking-wider">Ajukan Cuti / Izin</h2>
            <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">Silakan isi formulir pengajuan dengan benar</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('employee.leave-requests.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Leave Type -->
            <div>
                <label for="leave_type_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Jenis Cuti *</label>
                <select name="leave_type_id" id="leave_type_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] bg-white transition duration-200" required>
                    <option value="">-- Pilih Jenis Cuti --</option>
                    @foreach ($leaveTypes as $type)
                        <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                            @if ($type->max_days)
                                (Maksimal: {{ $type->max_days }} hari)
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('leave_type_id')
                    <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Start Date & End Date Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tanggal Mulai *</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200" required>
                    @error('start_date')
                        <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tanggal Selesai *</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200" required>
                    @error('end_date')
                        <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Reason -->
            <div>
                <label for="reason" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Alasan / Keterangan *</label>
                <textarea name="reason" id="reason" rows="3" maxlength="500" placeholder="Jelaskan alasan pengajuan Anda..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200 resize-none" required>{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Document Upload -->
            <div>
                <label for="document" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Dokumen Lampiran (Opsional)</label>
                <input type="file" name="document" id="document" accept=".pdf,.jpg,.png"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] bg-white transition duration-200">
                <p class="text-[10px] text-gray-400 font-semibold mt-1">Format: PDF, JPG, atau PNG (Maksimal: 5MB)</p>
                @error('document')
                    <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box -->
            <div class="bg-[#faf3e0] border border-[#f3e7c4] p-4 rounded-xl text-xs font-bold text-[#8a6d1c] flex items-center space-x-2">
                <svg class="w-5 h-5 text-[#8a6d1c] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>💡 Sisa jatah cuti tahunan aktif Anda: {{ $employee->annual_leave_balance }} Hari</span>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-2">
                <button type="submit" class="flex-1 bg-[#0a2219] hover:bg-[#123b2c] text-white py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 border border-transparent shadow-sm">
                    Ajukan Cuti
                </button>
                <a href="{{ route('employee.leave-requests.index') }}" class="flex-1 bg-[#f0f4f2] text-[#0a2219] border border-[#d2dfd8] py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
