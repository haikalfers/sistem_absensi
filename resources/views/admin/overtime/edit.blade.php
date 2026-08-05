@extends('layouts.admin')

@section('title', 'Validasi Lembur')
@section('page-title', 'Validasi & Edit Lembur')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-b border-gray-100 pb-5">
        <div>
            <span class="text-[10px] font-extrabold text-[#d4af37] tracking-widest uppercase">Validasi Data</span>
            <h2 class="text-xl font-extrabold text-[#0a2219] mt-1">Detail Lembur - {{ $overtime->employee->name }}</h2>
        </div>
        <a href="{{ route('admin.overtime.index') }}" class="inline-flex items-center text-xs font-bold text-gray-400 hover:text-[#0a2219] uppercase tracking-wider transition duration-150">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali
        </a>
    </div>

    <!-- Error/Success Flash Messages -->
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

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="font-semibold text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Main Content Card -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 space-y-8">
        
        <!-- Info Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 bg-gray-50 p-5 rounded-2xl border border-gray-100">
            <div>
                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider block">Tanggal</span>
                <span class="text-sm font-bold text-[#0a2219] mt-1 block">{{ \Carbon\Carbon::parse($overtime->date)->format('d M Y') }}</span>
            </div>
            <div>
                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider block">Tipe Lembur</span>
                <span class="text-sm font-bold text-gray-700 mt-1 block capitalize">{{ str_replace('_', ' ', $overtime->type) }}</span>
            </div>
            <div>
                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider block">Status Validasi</span>
                @if ($overtime->validated_by)
                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-extrabold rounded-lg border border-emerald-200 uppercase tracking-wider mt-1">
                        Tervalidasi
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-700 text-[10px] font-extrabold rounded-lg border border-amber-200 uppercase tracking-wider mt-1">
                        Pending
                    </span>
                @endif
            </div>
        </div>

        <!-- Form Edit Data (Only If Not Validated) -->
        @if (!$overtime->validated_by)
            <form method="POST" action="{{ route('admin.overtime.update', $overtime->id) }}" class="space-y-5 border-t border-gray-50 pt-6">
                @csrf
                @method('PUT')

                <h3 class="text-xs font-extrabold text-[#0a2219] uppercase tracking-wider mb-4">Edit Data Lembur</h3>
                
                <div class="grid grid-cols-1 gap-4">
                    @if(in_array($overtime->type, ['office', 'admin_production']))
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Durasi (Jam) *</label>
                            <input type="number" step="0.5" name="hours" value="{{ old('hours', $overtime->hours) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200" required>
                        </div>
                    @else
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Jumlah Hasil (KG) *</label>
                            <input type="number" step="0.01" name="kg_amount" value="{{ old('kg_amount', $overtime->kg_amount) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200" required>
                        </div>
                    @endif

                    @if($overtime->type === 'production_export')
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Bonus per KG (Rp) *</label>
                            <input type="number" name="export_bonus_per_kg" value="{{ old('export_bonus_per_kg', $overtime->export_bonus_per_kg) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200" required>
                        </div>
                    @endif
                </div>

                <div class="pt-2">
                    <button type="submit" class="bg-[#0a2219] hover:bg-[#123b2c] text-white px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 border border-transparent shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

            <!-- Form Validation -->
            <form method="POST" action="{{ route('admin.overtime.validate', $overtime->id) }}" class="border-t border-gray-50 pt-8">
                @csrf
                @method('PUT')
                
                <div class="bg-[#e7f0ec] p-6 rounded-2xl border border-[#d2dfd8]">
                    <h3 class="font-extrabold text-[#0a2219] text-sm mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#0a2219]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Validasi Data Lembur
                    </h3>
                    <p class="text-xs text-[#0a2219]/80 mb-5 leading-relaxed">
                        Pastikan data volume hasil kerja (tonase KG) atau jam durasi lembur sudah diisi dengan benar sebelum divalidasi. 
                        Setelah divalidasi, lembur akan **dikunci** dan nominalnya otomatis masuk ke perhitungan gaji periode (Payroll) berikutnya.
                    </p>
                    <button type="submit" class="w-full bg-[#0a2219] hover:bg-[#123b2c] text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 border border-transparent shadow-md shadow-emerald-950/10">
                        Validasi Sekarang
                    </button>
                </div>
            </form>
        @else
            <!-- Display Validated Data -->
            <div class="border-t border-gray-50 pt-6">
                <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-3">Rincian Lembur Tervalidasi</h3>
                <div class="bg-[#e7f0ec] p-6 rounded-2xl border border-[#d2dfd8] space-y-3">
                    @if(in_array($overtime->type, ['office', 'admin_production']))
                        <p class="text-sm font-semibold text-gray-700">
                            <span class="text-xs text-gray-500 font-bold uppercase tracking-wider block">Durasi Kerja</span>
                            {{ $overtime->hours }} Jam
                        </p>
                    @else
                        <p class="text-sm font-semibold text-gray-700">
                            <span class="text-xs text-gray-500 font-bold uppercase tracking-wider block">Jumlah Hasil Produksi</span>
                            {{ number_format($overtime->kg_amount, 2, ',', '.') }} KG
                        </p>
                    @endif
                    
                    <p class="text-lg font-extrabold text-gray-800 pt-2 border-t border-[#d2dfd8]/50">
                        <span class="text-xs text-[#0a2219] font-bold uppercase tracking-wider block">Nominal Lembur Diperoleh</span>
                        <span class="text-emerald-700">Rp {{ number_format($overtime->overtime_amount, 0, ',', '.') }}</span>
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
