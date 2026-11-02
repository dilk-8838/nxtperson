<?php

namespace Database\Seeders;

use App\Models\Advisory;
use App\Models\BuyerProfile;
use App\Models\Crop;
use App\Models\FarmerProfile;
use App\Models\FpoFarmerMembership;
use App\Models\FpoMember;
use App\Models\FpoOrganization;
use App\Models\MandiPrice;
use App\Models\SupplierMember;
use App\Models\SupplierOrganization;
use App\Models\User;
use App\Models\WeatherSnapshot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Demo Role Accounts & Secure Role Architecture Foundations
        $farmer = User::updateOrCreate(
            ['email' => 'suresh.patel@demo.nxtperson.farm'],
            [
                'name' => 'Suresh Patel',
                'phone' => '9876543210',
                'role' => 'farmer',
                'status' => 'active',
                'district' => 'Rajkot',
                'state' => 'Gujarat',
                'pincode' => '360001',
                'password' => Hash::make('demo1234'),
            ]
        );

        FarmerProfile::updateOrCreate(
            ['user_id' => $farmer->id],
            [
                'farm_name' => 'Patel Agro Farms (Gondal Cluster)',
                'village' => 'Gondal',
                'taluka' => 'Gondal',
                'district' => 'Rajkot',
                'state' => 'Gujarat',
                'pincode' => '360311',
                'total_acreage' => 18.5,
                'cultivated_acreage' => 17.0,
                'soil_type' => 'Medium Black Cotton Soil (Regur)',
                'irrigation_type' => 'drip',
                'crops_cultivated' => ['cotton', 'groundnut', 'grain'],
                'expected_harvest_date' => now()->addWeeks(6)->toDateString(),
                'estimated_harvest_quantity' => 245.0,
                'estimated_harvest_unit' => 'Quintal',
                'kisan_credit_card' => 'KCC-GJ-2024-9981',
                'survey_number' => 'SRV-104/2B',
                'verification_status' => 'verified',
                'verified_at' => now()->subMonths(3),
            ]
        );

        // Reconcile legacy distributor user phone if present to preserve uniqueness
        User::where('email', 'distributor.anand@demo.nxtperson.farm')->update(['phone' => '9876543221']);

        // Supplier 1: Approved Active Supplier
        $supplier = User::updateOrCreate(
            ['email' => 'supplier.agritech@demo.nxtperson.farm'],
            [
                'name' => 'Kisan Seva Inputs (Mahesh Shah)',
                'phone' => '9876543220',
                'role' => 'supplier',
                'status' => 'active',
                'district' => 'Anand',
                'state' => 'Gujarat',
                'pincode' => '388001',
                'password' => Hash::make('demo1234'),
            ]
        );

        // Keep legacy email alias for backward compatibility
        User::updateOrCreate(
            ['email' => 'distributor.anand@demo.nxtperson.farm'],
            [
                'name' => 'Kisan Seva Kendra (Mahesh Shah)',
                'phone' => '9876543221',
                'role' => 'distributor',
                'status' => 'active',
                'district' => 'Anand',
                'state' => 'Gujarat',
                'pincode' => '388001',
                'password' => Hash::make('demo1234'),
            ]
        );

        $supplierOrg = SupplierOrganization::updateOrCreate(
            ['slug' => 'agritech-inputs-gujarat'],
            [
                'name' => 'AgriTech Inputs Gujarat Pvt Ltd',
                'category' => 'multi_category',
                'description' => 'Manufacturer and distributor of certified hybrid seeds, micronutrients, bio-fertilizers, and modern farm equipment across Central Gujarat.',
                'gst_number' => '24AAACA1234A1Z5',
                'license_number' => 'GUJ/AGRI/DIST/2026/889',
                'authorized_representative_name' => 'Mahesh Shah',
                'contact_email' => 'supplier.agritech@demo.nxtperson.farm',
                'contact_phone' => '9876543220',
                'operating_address' => 'Plot 42, GIDC Industrial Estate, Phase 2',
                'district' => 'Anand',
                'state' => 'Gujarat',
                'pincode' => '388001',
                'verification_status' => SupplierOrganization::STATUS_APPROVED,
                'verified_at' => now()->subMonths(2),
            ]
        );

        SupplierMember::updateOrCreate(
            ['supplier_organization_id' => $supplierOrg->id, 'user_id' => $supplier->id],
            ['role_in_org' => 'owner', 'is_primary_contact' => true]
        );

        // Supplier 2: Pending Supplier for Admin Approval Queue Testing
        $pendingSupplierUser = User::updateOrCreate(
            ['email' => 'bhavin.biotech@demo.nxtperson.farm'],
            [
                'name' => 'Bhavin Vora (Bio-Nutrients)',
                'phone' => '9876543225',
                'role' => 'supplier',
                'status' => 'active',
                'district' => 'Surat',
                'state' => 'Gujarat',
                'pincode' => '395003',
                'password' => Hash::make('demo1234'),
            ]
        );

        $pendingSupplierOrg = SupplierOrganization::updateOrCreate(
            ['slug' => 'kisan-biotech-fertilizers'],
            [
                'name' => 'Kisan Biotech & Organic Fertilizers LLP',
                'category' => 'fertilizers',
                'description' => 'Certified organic bio-fertilizer manufacturing unit producing mycorrhiza and potassium mobilizing bacterial cultures.',
                'gst_number' => '24BBBPS9876B1Z3',
                'license_number' => 'SUR/ORG/BIO/2026/014',
                'authorized_representative_name' => 'Bhavin Vora',
                'contact_email' => 'bhavin.biotech@demo.nxtperson.farm',
                'contact_phone' => '9876543225',
                'operating_address' => 'Sachin GIDC Road, Surat',
                'district' => 'Surat',
                'state' => 'Gujarat',
                'pincode' => '395003',
                'verification_status' => SupplierOrganization::STATUS_PENDING,
            ]
        );

        SupplierMember::updateOrCreate(
            ['supplier_organization_id' => $pendingSupplierOrg->id, 'user_id' => $pendingSupplierUser->id],
            ['role_in_org' => 'owner', 'is_primary_contact' => true]
        );

        // Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@demo.nxtperson.farm'],
            [
                'name' => 'Dr. Alok Verma (Lead Agronomist & Admin)',
                'phone' => '9876543230',
                'role' => 'admin',
                'status' => 'active',
                'district' => 'Gandhinagar',
                'state' => 'Gujarat',
                'pincode' => '382010',
                'password' => Hash::make('demo1234'),
            ]
        );

        // Buyer User & Profile
        $buyer = User::updateOrCreate(
            ['email' => 'buyer.procurement@demo.nxtperson.farm'],
            [
                'name' => 'Adani Agri Fresh / Saurashtra Agro (Demo Buyer)',
                'phone' => '9876543240',
                'role' => 'buyer',
                'status' => 'active',
                'district' => 'Ahmedabad',
                'state' => 'Gujarat',
                'pincode' => '380001',
                'password' => Hash::make('demo1234'),
            ]
        );

        BuyerProfile::updateOrCreate(
            ['user_id' => $buyer->id],
            [
                'company_name' => 'Saurashtra Agro Commodities Ltd',
                'buyer_type' => 'processor',
                'gst_number' => '24AACCA5544C1Z9',
                'pan_number' => 'AACCA5544C',
                'trade_license' => 'FSSAI/2026/AHM/4412',
                'operating_address' => 'Floor 4, Agro Tower, SG Highway, Ahmedabad',
                'district' => 'Ahmedabad',
                'state' => 'Gujarat',
                'pincode' => '380054',
                'procurement_interests' => ['Cotton', 'Groundnut', 'Cumin', 'Sesame'],
                'annual_procurement_budget' => '₹5 Crore - ₹10 Crore',
                'verification_status' => 'verified',
                'verified_at' => now()->subMonths(4),
            ]
        );

        // FPO 1: Approved FPO & Farmer Memberships
        $fpoUser = User::updateOrCreate(
            ['email' => 'fpo.saurashtra@demo.nxtperson.farm'],
            [
                'name' => 'Saurashtra Kisan Producer Co. (Demo FPO)',
                'phone' => '9876543250',
                'role' => 'fpo',
                'status' => 'active',
                'district' => 'Rajkot',
                'state' => 'Gujarat',
                'pincode' => '360001',
                'password' => Hash::make('demo1234'),
            ]
        );

        $fpoOrg = FpoOrganization::updateOrCreate(
            ['slug' => 'saurashtra-kisan-producer-co'],
            [
                'name' => 'Saurashtra Kisan Producer Company Ltd',
                'organization_type' => 'Farmer Producer Company (FPC)',
                'registration_number' => 'U01100GJ2023PTC140221',
                'incorporation_year' => 2023,
                'description' => 'Farmer producer organization aggregating 450+ smallholder cotton, groundnut, and cumin growers across Rajkot and Gondal.',
                'registered_address' => 'Station Road, APMC Compound, Gondal',
                'village' => 'Gondal',
                'taluka' => 'Gondal',
                'district' => 'Rajkot',
                'state' => 'Gujarat',
                'pincode' => '360311',
                'authorized_representative_name' => 'Govindbhai Patel',
                'contact_email' => 'fpo.saurashtra@demo.nxtperson.farm',
                'contact_phone' => '9876543250',
                'bank_name' => 'State Bank of India (Gondal Branch)',
                'bank_account_number' => '38992010992',
                'ifsc_code' => 'SBIN0000382',
                'verification_status' => FpoOrganization::STATUS_APPROVED,
                'verified_at' => now()->subMonths(1),
            ]
        );

        FpoMember::updateOrCreate(
            ['fpo_organization_id' => $fpoOrg->id, 'user_id' => $fpoUser->id],
            ['role_in_fpo' => 'admin', 'is_primary' => true]
        );

        // Link farmer Suresh Patel as verified member without duplicating identity
        FpoFarmerMembership::updateOrCreate(
            ['fpo_organization_id' => $fpoOrg->id, 'farmer_user_id' => $farmer->id],
            [
                'member_code' => 'SKPC-MEM-001',
                'share_units' => 10,
                'status' => 'active',
                'joined_at' => now()->subMonths(6)->toDateString(),
                'notes' => 'Founding member farmer with 18.5 acres under Shankar-6 cotton and GG-20 groundnut.',
            ]
        );

        // FPO 2: Pending FPO for Admin Approval Queue Testing
        $pendingFpoUser = User::updateOrCreate(
            ['email' => 'kesar.mango.fpo@demo.nxtperson.farm'],
            [
                'name' => 'Gir Kesar Bagayat Sahkari Mandali',
                'phone' => '9876543255',
                'role' => 'fpo',
                'status' => 'active',
                'district' => 'Junagadh',
                'state' => 'Gujarat',
                'pincode' => '362001',
                'password' => Hash::make('demo1234'),
            ]
        );

        $pendingFpoOrg = FpoOrganization::updateOrCreate(
            ['slug' => 'gir-kesar-growers-coop'],
            [
                'name' => 'Gir Somnath Kesar Mango Growers Cooperative Ltd',
                'organization_type' => 'Cooperative Society',
                'registration_number' => 'COOP/JND/AGRI/2026/089',
                'incorporation_year' => 2024,
                'description' => 'Cooperative society of 180 GI-tagged Gir Kesar mango orchardists in Talala and Mendarda valleys.',
                'registered_address' => 'Talala Gir Market Yard, Junagadh',
                'village' => 'Talala',
                'taluka' => 'Talala',
                'district' => 'Junagadh',
                'state' => 'Gujarat',
                'pincode' => '362150',
                'authorized_representative_name' => 'Mansukhbhai Savaliya',
                'contact_email' => 'kesar.mango.fpo@demo.nxtperson.farm',
                'contact_phone' => '9876543255',
                'verification_status' => FpoOrganization::STATUS_PENDING,
            ]
        );

        FpoMember::updateOrCreate(
            ['fpo_organization_id' => $pendingFpoOrg->id, 'user_id' => $pendingFpoUser->id],
            ['role_in_fpo' => 'admin', 'is_primary' => true]
        );

        // 2. Seed 8 Key Gujarat Crops
        $cropsData = [
            [
                'slug' => 'cotton',
                'en_name' => 'Cotton',
                'hi_name' => 'कपास',
                'gu_name' => 'કપાસ',
                'botanical_name' => 'Gossypium hirsutum',
                'category' => 'Cash Crop',
                'season' => 'Kharif (June - Nov)',
                'icon' => '☁️',
                'badge' => 'Major Cash Crop',
                'optimal_temp' => '21°C - 35°C',
                'pests' => ['Pink Bollworm', 'Whitefly', 'Leaf Curl'],
                'mandi_price' => '₹7,150 / Qtl',
                'mandi_trend' => '+2.4%',
                'mandi_yard' => 'APMC Rajkot',
                'description' => 'Gujarat produces over 30% of Indian cotton. Key Saurashtra districts include Rajkot, Surendranagar, and Amreli.',
            ],
            [
                'slug' => 'banana',
                'en_name' => 'Banana',
                'hi_name' => 'केला',
                'gu_name' => 'કેળા',
                'botanical_name' => 'Musa acuminata (Grand Naine)',
                'category' => 'Horticulture',
                'season' => 'Perennial (Year Round)',
                'icon' => '🍌',
                'badge' => 'High Value',
                'optimal_temp' => '20°C - 38°C',
                'pests' => ['Sigatoka Leaf Spot', 'Panama Wilt', 'Rhizome Weevil'],
                'mandi_price' => '₹1,850 / Qtl',
                'mandi_trend' => '+1.1%',
                'mandi_yard' => 'APMC Surat',
                'description' => 'Central and South Gujarat banana belt around Anand, Bharuch, and Narmada river basins.',
            ],
            [
                'slug' => 'potato',
                'en_name' => 'Potato',
                'hi_name' => 'आलू',
                'gu_name' => 'બટાકા',
                'botanical_name' => 'Solanum tuberosum',
                'category' => 'Tuber',
                'season' => 'Rabi (Oct - Feb)',
                'icon' => '🥔',
                'badge' => 'Deesa Belt Focus',
                'optimal_temp' => '15°C - 24°C',
                'pests' => ['Late Blight', 'Early Blight', 'Aphids'],
                'mandi_price' => '₹1,420 / Qtl',
                'mandi_trend' => '-0.8%',
                'mandi_yard' => 'APMC Deesa',
                'description' => 'Deesa and Banaskantha are famous as the cold-storage potato hub of Western India.',
            ],
            [
                'slug' => 'rice',
                'en_name' => 'Rice (Paddy)',
                'hi_name' => 'धान/चावल',
                'gu_name' => 'ડાંગર/ચોખા',
                'botanical_name' => 'Oryza sativa (Gurjari/Jaya)',
                'category' => 'Cereal Staple',
                'season' => 'Kharif (June - Oct)',
                'icon' => '🌾',
                'badge' => 'Kheda-Anand Basin',
                'optimal_temp' => '24°C - 34°C',
                'pests' => ['Stem Borer', 'Bacterial Leaf Blight', 'Brown Planthopper'],
                'mandi_price' => '₹2,340 / Qtl',
                'mandi_trend' => '+3.1%',
                'mandi_yard' => 'APMC Anand',
                'description' => 'Canal-irrigated regions of Charotar, Kheda, and Navsari cultivating high-yielding fragrant varieties.',
            ],
            [
                'slug' => 'grain',
                'en_name' => 'Wheat & Grain',
                'hi_name' => 'गेहूं व अनाज',
                'gu_name' => 'ઘઉં અને અનાજ',
                'botanical_name' => 'Triticum aestivum (Bhalia Special)',
                'category' => 'Cereal Grain',
                'season' => 'Rabi (Nov - March)',
                'icon' => '🥖',
                'badge' => 'Bhalia Wheat GI Tag',
                'optimal_temp' => '12°C - 25°C',
                'pests' => ['Rust Disease', 'Termites', 'Karnal Bunt'],
                'mandi_price' => '₹2,820 / Qtl',
                'mandi_trend' => '+0.7%',
                'mandi_yard' => 'APMC Gondal',
                'description' => 'Geographically indicated Bhal wheat grown without irrigation on conserved soil moisture.',
            ],
            [
                'slug' => 'tomato',
                'en_name' => 'Tomato',
                'hi_name' => 'टमाटर',
                'gu_name' => 'ટામેટા',
                'botanical_name' => 'Solanum lycopersicum',
                'category' => 'Vegetable',
                'season' => 'Year Round / Kharif-Rabi',
                'icon' => '🍅',
                'badge' => 'Intensive Protected',
                'optimal_temp' => '18°C - 30°C',
                'pests' => ['Leaf Curl Virus', 'Fruit Borer', 'Early Blight'],
                'mandi_price' => '₹1,650 / Qtl',
                'mandi_trend' => '+5.2%',
                'mandi_yard' => 'APMC Surat',
                'description' => 'High-frequency vegetable market crop supporting daily farm cash flow near urban consumption hubs.',
            ],
            [
                'slug' => 'cauliflower',
                'en_name' => 'Cauliflower',
                'hi_name' => 'फूलगोभी',
                'gu_name' => 'ફૂલકોબી',
                'botanical_name' => 'Brassica oleracea var. botrytis',
                'category' => 'Cruciferous Veg',
                'season' => 'Winter / Rabi',
                'icon' => '🥦',
                'badge' => 'Cool Season',
                'optimal_temp' => '15°C - 22°C',
                'pests' => ['Diamondback Moth', 'Damping Off', 'Downy Mildew'],
                'mandi_price' => '₹1,980 / Qtl',
                'mandi_trend' => '-1.5%',
                'mandi_yard' => 'APMC Rajkot',
                'description' => 'Winter cash crop requiring delicate pest threshold management and quick transit to mandi.',
            ],
            [
                'slug' => 'chilli',
                'en_name' => 'Chilli',
                'hi_name' => 'मिर्च',
                'gu_name' => 'મરચા',
                'botanical_name' => 'Capsicum annuum (Resham Patti)',
                'category' => 'Spices & Veg',
                'season' => 'Kharif & Summer',
                'icon' => '🌶️',
                'badge' => 'High Value Spice',
                'optimal_temp' => '20°C - 35°C',
                'pests' => ['Thrips & Mites', 'Anthracnose Dieback', 'Leaf Curl'],
                'mandi_price' => '₹14,200 / Qtl',
                'mandi_trend' => '+4.0%',
                'mandi_yard' => 'APMC Gondal',
                'description' => 'Gondal APMC is Asia’s leading market yard for dry red chillies with world-renowned pungent varieties.',
            ],
        ];

        foreach ($cropsData as $crop) {
            Crop::updateOrCreate(['slug' => $crop['slug']], $crop);
        }

        // 3. Seed Gujarat APMC Mandi Prices
        $mandiPrices = [
            [
                'crop_slug' => 'cotton',
                'commodity_name' => 'Cotton (Shankar-6 Medium Staple)',
                'variety' => 'Shankar-6',
                'yard_name' => 'APMC Rajkot',
                'district' => 'Rajkot',
                'state' => 'Gujarat',
                'arrival_quantity' => '1,450 Quintals',
                'min_price' => 6900.00,
                'modal_price' => 7150.00,
                'max_price' => 7420.00,
                'price_unit' => '₹ / Quintal',
                'price_date' => now()->toDateString(),
                'trend_percentage' => '+2.4%',
                'is_sample' => true,
            ],
            [
                'crop_slug' => 'grain',
                'commodity_name' => 'Wheat (Tukdi / Sharbati)',
                'variety' => 'Tukdi Special',
                'yard_name' => 'APMC Gondal',
                'district' => 'Rajkot',
                'state' => 'Gujarat',
                'arrival_quantity' => '850 Quintals',
                'min_price' => 2750.00,
                'modal_price' => 2820.00,
                'max_price' => 2940.00,
                'price_unit' => '₹ / Quintal',
                'price_date' => now()->toDateString(),
                'trend_percentage' => '+0.7%',
                'is_sample' => true,
            ],
            [
                'crop_slug' => 'chilli',
                'commodity_name' => 'Dry Red Chilli (Resham Patti)',
                'variety' => 'Resham Patti Premium',
                'yard_name' => 'APMC Gondal',
                'district' => 'Rajkot',
                'state' => 'Gujarat',
                'arrival_quantity' => '420 Quintals',
                'min_price' => 13400.00,
                'modal_price' => 14200.00,
                'max_price' => 15100.00,
                'price_unit' => '₹ / Quintal',
                'price_date' => now()->toDateString(),
                'trend_percentage' => '+4.0%',
                'is_sample' => true,
            ],
            [
                'crop_slug' => 'potato',
                'commodity_name' => 'Potato (Jyoti Fresh Table)',
                'variety' => 'Kufri Jyoti',
                'yard_name' => 'APMC Deesa',
                'district' => 'Banaskantha',
                'state' => 'Gujarat',
                'arrival_quantity' => '3,100 Quintals',
                'min_price' => 1350.00,
                'modal_price' => 1420.00,
                'max_price' => 1560.00,
                'price_unit' => '₹ / Quintal',
                'price_date' => now()->toDateString(),
                'trend_percentage' => '-0.8%',
                'is_sample' => true,
            ],
            [
                'crop_slug' => 'rice',
                'commodity_name' => 'Paddy / Rice (Gurjari Fragrant)',
                'variety' => 'Gurjari Fine',
                'yard_name' => 'APMC Anand',
                'district' => 'Anand',
                'state' => 'Gujarat',
                'arrival_quantity' => '960 Quintals',
                'min_price' => 2200.00,
                'modal_price' => 2340.00,
                'max_price' => 2480.00,
                'price_unit' => '₹ / Quintal',
                'price_date' => now()->toDateString(),
                'trend_percentage' => '+3.1%',
                'is_sample' => true,
            ],
            [
                'crop_slug' => 'tomato',
                'commodity_name' => 'Tomato (Hybrid-1 Red Ripe)',
                'variety' => 'Abhinav Hybrid',
                'yard_name' => 'APMC Surat',
                'district' => 'Surat',
                'state' => 'Gujarat',
                'arrival_quantity' => '1,800 Crates',
                'min_price' => 1520.00,
                'modal_price' => 1650.00,
                'max_price' => 1780.00,
                'price_unit' => '₹ / Quintal',
                'price_date' => now()->toDateString(),
                'trend_percentage' => '+5.2%',
                'is_sample' => true,
            ],
        ];

        foreach ($mandiPrices as $mp) {
            MandiPrice::updateOrCreate(
                ['crop_slug' => $mp['crop_slug'], 'yard_name' => $mp['yard_name']],
                $mp
            );
        }

        // 4. Seed Weather Snapshot
        WeatherSnapshot::updateOrCreate(
            ['district' => 'Rajkot'],
            [
                'zone_name' => 'Saurashtra Central Agro-Climatic Zone',
                'temperature' => 28.5,
                'condition' => 'Scattered Clouds • Clear Horizons',
                'humidity' => 58,
                'wind_speed' => 11,
                'wind_direction' => 'NW',
                'rainfall_probability' => 12,
                'spray_window' => 'Optimal (Next 36 Hours)',
                'soil_moisture' => 'Adequate (64% VWC)',
                'heat_index' => '30°C',
                'is_sample' => true,
                'forecast_json' => [
                    ['day' => 'Today', 'temp' => '31° / 21°', 'icon' => '⛅', 'spray' => 'Good'],
                    ['day' => 'Tomorrow', 'temp' => '32° / 20°', 'icon' => '☀️', 'spray' => 'Optimal'],
                    ['day' => 'Saturday', 'temp' => '33° / 22°', 'icon' => '🌤️', 'spray' => 'Optimal'],
                    ['day' => 'Sunday', 'temp' => '30° / 21°', 'icon' => '🌦️', 'spray' => 'Moderate'],
                ],
            ]
        );

        // 5. Seed Demonstration Advisories
        Advisory::updateOrCreate(
            ['ticket_number' => 'ADV-GJ-2026-001'],
            [
                'crop_slug' => 'cotton',
                'farmer_name' => 'Suresh Patel',
                'farmer_phone' => '9876543210',
                'farmer_district' => 'Rajkot',
                'issue_description' => 'Green bolls shedding prematurely and small circular exit holes observed on lower fruiting branches.',
                'symptom_key' => 'bollworm',
                'ai_diagnosis_title' => 'Pink Bollworm (Pectinophora gossypiella)',
                'ai_diagnosis_description' => 'Early larval penetration detected in squares and green bolls with characteristic rosette flowers.',
                'ai_recommended_solution' => 'Install delta pheromone traps @ 8 traps/acre; apply Azadirachtin 10,000 ppm (1.5 ml/L).',
                'ai_confidence' => '95% Confidence',
                'status' => 'resolved',
                'assigned_agronomist' => 'Dr. Alok Verma',
                'agronomist_notes' => 'Confirmed via simulated visual review. Farmer notified to maintain pheromone septa replacement schedule.',
                'is_demonstration' => true,
            ]
        );

        Advisory::updateOrCreate(
            ['ticket_number' => 'ADV-GJ-2026-002'],
            [
                'crop_slug' => 'tomato',
                'farmer_name' => 'Bhavik Prajapati',
                'farmer_phone' => '9876543211',
                'farmer_district' => 'Anand',
                'issue_description' => 'Severe upward curling and crinkling of leaves, pale chlorotic borders.',
                'symptom_key' => 'curl',
                'ai_diagnosis_title' => 'Tomato Leaf Curl Virus (ToLCV)',
                'ai_diagnosis_description' => 'Stunted internodes, reduced leaflet size, pale marginal chlorosis, vector-borne.',
                'ai_recommended_solution' => 'Use silver reflective mulch; apply bio-insecticide to suppress whitefly vector population.',
                'ai_confidence' => '89% Confidence',
                'status' => 'escalated',
                'assigned_agronomist' => 'Senior Agronomist (Anand Zone)',
                'agronomist_notes' => 'Escalated for immediate field verification of whitefly population pressure.',
                'is_demonstration' => true,
            ]
        );

        // 6. Seed Manufacturers, Categories, Products & Distributors
        $this->call(ProductsAndDistributorsSeeder::class);

        // 7. Seed Produce Lots, Quality Assessments & Buyer Demo Inventory
        $this->call(ProduceTradingSeeder::class);
    }
}
