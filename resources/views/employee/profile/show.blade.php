@extends('layouts.employee')

@section('title', 'Profil Saya')

@section('content')

@if (session('success'))
    <div class="mb-5 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center shadow-sm">
        <svg class="w-5 h-5 mr-2 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="font-semibold text-xs">{{ session('success') }}</p>
    </div>
@endif

@if ($errors->any())
    <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl shadow-sm">
        <p class="font-bold text-red-700 mb-2 text-xs">Terjadi kesalahan:</p>
        <ul class="list-disc list-inside text-red-600 text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Hero Bio Card --}}
<div class="bg-gradient-to-br from-[#0a2219] to-[#153a2b] rounded-2xl overflow-hidden mb-5 border border-[#1d523e] shadow-sm">
    <div class="p-6 text-center text-white">
        <div class="w-20 h-20 bg-gradient-to-br from-[#f3e7c4] to-[#d4af37] rounded-full mx-auto flex items-center justify-center text-[#0a2219] font-black text-3xl mb-4 shadow-lg ring-4 ring-white/10">
            {{ strtoupper(substr($employee->name, 0, 1)) }}
        </div>
        <h3 class="font-extrabold text-lg text-white leading-tight">{{ $employee->name }}</h3>
        <p class="text-xs text-[#d4af37] font-bold uppercase tracking-widest mt-1">{{ $employee->position }}</p>
        <p class="text-[10px] text-gray-300 font-semibold uppercase tracking-wider mt-0.5">{{ $employee->department }}</p>
    </div>
    
    <div class="grid grid-cols-3 border-t border-[#1d523e]">
        <div class="py-4 text-center border-r border-[#1d523e]">
            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-1">Kode</p>
            <p class="text-xs text-white font-extrabold">{{ $employee->employee_code }}</p>
        </div>
        <div class="py-4 text-center border-r border-[#1d523e]">
            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-1">Mulai Kerja</p>
            <p class="text-xs text-white font-extrabold">{{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('M Y') : '-' }}</p>
        </div>
        <div class="py-4 text-center">
            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-1">Sisa Cuti</p>
            <p class="text-xs text-[#d4af37] font-extrabold">{{ $employee->annual_leave_balance }} Hari</p>
        </div>
    </div>
</div>

{{-- Info Tambahan --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
    <h4 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">Informasi Karyawan</h4>
    <div class="space-y-3 text-xs font-semibold text-gray-600">
        <div class="flex justify-between items-center py-2.5 border-b border-gray-50">
            <span class="text-gray-400 font-bold uppercase tracking-wider">Divisi / Unit</span>
            <span class="text-gray-800 font-bold">{{ $employee->division ?? '-' }}</span>
        </div>
        <div class="flex justify-between items-center py-2.5 border-b border-gray-50">
            <span class="text-gray-400 font-bold uppercase tracking-wider">Status</span>
            <span class="inline-flex items-center px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-extrabold rounded-lg border border-emerald-100 uppercase tracking-wider">
                Aktif
            </span>
        </div>
        <div class="flex justify-between items-center py-2.5 border-b border-gray-50">
            <span class="text-gray-400 font-bold uppercase tracking-wider">Nomor HP</span>
            <span class="text-gray-800 font-bold">{{ $employee->phone ?? '-' }}</span>
        </div>
        <div class="flex justify-between items-center py-2.5">
            <span class="text-gray-400 font-bold uppercase tracking-wider">Email Akun</span>
            <span class="text-gray-800 font-bold truncate max-w-[180px]">{{ auth()->user()->email }}</span>
        </div>
    </div>
</div>

{{-- Edit Data Akun --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
    <h4 class="text-xs font-extrabold text-gray-700 uppercase tracking-widest mb-4 border-b border-gray-50 pb-3">Edit Data Akun</h4>
    
    <form method="POST" action="{{ route('employee.profile.update') }}" class="space-y-4">
        @csrf
        @method('PUT')
        
        <div>
            <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
            <input type="text" name="name" id="name" value="{{ old('name', $employee->name) }}"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200" required>
            @error('name')
                <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200" required>
            @error('email')
                <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="phone" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nomor Handphone</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $employee->phone) }}"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200">
            @error('phone')
                <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>
        
        <button type="submit" class="w-full bg-[#0a2219] hover:bg-[#123b2c] text-white py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 border border-transparent shadow-sm mt-2">
            Simpan Perubahan
        </button>
    </form>
</div>

{{-- Ubah Password --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
    <h4 class="text-xs font-extrabold text-gray-700 uppercase tracking-widest mb-1 border-b border-gray-50 pb-3">Ubah Password Akun</h4>
    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider mb-4 mt-2">Gunakan password yang kuat & tidak mudah ditebak.</p>
    
    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Password Saat Ini</label>
            <input type="password" name="current_password"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200" required>
            @error('current_password', 'updatePassword')
                <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Password Baru</label>
            <input type="password" name="password"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200" required>
            @error('password', 'updatePassword')
                <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200" required>
        </div>
        
        <div class="flex items-center gap-4 pt-1">
            <button type="submit" class="flex-1 bg-gray-800 hover:bg-gray-900 text-white py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 border border-transparent shadow-sm">
                Simpan Password
            </button>
            @if (session('status') === 'password-updated')
                <span class="text-xs text-emerald-600 font-bold">✓ Tersimpan</span>
            @endif
        </div>
    </form>
</div>

{{-- Danger Zone --}}
<div class="bg-white rounded-2xl border border-red-100 shadow-sm p-6 mb-5">
    <h4 class="text-xs font-extrabold text-red-700 uppercase tracking-widest mb-3">Logout / Keluar Akun</h4>
    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider mb-4">Anda akan keluar dari sesi aplikasi ini. Silakan absen dahulu sebelum keluar.</p>
    
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-600 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition duration-150 border border-red-100">
            Logout Dari Akun
        </button>
    </form>
</div>

@endsection
