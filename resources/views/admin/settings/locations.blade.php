@extends('layouts.admin')

@section('title', 'Pengaturan Lokasi')
@section('page-title', 'Pengaturan Lokasi & GPS Kantor')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
    <div class="mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">Koordinat Lokasi Kantor</h2>
        <p class="text-gray-500 text-sm mt-1">Atur koordinat pusat (Latitude/Longitude) dan radius (dalam meter) untuk setiap cabang kantor. Digunakan untuk validasi absensi karyawan via PWA.</p>
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

    <form method="POST" action="{{ route('admin.settings.locations.update') }}">
        @csrf
        
        <div class="space-y-6">
            @foreach($locations as $index => $location)
                <div class="bg-gray-50 border rounded-lg p-5">
                    <h3 class="font-bold text-gray-800 mb-4">{{ $location->name }}</h3>
                    
                    <input type="hidden" name="locations[{{ $location->id }}][name]" value="{{ $location->name }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Latitude</label>
                            <input type="number" step="any" name="locations[{{ $location->id }}][latitude]" value="{{ old("locations.{$location->id}.latitude", $location->latitude) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Longitude</label>
                            <input type="number" step="any" name="locations[{{ $location->id }}][longitude]" value="{{ old("locations.{$location->id}.longitude", $location->longitude) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Radius Absensi (Meter)</label>
                            <input type="number" name="locations[{{ $location->id }}][radius_meters]" value="{{ old("locations.{$location->id}.radius_meters", $location->radius_meters) }}" required min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="mt-3 text-right">
                        @if($location->latitude && $location->longitude)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $location->latitude }},{{ $location->longitude }}" target="_blank" class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                                📍 Buka di Google Maps
                            </a>
                        @endif
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
