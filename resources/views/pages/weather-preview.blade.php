@extends('layouts.app')

@section('title', 'Agro Weather - NxtPerson AgriTech')

@section('content')
<div class="py-12 bg-stone-50">
    <div class="agri-container">
        <div class="mb-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold mb-3">
                <span>🌦️</span> Weather Module Preview • Phase 2
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">
                {{ __('messages.weather.title') }}
            </h1>
            <p class="text-stone-600 mt-2 max-w-2xl text-base">
                Structured micro-weather adapter with prominently labeled sample Gujarat data for Rajkot, Anand, Surat, Mehsana, and Amreli.
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-stone-200/80 p-8 shadow-xs max-w-3xl">
            <p class="text-sm text-stone-600 mb-6">Comprehensive 7-day spray conditions and humidity alerts will be linked in the modular implementation.</p>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-700 text-white font-semibold text-sm hover:bg-emerald-800 transition-colors">
                ← Return to Homepage
            </a>
        </div>
    </div>
</div>
@endsection
