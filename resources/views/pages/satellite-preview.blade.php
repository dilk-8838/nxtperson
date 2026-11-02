@extends('layouts.app')

@section('title', 'Satellite NDVI & Field Health - NxtPerson AgriTech')

@section('content')
<div class="py-12 bg-stone-50">
    <div class="agri-container">
        <div class="mb-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 text-purple-800 text-xs font-semibold mb-3">
                <span>🛰️</span> Satellite Health Preview • Simulated Data
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">
                {{ __('messages.modules.satellite_title') }}
            </h1>
            <p class="text-stone-600 mt-2 max-w-2xl text-base">
                {{ __('messages.modules.satellite_desc') }}
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-stone-200/80 p-8 shadow-xs max-w-3xl">
            <div class="p-4 rounded-xl bg-purple-50 border border-purple-200 text-purple-900 text-sm mb-6 flex items-start gap-3">
                <span class="text-xl">🛰️</span>
                <div>
                    <strong class="font-bold">Simulated Demonstration Layer:</strong>
                    <p class="text-xs mt-0.5 text-purple-800">
                        Interactive field plot NDVI spectral analysis will be rendered on an interactive canvas in Step 6.
                    </p>
                </div>
            </div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-700 text-white font-semibold text-sm hover:bg-emerald-800 transition-colors">
                ← Return to Homepage
            </a>
        </div>
    </div>
</div>
@endsection
