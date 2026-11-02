@extends('layouts.app')

@section('title', 'Automated Weather Stations Telemetry - Gujarat Network • NxtPerson AgriTech')

@section('content')
<div class="py-10 bg-stone-50 min-h-screen">
    <div class="agri-container">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
            <span>/</span>
            <a href="{{ route('weather.index') }}" class="hover:text-emerald-700 transition-colors">Farmer Services</a>
            <span>/</span>
            <span class="text-emerald-800 font-semibold">Weather Stations</span>
        </nav>

        <!-- Demonstration Notice Banner -->
        <div class="mb-6 p-4 rounded-2xl bg-amber-50/90 border border-amber-200/80 shadow-xs flex items-start gap-3">
            <span class="text-2xl shrink-0">⚠️</span>
            <div class="text-xs sm:text-sm text-amber-900">
                <span class="font-bold">{{ __('messages.simulated_tag') }}:</span>
                Ground-level IoT agro-weather telemetry is simulated for demo evaluation. Sensor readings reflect typical Gujarat Kharif/Rabi climatic patterns across 6 agricultural districts.
            </div>
        </div>

        <!-- Page Header -->
        <div class="mb-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold mb-3">
                <span>📡</span> Telemetric Agro-Weather Network
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">
                Gujarat Ground Weather Stations
            </h1>
            <p class="text-stone-600 mt-2 max-w-2xl text-sm sm:text-base">
                Hyperlocal atmospheric data feeds collected from deployed IoT telemetry stations across Saurashtra, Central Gujarat, and North Gujarat agro-ecological zones.
            </p>
        </div>

        <!-- Stations Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @php
                $stations = [
                    ['id' => 'AWS-RJK-01', 'name' => 'Rajkot APMC Sensor Unit', 'district' => 'Rajkot, Saurashtra', 'temp' => '28.4°C', 'humidity' => '58%', 'wind' => '14 km/h SW', 'soil_temp' => '24.1°C', 'status' => 'Online', 'signal' => '4G Telemetry'],
                    ['id' => 'AWS-AND-04', 'name' => 'Anand Agricultural Basin', 'district' => 'Anand, Charotar', 'temp' => '29.1°C', 'humidity' => '65%', 'wind' => '9 km/h W', 'soil_temp' => '25.3°C', 'status' => 'Online', 'signal' => 'LoRaWAN'],
                    ['id' => 'AWS-DSA-02', 'name' => 'Deesa Cold Belt Station', 'district' => 'Banaskantha, North Guj', 'temp' => '26.8°C', 'humidity' => '48%', 'wind' => '12 km/h NE', 'soil_temp' => '22.8°C', 'status' => 'Online', 'signal' => '4G Telemetry'],
                    ['id' => 'AWS-GDL-03', 'name' => 'Gondal Chilli Cluster', 'district' => 'Gondal, Rajkot', 'temp' => '28.9°C', 'humidity' => '54%', 'wind' => '16 km/h SW', 'soil_temp' => '24.7°C', 'status' => 'Online', 'signal' => '4G Telemetry'],
                    ['id' => 'AWS-UNJ-05', 'name' => 'Unjha Seed Market Yard', 'district' => 'Mehsana, North Guj', 'temp' => '27.5°C', 'humidity' => '51%', 'wind' => '11 km/h NW', 'soil_temp' => '23.4°C', 'status' => 'Online', 'signal' => '4G Telemetry'],
                    ['id' => 'AWS-SRT-06', 'name' => 'Surat Coastal Agro Station', 'district' => 'Surat, South Guj', 'temp' => '31.2°C', 'humidity' => '74%', 'wind' => '18 km/h S', 'soil_temp' => '26.8°C', 'status' => 'Online', 'signal' => 'LoRaWAN'],
                ];
            @endphp

            @foreach($stations as $s)
                <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs hover:border-emerald-300 transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-mono font-bold text-stone-500 uppercase tracking-wider bg-stone-100 px-2 py-0.5 rounded">
                            {{ $s['id'] }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 text-[11px] font-bold border border-emerald-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ $s['status'] }}
                        </span>
                    </div>

                    <h2 class="text-base font-bold text-stone-900 mb-0.5">
                        {{ $s['name'] }}
                    </h2>
                    <p class="text-xs text-stone-500 mb-4 flex items-center gap-1">
                        <span>📍</span> {{ $s['district'] }}
                    </p>

                    <div class="grid grid-cols-2 gap-2 text-xs bg-stone-50 p-3 rounded-2xl mb-4">
                        <div>
                            <span class="text-stone-500 block text-[10px] uppercase font-bold">Ambient Temp</span>
                            <span class="text-base font-extrabold text-stone-900">{{ $s['temp'] }}</span>
                        </div>
                        <div>
                            <span class="text-stone-500 block text-[10px] uppercase font-bold">Relative Humidity</span>
                            <span class="text-base font-extrabold text-stone-900">{{ $s['humidity'] }}</span>
                        </div>
                        <div>
                            <span class="text-stone-500 block text-[10px] uppercase font-bold">Wind Vector</span>
                            <span class="text-stone-700 font-bold">{{ $s['wind'] }}</span>
                        </div>
                        <div>
                            <span class="text-stone-500 block text-[10px] uppercase font-bold">Soil Temp (10cm)</span>
                            <span class="text-stone-700 font-bold">{{ $s['soil_temp'] }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-stone-400">
                        <span>Transmission: {{ $s['signal'] }}</span>
                        <span class="text-emerald-700 font-medium">Updated 3m ago</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex items-center gap-4">
            <a href="{{ route('weather.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-700 text-white font-semibold text-sm hover:bg-emerald-800 transition-colors">
                ← View 7-Day Forecast & Spray Windows
            </a>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-stone-600 text-sm hover:text-stone-900 transition-colors">
                Return to Home
            </a>
        </div>
    </div>
</div>
@endsection
