@props(['items', 'currentRole', 'currentLocale', 'roleLabels'])

<div id="mobile-drawer" 
     class="hidden lg:hidden border-t border-stone-200 bg-white/98 backdrop-blur-xl px-4 py-5 shadow-2xl transition-all max-h-[85vh] overflow-y-auto"
     role="dialog"
     aria-modal="true"
     aria-label="Mobile Navigation Drawer">

    <!-- Mobile Navigation List -->
    <div class="space-y-2 mb-6">
        <!-- Direct Mobile Notifications Link -->
        <a href="{{ route('notifications.index') }}" 
           class="flex items-center justify-between px-3.5 py-3 rounded-2xl text-sm font-bold text-[#0c365b] bg-slate-50 hover:bg-slate-100 transition-colors border border-slate-200/80 {{ request()->routeIs('notifications.*') ? 'bg-emerald-50 text-emerald-900 font-extrabold' : '' }}">
            <div class="flex items-center gap-2.5">
                <span class="text-base">🔔</span>
                <span>Notification Center</span>
            </div>
            <span class="text-stone-400">→</span>
        </a>
        @foreach($items as $item)
            @php
                $label = isset($item['label_key']) && \Illuminate\Support\Facades\Lang::has($item['label_key']) 
                    ? __($item['label_key']) 
                    : ($item['default_label'] ?? $item['id']);

                $isMega = ($item['type'] ?? '') === 'mega';
                $itemId = $item['id'] ?? 'mobile-item';

                $isActive = false;
                if ($itemId === 'home') {
                    $isActive = request()->routeIs('home');
                } elseif ($itemId === 'solutions') {
                    $isActive = request()->routeIs('solutions.*');
                } elseif ($itemId === 'farmer-services') {
                    $isActive = request()->routeIs('advisory.*', 'weather.*', 'satellite.*', 'mandi.*', 'dashboard', 'crops.*');
                } elseif ($itemId === 'crops') {
                    $isActive = request()->routeIs('crops.*');
                } elseif ($itemId === 'marketplace' || $itemId === 'agri-products' || $itemId === 'manufacturers') {
                    $isActive = request()->routeIs('marketplace.*', 'products.*', 'manufacturers.*', 'distributors.*');
                } elseif ($itemId === 'ecosystem') {
                    $isActive = request()->routeIs('ecosystem.*');
                } elseif ($itemId === 'about-us') {
                    $isActive = request()->routeIs('about', 'roadmap.*');
                }
            @endphp

            @if(!$isMega)
                <!-- Direct Mobile Link -->
                <a href="{{ isset($item['route']) ? route($item['route']) : '/' }}" 
                   class="flex items-center justify-between px-3.5 py-3 rounded-2xl text-sm font-bold text-stone-800 hover:bg-emerald-50 hover:text-emerald-900 transition-colors {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-extrabold' : '' }}">
                    <div class="flex items-center gap-2.5">
                        @if(!empty($item['icon']))
                            <span class="text-base">{{ $item['icon'] }}</span>
                        @endif
                        <span>{{ $label }}</span>
                    </div>
                    <span class="text-stone-400">→</span>
                </a>
            @else
                <!-- Mobile Accordion Section -->
                <div class="mobile-accordion-group rounded-2xl border border-stone-200/80 overflow-hidden bg-stone-50/50">
                    <button type="button" 
                            class="mobile-accordion-trigger w-full flex items-center justify-between px-3.5 py-3 text-sm font-bold text-stone-800 hover:bg-stone-100 transition-colors cursor-pointer {{ $isActive ? 'text-emerald-900 bg-emerald-50/70' : '' }}" 
                            aria-expanded="false" 
                            aria-controls="accordion-panel-{{ $itemId }}">
                        <div class="flex items-center gap-2.5">
                            @if(!empty($item['icon']))
                                <span class="text-base">{{ $item['icon'] }}</span>
                            @endif
                            <span>{{ $label }}</span>
                            @if(!empty($item['badge']))
                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    {{ $item['badge'] }}
                                </span>
                            @endif
                        </div>
                        <svg class="accordion-chevron w-4 h-4 text-stone-400 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Accordion Collapsible Content -->
                    <div id="accordion-panel-{{ $itemId }}" class="mobile-accordion-content hidden px-3 pt-2 pb-3 bg-white border-t border-stone-200/70 space-y-3">
                        @foreach($item['columns'] ?? [] as $col)
                            @php
                                $colTitle = isset($col['title_key']) && \Illuminate\Support\Facades\Lang::has($col['title_key'])
                                    ? __($col['title_key'])
                                    : ($col['default_title'] ?? '');
                            @endphp
                            <div class="space-y-1">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-400 px-2 pt-1">
                                    {{ $colTitle }}
                                </div>
                                @foreach($col['links'] ?? [] as $link)
                                    @php
                                        $linkLabel = isset($link['label_key']) && \Illuminate\Support\Facades\Lang::has($link['label_key'])
                                            ? __($link['label_key'])
                                            : ($link['default_label'] ?? $link['label'] ?? '');
                                        
                                        $url = isset($link['route']) 
                                            ? route($link['route'], $link['params'] ?? []) . (!empty($link['fragment']) ? '#' . $link['fragment'] : '') 
                                            : ($link['url'] ?? '#');
                                    @endphp
                                    <a href="{{ $url }}" class="flex items-start gap-2.5 p-2 rounded-xl text-xs hover:bg-emerald-50 text-stone-700 hover:text-emerald-900 transition-colors">
                                        @if(!empty($link['icon']))
                                            <span class="text-sm shrink-0">{{ $link['icon'] }}</span>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-stone-800 leading-tight">{{ $linkLabel }}</span>
                                                @if(!empty($link['badge']))
                                                    <span class="text-[8px] font-bold px-1 rounded bg-emerald-100 text-emerald-800">
                                                        {{ $link['badge'] }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if(!empty($link['desc']))
                                                <span class="text-[10px] text-stone-500 block leading-tight mt-0.5 line-clamp-1">{{ $link['desc'] }}</span>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Mobile Language Selector & Role Switcher Section -->
    <div class="pt-4 border-t border-stone-200">
        <!-- Language Switcher -->
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Select Language</span>
            <div class="flex items-center gap-1 bg-stone-100 p-0.5 rounded-lg text-xs">
                <a href="{{ route('locale.set', 'en') }}" class="px-2 py-1 rounded-md font-semibold {{ $currentLocale === 'en' ? 'bg-white text-emerald-900 shadow-2xs' : 'text-stone-600' }}">English</a>
                <a href="{{ route('locale.set', 'hi') }}" class="px-2 py-1 rounded-md font-semibold {{ $currentLocale === 'hi' ? 'bg-white text-emerald-900 shadow-2xs' : 'text-stone-600' }}">हिंदी</a>
                <a href="{{ route('locale.set', 'gu') }}" class="px-2 py-1 rounded-md font-semibold {{ $currentLocale === 'gu' ? 'bg-white text-emerald-900 shadow-2xs' : 'text-stone-600' }}">ગુજરાતી</a>
            </div>
        </div>

        <!-- Role Switcher -->
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Active Demo Persona</span>
            <span class="text-xs px-2 py-0.5 rounded-full font-bold {{ $roleLabels[$currentRole]['color'] ?? '' }}">
                {{ $roleLabels[$currentRole]['icon'] ?? '🌾' }} {{ $roleLabels[$currentRole]['label'] ?? 'Farmer' }}
            </span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5 text-center text-xs">
            <form action="{{ route('demo.switch-role') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="farmer">
                <button type="submit" class="w-full py-2 px-1 rounded-xl border text-stone-700 bg-stone-50 hover:bg-emerald-50 {{ $currentRole === 'farmer' ? 'border-emerald-600 bg-emerald-50 font-bold text-emerald-900' : 'border-stone-200' }}">
                    🌾 Farmer
                </button>
            </form>
            <form action="{{ route('demo.switch-role') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="supplier">
                <button type="submit" class="w-full py-2 px-1 rounded-xl border text-stone-700 bg-stone-50 hover:bg-amber-50 {{ in_array($currentRole, ['supplier', 'distributor']) ? 'border-amber-600 bg-amber-50 font-bold text-amber-900' : 'border-stone-200' }}">
                    🏢 Supplier
                </button>
            </form>
            <form action="{{ route('demo.switch-role') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="buyer">
                <button type="submit" class="w-full py-2 px-1 rounded-xl border text-stone-700 bg-stone-50 hover:bg-blue-50 {{ $currentRole === 'buyer' ? 'border-blue-600 bg-blue-50 font-bold text-blue-900' : 'border-stone-200' }}">
                    🛒 Buyer
                </button>
            </form>
            <form action="{{ route('demo.switch-role') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="fpo">
                <button type="submit" class="w-full py-2 px-1 rounded-xl border text-stone-700 bg-stone-50 hover:bg-teal-50 {{ in_array($currentRole, ['fpo', 'cooperative']) ? 'border-teal-600 bg-teal-50 font-bold text-teal-900' : 'border-stone-200' }}">
                    🤝 FPO / Coop
                </button>
            </form>
            <form action="{{ route('demo.switch-role') }}" method="POST" class="col-span-2 sm:col-span-1">
                @csrf
                <input type="hidden" name="role" value="admin">
                <button type="submit" class="w-full py-2 px-1 rounded-xl border text-stone-700 bg-stone-50 hover:bg-purple-50 {{ $currentRole === 'admin' ? 'border-purple-600 bg-purple-50 font-bold text-purple-900' : 'border-stone-200' }}">
                    🛡️ Admin
                </button>
            </form>
        </div>

        <!-- Mobile Login & Registration Links -->
        <div class="grid grid-cols-2 gap-2 mt-4 pt-3 border-t border-stone-200">
            <a href="{{ route('login') }}" class="w-full text-center py-2.5 px-3 rounded-xl border border-slate-300 font-semibold text-xs text-[#0c365b] hover:bg-slate-50">
                Log In
            </a>
            <a href="{{ route('register') }}" class="w-full text-center py-2.5 px-3 rounded-xl bg-[#0c365b] text-white font-bold text-xs hover:bg-[#071f37]">
                Register
            </a>
        </div>
    </div>
</div>
