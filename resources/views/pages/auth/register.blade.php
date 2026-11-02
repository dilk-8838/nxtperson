@extends('layouts.app')

@section('title', 'Create Your Account — NxtPerson Agritech')

@section('content')
<div class="bg-gradient-to-b from-slate-50 via-white to-slate-50 py-10 sm:py-16">
    <div class="agri-container max-w-4xl mx-auto px-4 sm:px-6">
        
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-10">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-[#00875a] text-xs font-bold uppercase tracking-wider mb-3">
                Secure Portal Onboarding
            </span>
            <h1 class="text-2xl sm:text-4xl font-extrabold text-[#0c365b] tracking-tight">
                Join the NxtPerson Agricultural Ecosystem
            </h1>
            <p class="text-sm sm:text-base text-slate-600 mt-2">
                Select your stakeholder category to configure your dedicated portal, business credentials, and role-based permissions.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>Please correct the errors below to continue:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-xs text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Container -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xl overflow-hidden">
            
            <!-- Role Selector Tabs -->
            <div class="border-b border-slate-200 bg-slate-50/80 p-2 sm:p-3">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center" id="role-selector-container">
                    <button type="button" 
                            data-role="farmer"
                            class="role-tab flex items-center justify-center gap-2 py-3 px-3 rounded-2xl font-bold text-xs sm:text-sm transition-all cursor-pointer {{ (old('role', $role) === 'farmer') ? 'bg-[#0c365b] text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/70' }}">
                        <span>🌾</span>
                        <span>Farmer</span>
                    </button>
                    <button type="button" 
                            data-role="supplier"
                            class="role-tab flex items-center justify-center gap-2 py-3 px-3 rounded-2xl font-bold text-xs sm:text-sm transition-all cursor-pointer {{ (old('role', $role) === 'supplier') ? 'bg-[#0c365b] text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/70' }}">
                        <span>🏢</span>
                        <span>Supplier</span>
                    </button>
                    <button type="button" 
                            data-role="buyer"
                            class="role-tab flex items-center justify-center gap-2 py-3 px-3 rounded-2xl font-bold text-xs sm:text-sm transition-all cursor-pointer {{ (old('role', $role) === 'buyer') ? 'bg-[#0c365b] text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/70' }}">
                        <span>🛒</span>
                        <span>Buyer</span>
                    </button>
                    <button type="button" 
                            data-role="fpo"
                            class="role-tab flex items-center justify-center gap-2 py-3 px-3 rounded-2xl font-bold text-xs sm:text-sm transition-all cursor-pointer {{ (old('role', $role) === 'fpo') ? 'bg-[#0c365b] text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/70' }}">
                        <span>🤝</span>
                        <span>FPO / Coop</span>
                    </button>
                </div>
            </div>

            <!-- Registration Form -->
            <form action="{{ route('register.submit') }}" method="POST" id="registration-form" class="p-6 sm:p-10 space-y-8">
                @csrf
                <input type="hidden" name="role" id="selected-role-input" value="{{ old('role', $role) }}">

                <!-- Role Explanation Banner -->
                <div id="role-banner" class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 flex items-start gap-3">
                    <span id="role-banner-icon" class="text-2xl shrink-0">🌾</span>
                    <div>
                        <h4 id="role-banner-title" class="text-sm font-bold text-[#0c365b]">Individual Farmer Account</h4>
                        <p id="role-banner-desc" class="text-xs text-slate-600 mt-0.5">
                            Direct farm produce trading, real-time AI pest & disease diagnostics, APMC mandi telemetry, and satellite advisories.
                        </p>
                    </div>
                </div>

                <!-- Section 1: Personal & Account Credentials -->
                <div>
                    <h3 class="text-base font-bold text-[#0c365b] pb-2 border-b border-slate-200 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-[#0c365b]/10 text-[#0c365b] inline-flex items-center justify-center text-xs">1</span>
                        <span>Personal Credentials</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Full Name / Authorized Representative <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   placeholder="e.g. Ramesh Patel"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#00875a] focus:border-transparent outline-none transition-all">
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Mobile Number (10 Digits) <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required
                                   placeholder="e.g. 9876543210"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#00875a] focus:border-transparent outline-none transition-all">
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   placeholder="e.g. ramesh@kisan.in"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#00875a] focus:border-transparent outline-none transition-all">
                        </div>

                        <div>
                            <label for="district" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                District <span class="text-red-500">*</span>
                            </label>
                            <select name="district" id="district" required
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#00875a] focus:border-transparent outline-none transition-all bg-white">
                                <option value="">Select District</option>
                                @foreach(['Rajkot', 'Anand', 'Banaskantha', 'Surat', 'Mehsana', 'Junagadh', 'Ahmedabad', 'Kheda', 'Vadodara', 'Surendranagar', 'Bhavnagar', 'Amreli'] as $dst)
                                    <option value="{{ $dst }}" {{ old('district') === $dst ? 'selected' : '' }}>{{ $dst }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Account Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" id="password" required minlength="8"
                                   placeholder="Minimum 8 characters"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#00875a] focus:border-transparent outline-none transition-all">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                                   placeholder="Repeat password"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#00875a] focus:border-transparent outline-none transition-all">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Role Specific Profiles -->
                
                <!-- Farmer Fields -->
                <div id="fields-farmer" class="role-specific-section">
                    <h3 class="text-base font-bold text-[#0c365b] pb-2 border-b border-slate-200 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-[#00875a]/10 text-[#00875a] inline-flex items-center justify-center text-xs">2</span>
                        <span>Farm Land & Crop Specifications</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="farm_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Farm or Holding Name</label>
                            <input type="text" name="farm_name" id="farm_name" value="{{ old('farm_name') }}" placeholder="e.g. Patel Organic Orchards"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#00875a] outline-none">
                        </div>
                        <div>
                            <label for="village" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Village / Taluka</label>
                            <input type="text" name="village" id="village" value="{{ old('village') }}" placeholder="e.g. Gondal"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#00875a] outline-none">
                        </div>
                        <div>
                            <label for="total_acreage" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Total Land Area (Acres)</label>
                            <input type="number" step="0.1" name="total_acreage" id="total_acreage" value="{{ old('total_acreage', 5.0) }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#00875a] outline-none">
                        </div>
                        <div>
                            <label for="irrigation_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Primary Irrigation Method</label>
                            <select name="irrigation_type" id="irrigation_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm bg-white">
                                <option value="drip">Drip Irrigation (Micro-irrigation)</option>
                                <option value="borewell">Borewell / Tube Well</option>
                                <option value="canal">Canal Gravity Flow</option>
                                <option value="sprinkler">Sprinkler System</option>
                                <option value="rainfed">Rainfed (Barani)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Supplier Fields -->
                <div id="fields-supplier" class="role-specific-section hidden">
                    <h3 class="text-base font-bold text-[#0c365b] pb-2 border-b border-slate-200 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-amber-500/10 text-amber-600 inline-flex items-center justify-center text-xs">2</span>
                        <span>Supplier Organization & Trade Licensing</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="business_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Business / Firm Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="business_name" id="business_name" value="{{ old('business_name') }}" placeholder="e.g. Kisan Seva Inputs LLP"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#00875a] outline-none">
                        </div>
                        <div>
                            <label for="business_category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Input Category <span class="text-red-500">*</span>
                            </label>
                            <select name="business_category" id="business_category" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm bg-white">
                                <option value="seeds">Certified Hybrid & Organic Seeds</option>
                                <option value="fertilizers">Fertilizers & Soil Amendments</option>
                                <option value="crop_protection">Bio-Protection & Crop Defense</option>
                                <option value="equipment">Smart Farm Equipment & Drones</option>
                                <option value="multi_category">Multi-Category Agro Inputs</option>
                            </select>
                        </div>
                        <div>
                            <label for="gst_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">GST Identification Number (GSTIN)</label>
                            <input type="text" name="gst_number" id="gst_number" value="{{ old('gst_number') }}" placeholder="e.g. 24AAACA1234A1Z5"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm uppercase">
                        </div>
                        <div>
                            <label for="license_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Fertilizer/Seed Dealer License</label>
                            <input type="text" name="license_number" id="license_number" value="{{ old('license_number') }}" placeholder="e.g. GUJ/AGRI/DIST/2026/889"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm uppercase">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="operating_address_supplier" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Registered Business Address <span class="text-red-500">*</span>
                            </label>
                            <textarea name="operating_address" id="operating_address_supplier" rows="2" placeholder="Full postal address of depot, factory, or office"
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">{{ old('operating_address') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Buyer Fields -->
                <div id="fields-buyer" class="role-specific-section hidden">
                    <h3 class="text-base font-bold text-[#0c365b] pb-2 border-b border-slate-200 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-blue-500/10 text-blue-600 inline-flex items-center justify-center text-xs">2</span>
                        <span>Procurement Company Details</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="company_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Company / Enterprise Name</label>
                            <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}" placeholder="e.g. Saurashtra Agro Foods Ltd"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                        </div>
                        <div>
                            <label for="buyer_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Procurement Business Type</label>
                            <select name="buyer_type" id="buyer_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm bg-white">
                                <option value="processor">Food Processor / Mill</option>
                                <option value="exporter">Commodity Exporter</option>
                                <option value="wholesaler">Mandi Wholesaler / Stockist</option>
                                <option value="retail_chain">Supermarket / Retail Chain</option>
                                <option value="individual">Direct Bulk Buyer</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- FPO Fields -->
                <div id="fields-fpo" class="role-specific-section hidden">
                    <h3 class="text-base font-bold text-[#0c365b] pb-2 border-b border-slate-200 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-teal-500/10 text-teal-600 inline-flex items-center justify-center text-xs">2</span>
                        <span>FPO / Cooperative Society Registration</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="fpo_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                FPO / Cooperative Full Legal Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="fpo_name" id="fpo_name" value="{{ old('fpo_name') }}" placeholder="e.g. Saurashtra Kisan Producer Company Ltd"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                        </div>
                        <div>
                            <label for="organization_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Organization Type <span class="text-red-500">*</span>
                            </label>
                            <select name="organization_type" id="organization_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm bg-white">
                                <option value="Farmer Producer Company (FPC)">Farmer Producer Company (FPC - Companies Act)</option>
                                <option value="Cooperative Society">Cooperative Society (State Coop Act)</option>
                                <option value="Multi-State Cooperative">Multi-State Cooperative Society</option>
                                <option value="Self-Help Group Federation">SHG / JLGs Federation</option>
                            </select>
                        </div>
                        <div>
                            <label for="registration_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                CIN or Society Registration Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number') }}" placeholder="e.g. U01100GJ2023PTC140221"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm uppercase">
                        </div>
                        <div>
                            <label for="incorporation_year" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Year of Incorporation</label>
                            <input type="number" name="incorporation_year" id="incorporation_year" value="{{ old('incorporation_year', 2023) }}" min="1950" max="{{ date('Y') }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                        </div>
                    </div>
                </div>

                <!-- Submit Button & Terms -->
                <div class="pt-4 border-t border-slate-200">
                    <button type="submit" 
                            id="submit-register-btn"
                            class="w-full py-4 px-6 rounded-2xl bg-[#0c365b] hover:bg-[#071f37] text-white font-extrabold text-sm sm:text-base shadow-lg hover:shadow-xl transition-all cursor-pointer flex items-center justify-center gap-2">
                        <span>Establish Account & Enter Dashboard</span>
                        <span>→</span>
                    </button>
                    <p class="text-center text-xs text-slate-500 mt-3">
                        By registering, you confirm that your details are genuine. Supplier & FPO entities require administrative verification before publishing listings.
                    </p>
                </div>
            </form>

            <!-- Bottom Login Switcher -->
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 text-center text-xs text-slate-600">
                Already registered with an active role? 
                <a href="{{ route('login') }}" class="font-bold text-[#0c365b] hover:underline ml-1">
                    Sign in to your account
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleTabs = document.querySelectorAll('.role-tab');
        const roleInput = document.getElementById('selected-role-input');
        const sections = {
            farmer: document.getElementById('fields-farmer'),
            supplier: document.getElementById('fields-supplier'),
            buyer: document.getElementById('fields-buyer'),
            fpo: document.getElementById('fields-fpo')
        };
        const bannerIcon = document.getElementById('role-banner-icon');
        const bannerTitle = document.getElementById('role-banner-title');
        const bannerDesc = document.getElementById('role-banner-desc');

        const roleMeta = {
            farmer: {
                icon: '🌾',
                title: 'Individual Farmer Account',
                desc: 'Direct farm produce trading, real-time AI pest & disease diagnostics, APMC mandi telemetry, and satellite advisories.'
            },
            supplier: {
                icon: '🏢',
                title: 'Agri Inputs & Equipment Supplier Organization',
                desc: 'Distribute certified hybrid seeds, fertilizers, crop defense inputs, and machinery across state dealer networks.'
            },
            buyer: {
                icon: '🛒',
                title: 'Institutional Buyer & Commodity Processor',
                desc: 'Procure verified, graded agricultural produce directly from verified farmers and aggregated FPO supply chains.'
            },
            fpo: {
                icon: '🤝',
                title: 'Farmer Producer Organization (FPO / Cooperative)',
                desc: 'Aggregate member farmer holdings, manage collective land acreage, track harvest forecasts, and trade at volume.'
            }
        };

        function switchRole(selectedRole) {
            roleInput.value = selectedRole;

            // Update tab styles
            roleTabs.forEach(tab => {
                const role = tab.getAttribute('data-role');
                if (role === selectedRole) {
                    tab.classList.remove('bg-white', 'text-slate-700', 'border');
                    tab.classList.add('bg-[#0c365b]', 'text-white', 'shadow-md');
                } else {
                    tab.classList.remove('bg-[#0c365b]', 'text-white', 'shadow-md');
                    tab.classList.add('bg-white', 'text-slate-700', 'border');
                }
            });

            // Show/hide sections
            Object.keys(sections).forEach(r => {
                if (sections[r]) {
                    if (r === selectedRole) {
                        sections[r].classList.remove('hidden');
                    } else {
                        sections[r].classList.add('hidden');
                    }
                }
            });

            // Update banner
            if (roleMeta[selectedRole]) {
                bannerIcon.textContent = roleMeta[selectedRole].icon;
                bannerTitle.textContent = roleMeta[selectedRole].title;
                bannerDesc.textContent = roleMeta[selectedRole].desc;
            }
        }

        roleTabs.forEach(tab => {
            tab.addEventListener('click', function () {
                const role = this.getAttribute('data-role');
                switchRole(role);
            });
        });

        // Initialize with default or current input
        switchRole(roleInput.value || 'farmer');
    });
</script>
@endsection
