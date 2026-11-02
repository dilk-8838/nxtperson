@extends('layouts.app')

@section('title', 'Farmer Onboarding & Login - NxtPerson AgriTech')

@section('content')
<div class="py-14 bg-stone-50">
    <div class="agri-container flex justify-center">
        <div class="w-full max-w-lg bg-white rounded-2xl border border-stone-200/90 p-8 shadow-sm">
            <div class="text-center mb-6">
                <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 text-3xl mb-3 shadow-xs">
                    🌾
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">{{ __('messages.nav.login') }}</h1>
                <p class="text-xs sm:text-sm text-stone-500 mt-1">Gujarat Farmer Mobile Authentication • Demonstration Mode</p>
            </div>

            <!-- Prominent Demonstration Notice -->
            <x-alert variant="amber" title="Demonstration Onboarding Policy:" class="mb-6">
                Mobile authentication below uses a simulated demonstration engine. <strong>No commercial SMS gateway is connected and no carrier text messages are dispatched.</strong> Use simulated OTP: <code class="bg-amber-200 px-1.5 py-0.5 rounded font-mono font-bold">123456</code>.
            </x-alert>

            @if(session('otp_sent'))
                <x-alert variant="emerald" title="Simulated OTP Dispatched:" class="mb-6">
                    {{ session('otp_sent') }}
                </x-alert>
            @endif

            @if($errors->any())
                <x-alert variant="rose" title="Verification Error:" class="mb-6">
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            <!-- 2-Step Simulated OTP Form -->
            <form action="{{ route('demo.verify-otp') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
                        Gujarat Farmer Mobile Number
                    </label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3.5 rounded-l-xl border border-r-0 border-stone-300 bg-stone-100 text-stone-600 text-xs font-mono font-bold">+91</span>
                        <input type="text" name="phone" value="{{ session('simulated_phone', '9876543210') }}" class="w-full rounded-r-xl border border-stone-300 bg-white py-2.5 px-3 text-sm text-stone-900 font-mono focus:ring-2 focus:ring-emerald-600 focus:outline-hidden" placeholder="Enter 10-digit mobile">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-stone-700 uppercase tracking-wider">
                            Simulated Demonstration OTP
                        </label>
                        <span class="text-[11px] text-emerald-700 font-semibold">Demo Code: 123456</span>
                    </div>
                    <input type="text" name="otp" value="123456" maxlength="6" class="w-full rounded-xl border border-stone-300 bg-stone-50 py-2.5 px-4 text-center text-lg font-mono font-bold tracking-widest text-emerald-900 focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:outline-hidden" placeholder="123456">
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm shadow-md transition-all flex items-center justify-center gap-2">
                    <span>🌾</span>
                    <span>Verify Simulated OTP & Access Farmer Portal</span>
                </button>
            </form>

            <!-- Quick Single-Click Demo Switcher -->
            <div class="mt-8 pt-6 border-t border-stone-200">
                <span class="block text-xs font-semibold text-stone-500 uppercase tracking-wider text-center mb-3">
                    Or Instant 1-Click Role Perspective:
                </span>
                <div class="grid grid-cols-3 gap-2">
                    <form action="{{ route('demo.switch-role') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role" value="farmer">
                        <button type="submit" class="w-full py-2 px-1 rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-900 text-xs font-bold hover:bg-emerald-100 transition-colors">
                            🌾 Farmer (Rajkot)
                        </button>
                    </form>
                    <form action="{{ route('demo.switch-role') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role" value="distributor">
                        <button type="submit" class="w-full py-2 px-1 rounded-lg border border-amber-300 bg-amber-50 text-amber-900 text-xs font-bold hover:bg-amber-100 transition-colors">
                            🏢 Distributor (Anand)
                        </button>
                    </form>
                    <form action="{{ route('demo.switch-role') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role" value="admin">
                        <button type="submit" class="w-full py-2 px-1 rounded-lg border border-purple-300 bg-purple-50 text-purple-900 text-xs font-bold hover:bg-purple-100 transition-colors">
                            🛡️ Admin (Lead)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
