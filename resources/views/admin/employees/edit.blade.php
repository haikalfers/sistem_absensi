@extends('layouts.admin')

@section('title', 'Edit Karyawan')
@section('page-title', 'Edit Data Karyawan')

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 max-w-2xl mx-auto">
    
    <div class="flex items-center justify-between mb-6 border-b border-gray-50 pb-4">
        <div>
            <span class="text-[10px] font-extrabold text-[#d4af37] tracking-widest uppercase">Perubahan Profil</span>
            <h3 class="text-base font-bold text-gray-800 uppercase tracking-wider mt-1">{{ $employee->name }}</h3>
        </div>
        <a href="{{ route('admin.employees.show', $employee) }}" class="inline-flex items-center text-xs font-bold text-gray-400 hover:text-[#0a2219] uppercase tracking-wider transition duration-150">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Batal
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-100 text-red-600 p-4 rounded-xl mb-6 shadow-sm">
            <p class="font-bold text-sm mb-1">Terjadi kesalahan input:</p>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.employees.update', $employee) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <!-- Nama Karyawan -->
        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Lengkap *</label>
            <input type="text" name="name" value="{{ old('name', $employee->name) }}" required 
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Position -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Jabatan (Position) *</label>
                <input type="text" name="position" value="{{ old('position', $employee->position) }}" required 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200">
            </div>

            <!-- Base Salary -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Gaji Pokok (Rp) *</label>
                <input type="number" name="base_salary" value="{{ old('base_salary', $employee->base_salary) }}" required min="0" step="1000"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Division -->
            <div>
                <label for="division" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Divisi</label>
                <select name="division" id="division" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] bg-white transition duration-200"
                        onchange="updateSubDivisions()">
                    <option value="">-- Pilih Divisi --</option>
                    @foreach(config('organization.structure', []) as $divName => $subs)
                        <option value="{{ $divName }}" {{ old('division', $employee->division) === $divName ? 'selected' : '' }}>{{ $divName }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sub Division -->
            <div>
                <label for="sub_division" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Subdivisi</label>
                <select name="sub_division" id="sub_division" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] bg-white transition duration-200">
                    <option value="">-- Pilih Subdivisi --</option>
                </select>
            </div>
        </div>

        <!-- Buttons -->
        <div class="mt-8 flex gap-4 pt-4 border-t border-gray-50">
            <button type="submit" class="flex-1 bg-[#0a2219] hover:bg-[#123b2c] text-white py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 shadow-md">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.employees.show', $employee) }}" class="flex-1 bg-[#f0f4f2] text-[#0a2219] border border-[#d2dfd8] py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 text-center">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
    const orgStructure = @json(config('organization.structure', []));
    const currentSubDiv = @json(old('sub_division', $employee->sub_division));

    function updateSubDivisions() {
        const divSelect = document.getElementById('division');
        const subSelect = document.getElementById('sub_division');
        const selectedDiv = divSelect.value;

        subSelect.innerHTML = '<option value="">-- Pilih Subdivisi --</option>';

        if (selectedDiv && orgStructure[selectedDiv] && orgStructure[selectedDiv].length > 0) {
            orgStructure[selectedDiv].forEach(sub => {
                const opt = document.createElement('option');
                opt.value = sub;
                opt.textContent = sub;
                if (currentSubDiv === sub) opt.selected = true;
                subSelect.appendChild(opt);
            });
            subSelect.disabled = false;
        } else {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = '-- Tanpa Subdivisi --';
            subSelect.appendChild(opt);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateSubDivisions();
    });
</script>
@endsection
