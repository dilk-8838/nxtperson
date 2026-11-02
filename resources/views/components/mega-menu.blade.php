@props(['item'])

@php
    $label = isset($item['label_key']) && \Illuminate\Support\Facades\Lang::has($item['label_key']) 
        ? __($item['label_key']) 
        : ($item['default_label'] ?? $item['id']);

    $isMega = ($item['type'] ?? '') === 'mega';
    $itemId = $item['id'] ?? 'nav-item';

    // Active state calculation
    $isActive = false;
    if ($itemId === 'home') {
        $isActive = request()->routeIs('home');
    } elseif ($itemId === 'solutions') {
        $isActive = request()->routeIs('solutions.*');
    } elseif ($itemId === 'farmer-services') {
        $isActive = request()->routeIs('advisory.*', 'weather.*', 'satellite.*', 'mandi.*', 'dashboard', 'crops.*');
    } elseif ($itemId === 'marketplace') {
        $isActive = request()->routeIs('marketplace.*', 'products.*', 'manufacturers.*', 'distributors.*', 'farmer.sell.*', 'buyer.*', 'farmer.lots.*', 'ecosystem.*');
    } elseif ($itemId === 'about-us') {
        $isActive = request()->routeIs('about', 'roadmap.*');
    }
@endphp

@if(!$isMega)
    <!-- Direct Navigation Link -->
    <a href="{{ isset($item['route']) ? route($item['route']) : '/' }}" 
       class="inline-flex items-center gap-1 xl:gap-1.5 px-2 py-1 xl:px-3.5 xl:py-2 text-xs xl:text-sm font-semibold rounded-xl transition-all whitespace-nowrap {{ $isActive ? 'text-emerald-900 bg-emerald-50/90 font-bold shadow-2xs' : 'text-stone-700 hover:text-emerald-800 hover:bg-stone-50' }}"
       id="nav-link-{{ $itemId }}">
        @if(!empty($item['icon']))
            <span class="text-xs xl:text-sm">{{ $item['icon'] }}</span>
        @endif
        <span>{{ $label }}</span>
    </a>
@else
    <!-- Mega Menu Dropdown Item -->
    <div class="mega-menu-item static" data-menu-id="{{ $itemId }}">
        <button type="button" 
                class="mega-menu-trigger inline-flex items-center gap-1 xl:gap-1.5 px-2 py-1 xl:px-3.5 xl:py-2 text-xs xl:text-sm font-semibold rounded-xl text-stone-700 hover:text-emerald-800 hover:bg-stone-50 transition-all cursor-pointer focus:outline-hidden focus:ring-2 focus:ring-emerald-600 whitespace-nowrap {{ $isActive ? 'text-emerald-900 bg-emerald-50/90 font-bold' : '' }}" 
                id="menu-btn-{{ $itemId }}"
                aria-expanded="false" 
                aria-haspopup="true"
                aria-controls="mega-panel-{{ $itemId }}">
            @if(!empty($item['icon']))
                <span class="text-xs xl:text-sm">{{ $item['icon'] }}</span>
            @endif
            <span>{{ $label }}</span>
            @if(!empty($item['badge']))
                <span class="hidden 2xl:inline-block text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                    {{ $item['badge'] }}
                </span>
            @endif
            <svg class="chevron-icon w-3.5 h-3.5 text-stone-400 group-hover:text-emerald-700 transition-transform duration-200 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
        </button>

        <!-- Dropdown Mega Panel (Positioned full container width under navbar with premium rounded corners) -->
        <div id="mega-panel-{{ $itemId }}" 
             role="region"
             aria-labelledby="menu-btn-{{ $itemId }}"
             class="mega-menu-panel absolute left-0 right-0 top-full w-full bg-white border border-stone-200/90 rounded-2xl sm:rounded-3xl shadow-2xl p-6 sm:p-7 mt-2 z-50 opacity-0 translate-y-1.5 invisible pointer-events-none transition-all duration-200 ease-out overflow-hidden before:content-[''] before:absolute before:-top-3 before:left-0 before:right-0 before:h-3">
            
            @php
                $colCount = count($item['columns'] ?? []);
                $gridClass = ($colCount === 2) ? 'sm:grid-cols-2' : 'sm:grid-cols-2 lg:grid-cols-3';
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8 items-stretch">
                <!-- Columns Area -->
                <div class="md:col-span-8 lg:col-span-9 grid grid-cols-1 {{ $gridClass }} gap-6">
                    @foreach($item['columns'] ?? [] as $col)
                        @php
                            $colTitle = isset($col['title_key']) && \Illuminate\Support\Facades\Lang::has($col['title_key'])
                                ? __($col['title_key'])
                                : ($col['default_title'] ?? '');
                        @endphp
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 pb-2 border-b border-stone-100">
                                @if(!empty($col['icon']))
                                    <span class="text-base">{{ $col['icon'] }}</span>
                                @endif
                                <h3 class="text-xs font-bold uppercase tracking-wider text-stone-500">
                                    {{ $colTitle }}
                                </h3>
                            </div>

                            <ul class="space-y-1.5" role="menu">
                                @foreach($col['links'] ?? [] as $link)
                                    @php
                                        $linkLabel = isset($link['label_key']) && \Illuminate\Support\Facades\Lang::has($link['label_key'])
                                            ? __($link['label_key'])
                                            : ($link['default_label'] ?? $link['label'] ?? '');
                                        
                                        $url = isset($link['route']) 
                                            ? route($link['route'], $link['params'] ?? []) . (!empty($link['fragment']) ? '#' . $link['fragment'] : '') 
                                            : ($link['url'] ?? '#');
                                        
                                        $isLinkActive = request()->fullUrl() === $url || (isset($link['route']) && request()->routeIs($link['route']));
                                    @endphp
                                    <li role="none">
                                        <a href="{{ $url }}" 
                                           role="menuitem"
                                           class="group/item flex items-start gap-2.5 p-2 rounded-xl transition-all {{ $isLinkActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'hover:bg-emerald-50/80 text-stone-700 hover:text-emerald-900' }}">
                                            @if(!empty($link['icon']))
                                                <div class="h-8 w-8 rounded-lg bg-stone-100 group-hover/item:bg-emerald-100 group-hover/item:scale-105 flex items-center justify-center text-sm shrink-0 transition-all text-stone-600 group-hover/item:text-emerald-800">
                                                    {{ $link['icon'] }}
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="text-xs sm:text-sm font-bold text-stone-800 group-hover/item:text-emerald-900 transition-colors leading-tight">
                                                        {{ $linkLabel }}
                                                    </span>
                                                    @if(!empty($link['badge']))
                                                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 shrink-0">
                                                            {{ $link['badge'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if(!empty($link['desc']))
                                                    <p class="text-[11px] text-stone-500 group-hover/item:text-stone-600 line-clamp-1 leading-snug mt-0.5">
                                                        {{ $link['desc'] }}
                                                    </p>
                                                @endif
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>

                <!-- Featured Highlight Card Column -->
                @if(!empty($item['featured']))
                    <div class="md:col-span-4 lg:col-span-3 flex flex-col justify-between p-5 rounded-2xl bg-gradient-to-br from-emerald-900 via-emerald-950 to-stone-900 text-white shadow-md relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 text-7xl opacity-10 select-none pointer-events-none">
                            {{ $item['featured']['icon'] ?? '🌾' }}
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-2.5">
                                <span class="h-6 w-6 rounded-lg bg-emerald-800/80 border border-emerald-700 flex items-center justify-center text-xs">
                                    {{ $item['featured']['icon'] ?? '⚡' }}
                                </span>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-300">
                                    Featured Focus
                                </span>
                            </div>
                            <h4 class="text-sm sm:text-base font-bold text-white mb-1.5 leading-snug">
                                {{ $item['featured']['title'] }}
                            </h4>
                            <p class="text-xs text-emerald-100/90 leading-relaxed mb-4">
                                {{ $item['featured']['desc'] }}
                            </p>
                        </div>
                        <div class="relative z-10 pt-3 border-t border-emerald-800/60">
                            @php
                                $ctaUrl = isset($item['featured']['cta_route']) 
                                    ? route($item['featured']['cta_route']) . (!empty($item['featured']['cta_fragment']) ? '#' . $item['featured']['cta_fragment'] : '') 
                                    : '#';
                            @endphp
                            <a href="{{ $ctaUrl }}" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-300 hover:text-white transition-colors group/cta">
                                <span>{{ $item['featured']['cta_label'] }}</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Bottom Sub-bar inside Mega Menu -->
            <div class="mt-5 pt-3 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                <span class="flex items-center gap-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    <span>NxtPerson Gujarat Agritech Intelligence • All routes active</span>
                </span>
                <span class="text-stone-400">Press <kbd class="px-1.5 py-0.5 bg-stone-100 border border-stone-300 rounded font-mono text-[10px] text-stone-600">Esc</kbd> to close</span>
            </div>
        </div>
    </div>
@endif
