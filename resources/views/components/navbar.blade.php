@php
    $currentLocale = app()->getLocale();
    $authUser = \Illuminate\Support\Facades\Auth::user();
    // Prioritize actual authenticated user role, fallback to demo session role
    $currentRole = $authUser ? $authUser->role : session('demo_role', 'farmer');

    $roleLabels = [
        'farmer' => ['label' => 'Farmer Mode', 'icon' => '🌾', 'color' => 'bg-emerald-100 text-emerald-800 border-emerald-300'],
        'supplier' => ['label' => 'Supplier Mode', 'icon' => '🏢', 'color' => 'bg-amber-100 text-amber-800 border-amber-300'],
        'distributor' => ['label' => 'Supplier Mode', 'icon' => '🏢', 'color' => 'bg-amber-100 text-amber-800 border-amber-300'],
        'buyer' => ['label' => 'Buyer Mode', 'icon' => '🛒', 'color' => 'bg-blue-100 text-blue-800 border-blue-300'],
        'fpo' => ['label' => 'FPO / Coop Mode', 'icon' => '🤝', 'color' => 'bg-teal-100 text-teal-800 border-teal-300'],
        'cooperative' => ['label' => 'FPO / Coop Mode', 'icon' => '🤝', 'color' => 'bg-teal-100 text-teal-800 border-teal-300'],
        'admin' => ['label' => 'Admin Mode', 'icon' => '🛡️', 'color' => 'bg-purple-100 text-purple-800 border-purple-300'],
    ];
    $navItems = config('navigation.items', []);

    // Role-aware single primary header CTA
    if ($currentRole === 'admin') {
        $ctaRoute = route('admin.index');
        $ctaLabel = __('messages.nav.admin_dashboard') ?: 'Admin Dashboard';
        $ctaIcon = '🛡️';
        $ctaAriaLabel = 'Admin Dashboard';
    } elseif ($currentRole === 'fpo' || $currentRole === 'cooperative') {
        $ctaRoute = route('fpo.dashboard');
        $ctaLabel = 'FPO Portal';
        $ctaIcon = '🤝';
        $ctaAriaLabel = 'FPO Cooperative Dashboard';
    } elseif ($currentRole === 'supplier' || $currentRole === 'distributor') {
        $ctaRoute = route('supplier.dashboard');
        $ctaLabel = 'Supplier Portal';
        $ctaIcon = '🏢';
        $ctaAriaLabel = 'Supplier Business Dashboard';
    } elseif ($currentRole === 'buyer') {
        $ctaRoute = route('buyer.marketplace');
        $ctaLabel = __('messages.nav.buy_produce') ?: 'Buy Produce';
        $ctaIcon = '🛒';
        $ctaAriaLabel = 'Buy Agricultural Produce';
    } else {
        $ctaRoute = route('farmer.sell.create');
        $ctaLabel = __('messages.nav.sell_produce') ?: 'Sell Produce';
        $ctaIcon = '📸';
        $ctaAriaLabel = 'Sell Your Produce';
    }
@endphp

<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/90 shadow-xs transition-all">
    <!-- Top Utility & Role Switcher Bar -->
    <div class="bg-[#071f37] text-slate-200 text-[11px] sm:text-xs py-1.5 px-2.5 sm:px-4 border-b border-[#051627]">
        <div class="agri-container flex items-center justify-between gap-1.5 min-w-0">
            <div class="flex items-center gap-2 min-w-0 shrink">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-[#00875a]/20 text-[#34d399] border border-[#00875a]/40 font-semibold text-[10px] truncate">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#34d399] animate-pulse shrink-0"></span>
                    <span class="truncate">{{ __('messages.simulated_tag') }}</span>
                </span>
                <span class="hidden md:inline text-slate-300 text-[11px] truncate">
                    {{ __('messages.demo_banner') }}
                </span>
            </div>

            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <!-- Role Switcher Form -->
                <form action="{{ route('demo.switch-role') }}" method="POST" class="hidden sm:flex items-center gap-1.5">
                    @csrf
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Persona:</span>
                    <select name="role" onchange="this.form.submit()" class="bg-[#0c365b] text-white text-[11px] font-semibold rounded-lg border border-slate-600/60 py-0.5 px-2 focus:ring-1 focus:ring-[#34d399] cursor-pointer max-w-[150px] sm:max-w-none truncate" aria-label="Select Demo Persona">
                        <option value="farmer" {{ $currentRole === 'farmer' ? 'selected' : '' }}>🌾 Farmer (Suresh)</option>
                        <option value="supplier" {{ in_array($currentRole, ['supplier', 'distributor']) ? 'selected' : '' }}>🏢 Supplier (Kisan Seva)</option>
                        <option value="buyer" {{ $currentRole === 'buyer' ? 'selected' : '' }}>🛒 Buyer (Adani Agri)</option>
                        <option value="fpo" {{ in_array($currentRole, ['fpo', 'cooperative']) ? 'selected' : '' }}>🤝 FPO (Saurashtra Kisan)</option>
                        <option value="admin" {{ $currentRole === 'admin' ? 'selected' : '' }}>🛡️ Admin (Dr. Alok)</option>
                    </select>
                </form>

                <!-- Language Switcher -->
                <div class="flex items-center gap-1 border-slate-700 sm:border-l sm:pl-2.5 text-[11px] shrink-0 font-medium">
                    <a href="{{ route('locale.set', 'en') }}" class="px-1.5 py-0.5 rounded transition-colors {{ $currentLocale === 'en' ? 'bg-[#00875a] text-white font-bold' : 'text-slate-300 hover:text-white' }}">EN</a>
                    <span class="text-slate-600">|</span>
                    <a href="{{ route('locale.set', 'hi') }}" class="px-1.5 py-0.5 rounded transition-colors {{ $currentLocale === 'hi' ? 'bg-[#00875a] text-white font-bold' : 'text-slate-300 hover:text-white' }}">हिंदी</a>
                    <span class="text-slate-600">|</span>
                    <a href="{{ route('locale.set', 'gu') }}" class="px-1.5 py-0.5 rounded transition-colors {{ $currentLocale === 'gu' ? 'bg-[#00875a] text-white font-bold' : 'text-slate-300 hover:text-white' }}">ગુજરાતી</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar with relative container for full-width mega menu positioning -->
    <div class="agri-container relative">
        <div class="flex items-center justify-between h-16 sm:h-20 gap-2 sm:gap-4 min-w-0">
            <!-- Brand Logo (Reference-styled) -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 group shrink-0 min-w-0 focus:outline-hidden focus:ring-2 focus:ring-[#0c365b] rounded-lg p-0.5" aria-label="NxtPerson Home">
                <x-logo size="md" />
                <span class="text-[9px] sm:text-[10px] font-bold tracking-wider uppercase px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 hidden xl:inline-block shrink-0">AgriTech</span>
                <span class="sr-only">NxtPerson</span>
            </a>

            <!-- Desktop Agritech Navigation Menu (Clean 5 items) -->
            <nav class="hidden lg:flex items-center gap-0.5 xl:gap-2 text-[11px] xl:text-sm font-semibold text-slate-700 shrink-0" aria-label="Main Navigation">
                @foreach($navItems as $item)
                    <x-mega-menu :item="$item" />
                @endforeach
            </nav>

            <!-- Actions & Active Persona Controls -->
            <div class="flex items-center gap-1 sm:gap-3 shrink-0">
                <!-- Multi-Role Notification Bell & Dropdown Menu -->
                <div class="relative inline-block" id="notification-dropdown-container">
                    <button type="button" 
                            id="notification-bell-btn" 
                            class="relative p-1.5 sm:p-2 rounded-full text-slate-700 hover:text-[#0c365b] hover:bg-slate-100 transition-colors shrink-0 focus:outline-hidden focus:ring-2 focus:ring-[#0c365b] cursor-pointer" 
                            title="Notifications" 
                            aria-label="Notifications"
                            aria-expanded="false"
                            aria-haspopup="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span id="notification-unread-badge" class="hidden absolute top-0.5 right-0.5 min-w-[14px] h-3.5 px-0.5 bg-red-600 text-white text-[9px] font-bold rounded-full items-center justify-center animate-pulse">0</span>
                        <span id="buyer-unread-badge" class="hidden"></span>
                        <span id="buyer-bell-btn" class="sr-only">Notifications</span>
                    </button>

                    <!-- Dropdown Panel -->
                    <div id="notification-dropdown-panel" 
                         class="hidden absolute right-0 mt-2 w-72 sm:w-88 md:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200/90 z-50 overflow-hidden"
                         role="region"
                         aria-label="Notifications Panel">
                        <!-- Header -->
                        <div class="p-3 bg-gradient-to-r from-[#0c365b] to-[#071f37] text-white flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-xs sm:text-sm font-bold">Notifications</span>
                                <span id="dropdown-unread-pill" class="text-[10px] bg-[#00875a] text-white font-extrabold px-2 py-0.5 rounded-full hidden">0 Unread</span>
                            </div>
                            <button type="button" 
                                    id="dropdown-mark-all-btn" 
                                    class="text-[11px] text-slate-300 hover:text-white underline font-semibold transition-colors cursor-pointer">
                                Mark all as read
                            </button>
                        </div>

                        <!-- Dropdown Items List -->
                        <div id="dropdown-items-list" class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                            <div id="dropdown-empty" class="p-6 text-center text-xs text-slate-500">
                                <div class="text-2xl mb-1.5">🔔</div>
                                <p class="font-bold text-slate-700">No unread notifications</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">You're caught up with your updates.</p>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="p-2.5 bg-slate-50 border-t border-slate-100 text-center">
                            <a href="{{ route('notifications.index') }}" 
                               class="text-xs font-bold text-[#0c365b] hover:text-[#00875a] inline-flex items-center gap-1 transition-colors">
                                <span>View all notification history</span>
                                <span>→</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Register Pill Button -->
                <a href="{{ route('register') }}" 
                   class="hidden md:inline-flex items-center justify-center px-3 py-1.5 xl:px-4 xl:py-2 text-xs xl:text-sm font-semibold rounded-full border border-emerald-300 text-emerald-800 hover:bg-emerald-50 hover:border-emerald-500 transition-all whitespace-nowrap shrink-0">
                    Register
                </a>

                <!-- Login Outline Pill Button (Reference-aligned) -->
                <a href="{{ route('login') }}" 
                   class="hidden md:inline-flex items-center justify-center px-3 py-1.5 xl:px-5 xl:py-2 text-xs xl:text-sm font-semibold rounded-full border border-slate-300 text-[#0c365b] hover:bg-slate-50 hover:border-slate-400 transition-all whitespace-nowrap shrink-0">
                    Login
                </a>

                <!-- Single Primary Header CTA (Solid Navy Pill Button from Reference) -->
                <a href="{{ $ctaRoute }}" 
                   id="header-primary-cta" 
                   class="inline-flex items-center justify-center gap-1 sm:gap-1.5 px-2.5 py-1.5 sm:px-3 sm:py-1.5 xl:px-5 xl:py-2.5 text-[11px] sm:text-xs xl:text-sm font-bold rounded-full bg-[#0c365b] hover:bg-[#071f37] text-white shadow-sm hover:shadow-md transition-all whitespace-nowrap shrink-0 group" 
                   aria-label="{{ $ctaAriaLabel }}">
                    <span class="text-xs sm:text-sm shrink-0">{{ $ctaIcon }}</span>
                    <span>{{ $ctaLabel }}</span>
                    <span class="hidden md:inline group-hover:translate-x-0.5 transition-transform">→</span>
                </a>

                <!-- Mobile Hamburger Toggle Button -->
                <button type="button" 
                        id="mobile-drawer-btn" 
                        class="lg:hidden p-1.5 sm:p-2 rounded-xl text-slate-700 hover:text-slate-900 hover:bg-slate-100 touch-target focus:outline-hidden focus:ring-2 focus:ring-[#0c365b] transition-colors cursor-pointer shrink-0" 
                        aria-label="Toggle Navigation Drawer" 
                        aria-expanded="false" 
                        aria-controls="mobile-drawer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path id="hamburger-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path id="close-icon" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer Navigation with Accordions -->
    <x-mobile-drawer :items="$navItems" :currentRole="$currentRole" :currentLocale="$currentLocale" :roleLabels="$roleLabels" />
</header>

<style>
    /* Open states for desktop mega menu */
    @media (min-width: 1024px) {
        .mega-menu-item.is-open .mega-menu-panel {
            opacity: 1 !important;
            transform: translateY(0) !important;
            visibility: visible !important;
            pointer-events: auto !important;
        }
        .mega-menu-item.is-open .chevron-icon {
            transform: rotate(180deg);
        }
        .mega-menu-item.is-open .mega-menu-trigger {
            background-color: rgba(240, 253, 244, 0.9);
            color: #065f46;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Desktop Mega Menu Controller ---
        const megaItems = document.querySelectorAll('.mega-menu-item');
        let closeTimer = null;
        let activeItem = null;

        function closeAllMegaMenus() {
            megaItems.forEach(item => {
                item.classList.remove('is-open');
                const trigger = item.querySelector('.mega-menu-trigger');
                if (trigger) {
                    trigger.setAttribute('aria-expanded', 'false');
                }
            });
            activeItem = null;
        }

        function openMegaMenu(item) {
            if (closeTimer) {
                clearTimeout(closeTimer);
                closeTimer = null;
            }
            if (activeItem && activeItem !== item) {
                activeItem.classList.remove('is-open');
                const prevTrigger = activeItem.querySelector('.mega-menu-trigger');
                if (prevTrigger) prevTrigger.setAttribute('aria-expanded', 'false');
            }
            item.classList.add('is-open');
            const trigger = item.querySelector('.mega-menu-trigger');
            if (trigger) {
                trigger.setAttribute('aria-expanded', 'true');
            }
            activeItem = item;
        }

        megaItems.forEach(item => {
            const trigger = item.querySelector('.mega-menu-trigger');

            // Mouse hover handling with smooth intent bridge
            item.addEventListener('mouseenter', function () {
                if (window.innerWidth >= 1024) {
                    openMegaMenu(item);
                }
            });

            item.addEventListener('mouseleave', function () {
                if (window.innerWidth >= 1024) {
                    closeTimer = setTimeout(function () {
                        closeAllMegaMenus();
                    }, 180);
                }
            });

            // Touch & Click toggle handling
            if (trigger) {
                trigger.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isOpen = item.classList.contains('is-open');
                    if (isOpen) {
                        closeAllMegaMenus();
                    } else {
                        openMegaMenu(item);
                    }
                });

                // Keyboard handling for Enter & Space
                trigger.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        const isOpen = item.classList.contains('is-open');
                        if (isOpen) {
                            closeAllMegaMenus();
                        } else {
                            openMegaMenu(item);
                        }
                    }
                });
            }
        });

        // Close when clicking anywhere outside
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.mega-menu-item')) {
                closeAllMegaMenus();
            }
        });

        // Close on Escape key press
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                if (activeItem) {
                    const trigger = activeItem.querySelector('.mega-menu-trigger');
                    closeAllMegaMenus();
                    if (trigger) trigger.focus();
                }
                const mobileDrawer = document.getElementById('mobile-drawer');
                if (mobileDrawer && !mobileDrawer.classList.contains('hidden')) {
                    toggleMobileDrawer(false);
                }
            }
        });

        // --- Mobile Drawer & Accordion Controller ---
        const drawerBtn = document.getElementById('mobile-drawer-btn');
        const mobileDrawer = document.getElementById('mobile-drawer');
        const hamburgerIcon = document.getElementById('hamburger-icon');
        const closeIcon = document.getElementById('close-icon');

        function toggleMobileDrawer(forceState) {
            if (!mobileDrawer) return;
            const isOpen = forceState !== undefined ? !forceState : mobileDrawer.classList.contains('hidden');
            
            if (isOpen) {
                mobileDrawer.classList.remove('hidden');
                if (hamburgerIcon) hamburgerIcon.classList.add('hidden');
                if (closeIcon) closeIcon.classList.remove('hidden');
                if (drawerBtn) drawerBtn.setAttribute('aria-expanded', 'true');
            } else {
                mobileDrawer.classList.add('hidden');
                if (hamburgerIcon) hamburgerIcon.classList.remove('hidden');
                if (closeIcon) closeIcon.classList.add('hidden');
                if (drawerBtn) drawerBtn.setAttribute('aria-expanded', 'false');
            }
        }

        if (drawerBtn) {
            drawerBtn.addEventListener('click', function () {
                toggleMobileDrawer();
            });
        }

        // Accordion handling inside mobile drawer
        const accordionTriggers = document.querySelectorAll('.mobile-accordion-trigger');
        accordionTriggers.forEach(btn => {
            btn.addEventListener('click', function () {
                const group = btn.closest('.mobile-accordion-group');
                const content = group ? group.querySelector('.mobile-accordion-content') : null;
                const chevron = btn.querySelector('.accordion-chevron');
                
                if (content) {
                    const isExpanded = !content.classList.contains('hidden');
                    if (isExpanded) {
                        content.classList.add('hidden');
                        btn.setAttribute('aria-expanded', 'false');
                        if (chevron) chevron.classList.remove('rotate-180');
                    } else {
                        content.classList.remove('hidden');
                        btn.setAttribute('aria-expanded', 'true');
                        if (chevron) chevron.classList.add('rotate-180');
                    }
                }
            });
        });
    });
</script>
