@extends('layouts.app')

@section('title', 'Find Distributors - NxtPerson AgriTech')

@section('content')
<div class="py-12 bg-stone-50">
    <div class="agri-container">
        <div class="mb-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-100 text-teal-800 text-xs font-semibold mb-3">
                <span>📍</span> Pincode Distributor Directory
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">
                {{ __('messages.modules.distributors_title') }}
            </h1>
            <p class="text-stone-600 mt-2 max-w-2xl text-base">
                Pincode and district-based filtering for verified seed, fertilizer, and equipment dealers across Gujarat.
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-stone-200/80 p-8 shadow-xs max-w-3xl">
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm mb-6">
                <strong>Demonstration Disclosure:</strong> Seeded demonstration partners — addresses are sample mock records and not real-world commercial stores.
            </div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-700 text-white font-semibold text-sm hover:bg-emerald-800 transition-colors">
                ← Return to Homepage
            </a>
        </div>
    </div>
</div>
@endsection
