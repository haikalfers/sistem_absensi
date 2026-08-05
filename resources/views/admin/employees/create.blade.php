@extends('layouts.admin')

@section('title', 'Tambah Karyawan')
@section('page-title', 'Pendaftaran Karyawan')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
            
            <div class="flex items-center space-x-3 mb-6 border-b border-gray-50 pb-4">
                <div class="w-10 h-10 bg-[#e7f0ec] text-[#0a2219] rounded-xl flex items-center justify-center font-bold">
                    +
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800 uppercase tracking-wider">Tambah Karyawan Baru</h3>
                    <p class="text-xs text-gray-500 font-semibold">Isi data pribadi dan info pekerjaan dengan lengkap</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.employees.store') }}" class="space-y-5">
                @csrf

                <!-- Nama -->
                <div>
                    <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Lengkap *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200"
                           required placeholder="Masukkan nama lengkap">
                    @error('name') <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Alamat Email *</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200"
                           required placeholder="nama@sukajadilogam.com">
                    @error('email') <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Employee Code -->
                    <div>
                        <label for="employee_code" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">ID Karyawan *</label>
                        <input type="text" name="employee_code" id="employee_code" value="{{ old('employee_code') }}" 
                               placeholder="e.g., EMP001"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200"
                               required>
                        @error('employee_code') <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <!-- Position -->
                    <div>
                        <label for="position" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Jabatan *</label>
                        <input type="text" name="position" id="position" value="{{ old('position') }}" 
                               placeholder="e.g., Manager, Staff"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200"
                               required>
                        @error('position') <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Division -->
                    <div>
                        <label for="division" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Divisi</label>
                        <input type="text" name="division" id="division" value="{{ old('division') }}" 
                               placeholder="e.g., Produksi, HR"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200">
                        @error('division') <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="department" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Departemen *</label>
                        <select name="department" id="department" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] bg-white transition duration-200"
                                required>
                            <option value="">-- Pilih Departemen --</option>
                            <option value="Rungkut" {{ old('department') === 'Rungkut' ? 'selected' : '' }}>Rungkut</option>
                            <option value="Driyorejo" {{ old('department') === 'Driyorejo' ? 'selected' : '' }}>Driyorejo</option>
                        </select>
                        @error('department') <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Base Salary -->
                <div>
                    <label for="base_salary" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Gaji Pokok (Rp) *</label>
                    <input type="number" name="base_salary" id="base_salary" value="{{ old('base_salary') }}" 
                           placeholder="0"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200"
                           required min="0" step="1000">
                    @error('base_salary') <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Password *</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" 
                                   class="w-full pl-4 pr-10 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200"
                                   required minlength="8">
                            <button type="button" onclick="togglePassword('password', 'eye-icon-password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-[#d4af37] focus:outline-none transition-colors">
                                <svg id="eye-icon-password" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-[10px] text-gray-400 font-semibold mt-1">Minimal 8 karakter</p>
                        @error('password') <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Konfirmasi Password *</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   class="w-full pl-4 pr-10 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200"
                                   required minlength="8">
                            <button type="button" onclick="togglePassword('password_confirmation', 'eye-icon-password-conf')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-[#d4af37] focus:outline-none transition-colors">
                                <svg id="eye-icon-password-conf" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation') <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-4 border-t border-gray-50 mt-6">
                    <button type="submit" class="flex-1 bg-[#0a2219] hover:bg-[#123b2c] text-white py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 shadow-md">
                        Simpan Karyawan
                    </button>
                    <a href="{{ route('admin.employees.index') }}" class="flex-1 bg-[#f0f4f2] text-[#0a2219] border border-[#d2dfd8] py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
        }
    }
</script>
@endsection