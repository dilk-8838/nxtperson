@extends('layouts.app')

@section('title', 'Certified Agri Inputs - NxtPerson AgriTech')

@section('content')
<div class="py-12 bg-stone-50">
    <div class="agri-container">
        <div class="mb-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold mb-3">
                <span>📦</span> Input Catalog • No Direct Checkout
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">
                {{ __('messages.modules.products_title') }}
            </h1>
            <p class="text-stone-600 mt-2 max-w-2xl text-base">
                Curated bio-fertilizers, micro-nutrients, and crop protection inputs matched to Gujarat crops.
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-stone-200/80 p-8 shadow-xs max-w-3xl">
            <p class="text-sm text-stone-600 mb-6">In accordance with the specification, this catalog strictly operates as a discovery directory connecting with local distributors without checkout.</p>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-700 text-white font-semibold text-sm hover:bg-emerald-800 transition-colors">
                ← Return to Homepage
            </a>
        </div>
    </div>
</div>
@endsection
