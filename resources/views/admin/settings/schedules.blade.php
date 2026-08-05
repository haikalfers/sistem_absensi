@extends('layouts.admin')

@section('title', 'Pengaturan Jadwal Kerja')
@section('page-title', 'Pengaturan Jadwal Kerja')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
    <div class="mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">Jadwal Kerja Standar</h2>
        <p class="text-gray-500 text-sm mt-1">Atur jam masuk, jam pulang, dan hari kerja efektif untuk setiap jadwal (misal: Office, Produksi).</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="bg-green-50 text-green-600 p-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.schedules.update') }}">
        @csrf
        
        <div class="space-y-6">
            @foreach($schedules as $schedule)
                <div class="bg-gray-50 border rounded-lg p-5">
                    <h3 class="font-bold text-gray-800 mb-4">{{ $schedule->name }}</h3>
                    
                    <input type="hidden" name="schedules[{{ $schedule->id }}][name]" value="{{ $schedule->name }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Jam Masuk</label>
                            <input type="time" name="schedules[{{ $schedule->id }}][check_in_time]" value="{{ old("schedules.{$schedule->id}.check_in_time", \Carbon\Carbon::parse($schedule->check_in_time)->format('H:i')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Jam Pulang</label>
                            <input type="time" name="schedules[{{ $schedule->id }}][check_out_time]" value="{{ old("schedules.{$schedule->id}.check_out_time", \Carbon\Carbon::parse($schedule->check_out_time)->format('H:i')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Hari Kerja Efektif</label>
                        @php
                            $selectedDays = old("schedules.{$schedule->id}.working_days", $schedule->working_days ?? []);
                        @endphp
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach($daysOfWeek as $dayValue => $dayName)
                                <label class="inline-flex items-center bg-white border rounded px-3 py-2 cursor-pointer hover:bg-gray-100">
                                    <input type="checkbox" name="schedules[{{ $schedule->id }}][working_days][]" value="{{ $dayValue }}" {{ in_array($dayValue, $selectedDays) ? 'checked' : '' }} class="form-checkbox h-4 w-4 text-blue-600 rounded">
                                    <span class="ml-2 text-sm text-gray-700">{{ $dayName }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
