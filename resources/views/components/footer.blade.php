<footer class="bg-[#071f37] text-slate-300 border-t border-[#0c365b] pt-16 pb-20 lg:pb-12 mt-20">
    <div class="agri-container">
        <!-- Top Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-slate-700/60">
            <!-- Brand & Mission (2 cols on lg) -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center gap-3">
                    <x-logo variant="light" size="lg" />
                    <span class="ml-1 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-[#00875a]/20 text-[#34d399] border border-[#00875a]/40">
                        AgriTech • Prototype
                    </span>
                </div>
                <p class="text-sm text-slate-300 leading-relaxed max-w-md">
                    <strong>{{ __('messages.tagline') }}</strong>. Developing an integrated digital agricultural marketplace and value chain to eliminate post-harvest waste and empower farmers across Gujarat and India.
                </p>
                <div class="p-3.5 rounded-xl bg-[#0c365b]/50 border border-slate-700/60 text-xs text-slate-300">
                    <p class="font-semibold text-white mb-1 flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full bg-[#34d399]"></span>
                        Demonstration Platform Transparency Note:
                    </p>
                    <p>
                        This system is an architectural demonstration aligned with the corporate business document: Integrated Open Marketplace. Live features (crop advisory, weather, mandi, input directory) and forward-looking roadmap capabilities (AI grading, physical sensors, produce trading) are transparently labeled.
                    </p>
                </div>
            </div>

            <!-- 8 Target Crops Links -->
            <div>
                <h4 class="text-sm font-semibold uppercase tracking-wider text-stone-100 mb-4 flex items-center gap-2">
                    <span class="text-emerald-400">🌾</span> Target Crops
                </h4>
                <ul class="space-y-2 text-sm text-stone-400">
                    <li><a href="{{ route('crops.show', 'cotton') }}" class="hover:text-emerald-400 transition-colors">Cotton (કપાસ / कपास)</a></li>
                    <li><a href="{{ route('crops.show', 'banana') }}" class="hover:text-emerald-400 transition-colors">Banana (કેળા / केला)</a></li>
                    <li><a href="{{ route('crops.show', 'potato') }}" class="hover:text-emerald-400 transition-colors">Potato (બટાકા / आलू)</a></li>
                    <li><a href="{{ route('crops.show', 'rice') }}" class="hover:text-emerald-400 transition-colors">Rice / Paddy (ડાંગર)</a></li>
                    <li><a href="{{ route('crops.show', 'grain') }}" class="hover:text-emerald-400 transition-colors">Wheat & Grain (ઘઉં)</a></li>
                    <li><a href="{{ route('crops.show', 'tomato') }}" class="hover:text-emerald-400 transition-colors">Tomato (ટામેટા / टमाटर)</a></li>
                    <li><a href="{{ route('crops.show', 'cauliflower') }}" class="hover:text-emerald-400 transition-colors">Cauliflower (ફૂલકોબી)</a></li>
                    <li><a href="{{ route('crops.show', 'chilli') }}" class="hover:text-emerald-400 transition-colors">Chilli (મરચા / मिर्च)</a></li>
                </ul>
            </div>

            <!-- Agritech Services / 4 Solutions -->
            <div>
                <h4 class="text-sm font-semibold uppercase tracking-wider text-stone-100 mb-4 flex items-center gap-2">
                    <span class="text-emerald-400">⚡</span> Four Core Pillars
                </h4>
                <ul class="space-y-2 text-sm text-stone-400">
                    <li><a href="{{ route('solutions.quality-grading') }}" class="hover:text-emerald-400 transition-colors">AI Quality Assurance</a></li>
                    <li><a href="{{ route('solutions.precision-agri') }}" class="hover:text-emerald-400 transition-colors">Precision Agriculture</a></li>
                    <li><a href="{{ route('solutions.digital-farmer') }}" class="hover:text-emerald-400 transition-colors">Digital Farmer Platform</a></li>
                    <li><a href="{{ route('solutions.marketplace') }}" class="hover:text-emerald-400 transition-colors">Integrated Marketplace</a></li>
                    <li><a href="{{ route('ecosystem.index') }}" class="hover:text-emerald-400 transition-colors">Stakeholder Ecosystem</a></li>
                    <li><a href="{{ route('roadmap.index') }}" class="hover:text-emerald-400 transition-colors">Planned Roadmap (2026–31)</a></li>
                </ul>
            </div>

            <!-- Gujarat Coverage & Support -->
            <div>
                <h4 class="text-sm font-semibold uppercase tracking-wider text-stone-100 mb-4 flex items-center gap-2">
                    <span class="text-emerald-400">📍</span> Gujarat Agro Zones
                </h4>
                <ul class="space-y-2 text-xs text-stone-400 leading-relaxed">
                    <li class="pb-1 border-b border-stone-800">
                        <strong class="text-stone-300">Saurashtra Zone:</strong><br>Rajkot, Gondal, Amreli (Cotton, Groundnut, Grain)
                    </li>
                    <li class="pb-1 border-b border-stone-800">
                        <strong class="text-stone-300">North Gujarat:</strong><br>Unjha, Mehsana, Deesa (Cumin, Potato, Mustard)
                    </li>
                    <li class="pb-1 border-b border-stone-800">
                        <strong class="text-stone-300">Central Gujarat:</strong><br>Anand, Kheda, Vadodara (Banana, Rice, Tobacco)
                    </li>
                    <li>
                        <strong class="text-stone-300">South Gujarat:</strong><br>Surat, Navsari (Sugarcane, Tomato, Chilli)
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-stone-500">
            <p>© 2026 Nxtperson Private Limited. {{ __('messages.common.rights_reserved') }}</p>
            <div class="flex items-center gap-4">
                <span class="text-stone-400">Integrated Open Marketplace Prototype</span>
                <span>•</span>
                <span class="text-stone-400">Laravel 11 & Tailwind CSS</span>
                <span>•</span>
                <span class="text-stone-400">Clean Standalone Project</span>
            </div>
        </div>
    </div>
</footer>
