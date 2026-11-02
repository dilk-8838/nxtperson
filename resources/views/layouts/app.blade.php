<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Nxtperson Private Limited - Digitising Agriculture, Empowering Farmer')</title>
    <meta name="description" content="@yield('meta_description', 'Nxtperson Private Limited is an integrated digital agricultural value-chain platform developing an end-to-end ecosystem connecting farmers, FPOs, suppliers, and institutional buyers.')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Caveat:wght@600;700&display=swap" rel="stylesheet">

    <!-- Favicon & Brand Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo-mark.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

    <!-- Styles & Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col bg-white text-slate-900 selection:bg-emerald-100 selection:text-emerald-900 pb-16 lg:pb-0 overflow-x-hidden">

    <!-- Navbar Component -->
    @include('components.navbar')

    <!-- Flash Status Messages -->
    @if(session('role_message'))
        <div class="bg-emerald-800 text-white text-xs sm:text-sm py-2 px-4 shadow-sm border-b border-emerald-900 animate-fadeIn">
            <div class="agri-container flex items-center justify-between">
                <span class="flex items-center gap-2 font-medium">
                    <span class="text-base">✓</span> {{ session('role_message') }}
                </span>
                <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-emerald-200 hover:text-white text-base">✕</button>
            </div>
        </div>
    @endif

    <!-- Main Content Canvas -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Real-Time Buyer Notification Popup Toast -->
    @include('components.realtime-notification-popup')

    <!-- Footer Component -->
    @include('components.footer')

    <!-- Mobile Bottom Navigation Dock (Fixed for screens <= 768px) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-stone-200 py-1.5 px-3 flex justify-around items-center shadow-lg text-[10px] font-medium text-stone-600">
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-0.5 p-1 touch-target justify-center {{ request()->routeIs('home') ? 'text-emerald-800 font-bold' : 'hover:text-emerald-700' }}">
            <span class="text-lg">🌾</span>
            <span>Home</span>
        </a>
        <a href="{{ route('advisory.index') }}" class="flex flex-col items-center gap-0.5 p-1 touch-target justify-center {{ request()->routeIs('advisory.*') ? 'text-emerald-800 font-bold' : 'hover:text-emerald-700' }}">
            <span class="text-lg">🔬</span>
            <span>Advisory</span>
        </a>
        <a href="{{ route('weather.index') }}" class="flex flex-col items-center gap-0.5 p-1 touch-target justify-center {{ request()->routeIs('weather.*') ? 'text-emerald-800 font-bold' : 'hover:text-emerald-700' }}">
            <span class="text-lg">🌦️</span>
            <span>Weather</span>
        </a>
        <a href="{{ route('mandi.index') }}" class="flex flex-col items-center gap-0.5 p-1 touch-target justify-center {{ request()->routeIs('mandi.*') ? 'text-emerald-800 font-bold' : 'hover:text-emerald-700' }}">
            <span class="text-lg">📈</span>
            <span>Mandi</span>
        </a>
        <a href="{{ route('satellite.index') }}" class="flex flex-col items-center gap-0.5 p-1 touch-target justify-center {{ request()->routeIs('satellite.*') ? 'text-emerald-800 font-bold' : 'hover:text-emerald-700' }}">
            <span class="text-lg">🛰️</span>
            <span>NDVI</span>
        </a>
    </nav>

    <!-- Micro JS for Interactive Elements -->
    <script>
        // Smooth scroll helper
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>
