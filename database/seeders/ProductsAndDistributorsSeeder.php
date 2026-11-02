<?php

namespace Database\Seeders;

use App\Models\Distributor;
use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductsAndDistributorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Categories
        $categories = [
            ['name' => 'High-Yield Seeds', 'slug' => 'seeds', 'icon' => '🌱', 'description' => 'Certified hybrids and university bred indigenous seeds for Gujarat Kharif and Rabi cycles.'],
            ['name' => 'Organic & Bio-Fertilizers', 'slug' => 'fertilizers', 'icon' => '🌿', 'description' => 'Soil conditioners, NPK consortia, urea, and compost tailored for regional soil nutrition.'],
            ['name' => 'Crop Protection', 'slug' => 'crop-protection', 'icon' => '🛡️', 'description' => 'Bio-pesticides, pheromone lures, trichoderma, and botanical pest management solutions.'],
            ['name' => 'Micronutrients', 'slug' => 'micronutrients', 'icon' => '🧪', 'description' => 'Chelated foliar Zinc, Boron, Ferrous, and specialized multivitamin crop sprays.'],
            ['name' => 'Irrigation & Tools', 'slug' => 'irrigation-tools', 'icon' => '💧', 'description' => 'Pressure-compensating drip lateral lines, knapsack sprayers, and solar insect traps.'],
        ];

        foreach ($categories as $cat) {
            ProductCategory::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 2. Seed Manufacturers
        $manufacturersData = [
            [
                'name' => 'Gujarat State Fertilizers & Chemicals (GSFC)',
                'slug' => 'gsfc',
                'category' => 'fertilizer',
                'hq_location' => 'Fertilizernagar, Vadodara, Gujarat',
                'phone' => '+91 265 2242451',
                'email' => 'sales@gsfcltd.com',
                'description' => 'Pioneering state-supported manufacturer of Sardar Urea, DAP, Ammonium Sulphate, and Liquid Bio-Fertilizers across Gujarat.',
                'authorized_dealers_count' => 1420,
            ],
            [
                'name' => 'Gujarat Narmada Valley Fertilizers & Chemicals (GNFC)',
                'slug' => 'gnfc',
                'category' => 'fertilizer',
                'hq_location' => 'Narmadanagar, Bharuch, Gujarat',
                'phone' => '+91 2642 247001',
                'email' => 'marketing@gnfc.in',
                'description' => 'World-scale producer of Neem-Coated Granular Urea, ammonium nitrophosphate, and bio-consortia in Southern Gujarat.',
                'authorized_dealers_count' => 980,
            ],
            [
                'name' => 'Gujarat State Seed Corporation (GSSC - Gurjari)',
                'slug' => 'gssc',
                'category' => 'seeds',
                'hq_location' => 'Sector 10-A, Gandhinagar, Gujarat',
                'phone' => '+91 79 23223071',
                'email' => 'seeds@gurjari.org',
                'description' => 'Official state agency breeding, certifying, and distributing high-germination Shankar-6 Cotton, Bhalia Wheat, and Paddy varieties.',
                'authorized_dealers_count' => 2100,
            ],
            [
                'name' => 'Charotar Agro Bio-Tech Laboratories',
                'slug' => 'charotar-bio',
                'category' => 'seeds',
                'hq_location' => 'Anand Agricultural Complex, Anand, Gujarat',
                'phone' => '+91 2692 261310',
                'email' => 'info@charotarbio.com',
                'description' => 'Pioneering tissue-culture nursery producing virus-indexed Grand Naine banana plantlets and solanaceous seed stocks.',
                'authorized_dealers_count' => 340,
            ],
            [
                'name' => 'Saurashtra Bio-Formulations & Pests Ltd',
                'slug' => 'saurashtra-bio',
                'category' => 'crop-protection',
                'hq_location' => 'Aji Industrial Area, Rajkot, Gujarat',
                'phone' => '+91 281 2387799',
                'email' => 'contact@saurashtrabio.farm',
                'description' => 'Certified organic bio-control laboratory specializing in NeemAzal extracts, Beauveria bassiana, and delta pheromone lures.',
                'authorized_dealers_count' => 620,
            ],
            [
                'name' => 'Deesa Agro Coldchain & Protection Corp',
                'slug' => 'deesa-agro',
                'category' => 'crop-protection',
                'hq_location' => 'Highway Agro Hub, Deesa, Banaskantha, Gujarat',
                'phone' => '+91 2744 220199',
                'email' => 'protection@deesaagro.co.in',
                'description' => 'Specialized cold-belt fungicide and tuber protection formulator serving North Gujarat potato and cruciferous cultivators.',
                'authorized_dealers_count' => 450,
            ],
        ];

        $mfgMap = [];
        foreach ($manufacturersData as $m) {
            $record = Manufacturer::firstOrCreate(['slug' => $m['slug']], $m);
            $mfgMap[$m['slug']] = $record->id;
        }

        // 3. Seed Products
        $productsData = [
            // Seeds
            [
                'manufacturer_id' => $mfgMap['gssc'],
                'category_slug' => 'seeds',
                'name' => 'Gurjari Certified Shankar-6 Bt-Cotton Seeds',
                'slug' => 'gurjari-shankar-6-cotton-seeds',
                'active_ingredient' => 'Cry1Ac + Cry2Ab Bt Protein Hybrids',
                'package_size' => '450g Pouch + 120g Non-Bt Refuge',
                'price_guide' => 'MRP ₹864 / 450g (Govt Price Cap)',
                'description' => 'High-yielding medium-staple Shankar-6 hybrid seeds certified by GSSC Gandhinagar with high ginning outturn (35%).',
                'usage_instructions' => 'Sow in Kharif season (June-July) with spacing 120cm x 45cm under drip irrigation. Maintain refuge border.',
                'safety_guidelines' => 'Treated with Imidacloprid 70% WS. Keep away from domestic animals and food supplies.',
                'target_crops' => ['cotton'],
                'is_featured' => true,
            ],
            [
                'manufacturer_id' => $mfgMap['gssc'],
                'category_slug' => 'seeds',
                'name' => 'Bhalia Wheat GW-496 Rainfed Certified Seed',
                'slug' => 'bhalia-wheat-gw-496-seed',
                'active_ingredient' => 'High-Gluten Triticum Durum Specie',
                'package_size' => '40 kg Sealed Bag',
                'price_guide' => 'MRP ₹1,750 / 40kg Bag',
                'description' => 'Geographically indicated Bhal region wheat grain seed suited for conserved moisture cultivation with high lustrous amber grain.',
                'usage_instructions' => 'Sow at 100-120 kg/ha in late October to November after residual monsoon moisture evaluation.',
                'safety_guidelines' => 'Thiram fungicide pre-treated. Wash hands after handling.',
                'target_crops' => ['grain'],
                'is_featured' => true,
            ],
            [
                'manufacturer_id' => $mfgMap['charotar-bio'],
                'category_slug' => 'seeds',
                'name' => 'Grand Naine Tissue Culture Banana Plantlets',
                'slug' => 'grand-naine-tissue-culture-banana',
                'active_ingredient' => 'Virus-Indexed Musa Acuminata (AAA)',
                'package_size' => 'Tray of 50 Hardened Seedlings',
                'price_guide' => 'MRP ₹18.50 / Plantlet',
                'description' => 'Hardened secondary-stage tissue-culture banana plants free from bunchy top and banana streak virus with uniform bunch maturity.',
                'usage_instructions' => 'Plant in pit of 45x45x45 cm with FYM. Space 1.8m x 1.8m in Anand and Bharuch agro zones.',
                'safety_guidelines' => 'Harden in shade for 48 hours prior to open field transplantation.',
                'target_crops' => ['banana'],
                'is_featured' => true,
            ],
            [
                'manufacturer_id' => $mfgMap['gssc'],
                'category_slug' => 'seeds',
                'name' => 'Gurjari Fragrant Paddy Seeds (GR-11)',
                'slug' => 'gurjari-paddy-gr-11-seeds',
                'active_ingredient' => 'Oryza Sativa Semi-Dwarf Cultivar',
                'package_size' => '25 kg Bag',
                'price_guide' => 'MRP ₹1,200 / 25kg Bag',
                'description' => 'Semi-dwarf aromatic rice variety adapted to South and Central Gujarat canal command regions with blast tolerance.',
                'usage_instructions' => 'Seed rate 25-30 kg/ha for nursery. Transplant 21-day seedlings with 2-3 per hill.',
                'safety_guidelines' => 'Seed soak with Carbendazim 2g/kg recommended before nursery germination.',
                'target_crops' => ['rice'],
                'is_featured' => false,
            ],

            // Fertilizers
            [
                'manufacturer_id' => $mfgMap['gsfc'],
                'category_slug' => 'fertilizers',
                'name' => 'Sardar Urea 46% Prilled Nitrogen Fertilizer',
                'slug' => 'sardar-urea-46-n',
                'active_ingredient' => '46% Total Nitrogen (Ammoniacal Form)',
                'package_size' => '45 kg Bag',
                'price_guide' => 'MRP ₹266.50 / 45kg (Subsidized MRP)',
                'description' => 'Essential primary macronutrient fertilizer promoting rapid vegetative vigor and leaf chlorophyll synthesis across Gujarat.',
                'usage_instructions' => 'Split application: 25% basal, 50% active tillering/square formation, 25% pre-flowering.',
                'safety_guidelines' => 'Store on wooden pallets in moisture-proof godowns. Avoid open contact with eyes.',
                'target_crops' => ['cotton', 'rice', 'grain', 'potato', 'banana'],
                'is_featured' => true,
            ],
            [
                'manufacturer_id' => $mfgMap['gsfc'],
                'category_slug' => 'fertilizers',
                'name' => 'Sardar DAP (Di-Ammonium Phosphate 18:46:0)',
                'slug' => 'sardar-dap-18-46-0',
                'active_ingredient' => '18% Nitrogen + 46% P2O5 Phosphate',
                'package_size' => '50 kg Bag',
                'price_guide' => 'MRP ₹1,350 / 50kg Bag',
                'description' => 'High-phosphorus basal fertilizer vital for root proliferation, nodulation, and early seedling establishment in black cotton soils.',
                'usage_instructions' => 'Apply 100% as basal placement 5cm below and beside seed line during sowing.',
                'safety_guidelines' => 'Do not mix with calcium ammonium nitrate in single hopper.',
                'target_crops' => ['cotton', 'potato', 'grain'],
                'is_featured' => true,
            ],
            [
                'manufacturer_id' => $mfgMap['gnfc'],
                'category_slug' => 'fertilizers',
                'name' => 'Narmada Neem-Coated Granular Urea',
                'slug' => 'narmada-neem-coated-urea',
                'active_ingredient' => '46% N coated with 0.05% Pure Neem Oil',
                'package_size' => '45 kg Bag',
                'price_guide' => 'MRP ₹266.50 / 45kg Bag',
                'description' => 'Slow-release nitrification-inhibited nitrogen that reduces leaching losses in flood-irrigated Charotar basin soils.',
                'usage_instructions' => 'Apply during morning hours before irrigation. Highly efficient in heavy soils.',
                'safety_guidelines' => 'Eco-safe neem coating; store away from direct rain.',
                'target_crops' => ['banana', 'rice', 'cotton'],
                'is_featured' => false,
            ],
            [
                'manufacturer_id' => $mfgMap['gsfc'],
                'category_slug' => 'fertilizers',
                'name' => 'GSFC Bio-NPK Consortia Liquid Formulation',
                'slug' => 'gsfc-bio-npk-consortia',
                'active_ingredient' => 'Azotobacter + PSB + Frateuria aurantia',
                'package_size' => '1 Liter Bottle',
                'price_guide' => 'MRP ₹340 / Liter',
                'description' => 'Synergistic microbial liquid consortium fixing atmospheric nitrogen, solubilizing fixed soil phosphorus, and mobilizing potash.',
                'usage_instructions' => 'Drip application: 1 Liter/acre mixed in 200L water. Apply during active root development.',
                'safety_guidelines' => 'Biological live culture. Do not mix with chemical bactericides or copper compounds.',
                'target_crops' => ['potato', 'banana', 'tomato', 'chilli'],
                'is_featured' => false,
            ],

            // Crop Protection
            [
                'manufacturer_id' => $mfgMap['saurashtra-bio'],
                'category_slug' => 'crop-protection',
                'name' => 'NeemAzal 10,000 ppm Pure Bio-Pesticide',
                'slug' => 'neemazal-10000-ppm-bio-pesticide',
                'active_ingredient' => 'Azadirachtin 1.0% EC (10,000 ppm)',
                'package_size' => '500 ml Bottle',
                'price_guide' => 'MRP ₹480 / 500ml',
                'description' => 'Botanical antifeedant and insect growth regulator suppressing bollworms, thrips, and aphids without harming honeybees.',
                'usage_instructions' => 'Foliar spray: 1.5 - 2.0 ml per liter of water. Spray during late afternoon.',
                'safety_guidelines' => 'Standard personal protective gear recommended. Non-toxic to non-target pollinators.',
                'target_crops' => ['cotton', 'chilli', 'tomato', 'cauliflower'],
                'is_featured' => true,
            ],
            [
                'manufacturer_id' => $mfgMap['saurashtra-bio'],
                'category_slug' => 'crop-protection',
                'name' => 'Trichoderma Viride 1.5% WP Bio-Fungicide',
                'slug' => 'trichoderma-viride-1-5-wp',
                'active_ingredient' => 'Trichoderma viride 2x10^8 CFU/g',
                'package_size' => '1 kg Pouch',
                'price_guide' => 'MRP ₹220 / 1kg',
                'description' => 'Antagonistic fungal biocontrol against seed rot, damping-off, fusarium wilt, and collar rot in groundnut and cotton.',
                'usage_instructions' => 'Seed treatment: 10g/kg seed. Soil application: 2.5 kg mixed with 100kg compost per acre.',
                'safety_guidelines' => 'Maintain shade during application; keep away from chemical systemic fungicides.',
                'target_crops' => ['cotton', 'potato', 'banana', 'chilli'],
                'is_featured' => false,
            ],
            [
                'manufacturer_id' => $mfgMap['deesa-agro'],
                'category_slug' => 'crop-protection',
                'name' => 'Mancozeb 75% WP Prophylactic Fungicide',
                'slug' => 'mancozeb-75-wp-fungicide',
                'active_ingredient' => 'Mancozeb 75% Wettable Powder',
                'package_size' => '1 kg Pack',
                'price_guide' => 'MRP ₹490 / 1kg',
                'description' => 'Broad-spectrum protective contact fungicide preventing late blight in Deesa potato fields and fruit rot in chilli.',
                'usage_instructions' => 'Mix 2.5g per liter water. Apply as preventative spray before forecast rain or night dew.',
                'safety_guidelines' => 'Wear mask and gloves during preparation. Observe 7-day pre-harvest interval.',
                'target_crops' => ['potato', 'tomato', 'cauliflower', 'chilli'],
                'is_featured' => true,
            ],
            [
                'manufacturer_id' => $mfgMap['saurashtra-bio'],
                'category_slug' => 'crop-protection',
                'name' => 'Delta Pheromone Traps & Gossyplure Kit',
                'slug' => 'delta-pheromone-traps-kit',
                'active_ingredient' => 'Synthetic (Z,Z) & (Z,E)-Gossyplure Lure',
                'package_size' => 'Kit of 5 Traps + 10 Septa Lures',
                'price_guide' => 'MRP ₹350 / Kit',
                'description' => 'Targeted early-warning and mating disruption tool for monitoring pink bollworm male moths in Bt-cotton fields.',
                'usage_instructions' => 'Install @ 8-10 traps/acre at canopy height. Replace lure septa every 21 days.',
                'safety_guidelines' => 'Store lures in cold refrigerator (not deep freezer) prior to field hanging.',
                'target_crops' => ['cotton'],
                'is_featured' => false,
            ],

            // Micronutrients
            [
                'manufacturer_id' => $mfgMap['gsfc'],
                'category_slug' => 'micronutrients',
                'name' => 'Sardar Chelated Zinc 12% EDTA Foliar Grade',
                'slug' => 'sardar-chelated-zinc-12-edta',
                'active_ingredient' => 'Zinc 12% fully chelated with EDTA',
                'package_size' => '500g Container',
                'price_guide' => 'MRP ₹380 / 500g',
                'description' => 'Rapidly absorbed foliar formulation treating Khaira disease in paddy and interveinal chlorosis in cotton and maize.',
                'usage_instructions' => 'Dissolve 1.0g per liter water. Apply 2-3 sprays during active tillering/branching.',
                'safety_guidelines' => 'Compatible with most non-alkaline agrochemicals.',
                'target_crops' => ['rice', 'cotton', 'grain', 'tomato'],
                'is_featured' => true,
            ],
            [
                'manufacturer_id' => $mfgMap['charotar-bio'],
                'category_slug' => 'micronutrients',
                'name' => 'Soluble Boron 20% Disodium Octaborate',
                'slug' => 'soluble-boron-20-spray-grade',
                'active_ingredient' => 'Boron 20% Soluble Micro-Granules',
                'package_size' => '250g Pack',
                'price_guide' => 'MRP ₹210 / 250g',
                'description' => 'Essential micronutrient preventing brown heart in cauliflower, hollow stem, and fruit cracking in Gujarat tomato farms.',
                'usage_instructions' => 'Dissolve 1.0 - 1.5g per liter. Spray during pre-flowering and curd initiation stages.',
                'safety_guidelines' => 'Do not exceed prescribed dosage to prevent boron phytotoxicity.',
                'target_crops' => ['cauliflower', 'tomato', 'chilli'],
                'is_featured' => false,
            ],

            // Irrigation Tools
            [
                'manufacturer_id' => $mfgMap['saurashtra-bio'],
                'category_slug' => 'irrigation-tools',
                'name' => 'Inline PC Drip Lateral Line 16mm (40cm Spacing)',
                'slug' => 'inline-pc-drip-lateral-16mm',
                'active_ingredient' => 'Virgin HDPE with UV Stabilizer Matrix',
                'package_size' => '400 Meter Coil (2.0 LPH Drippers)',
                'price_guide' => 'MRP ₹3,600 / 400m Coil',
                'description' => 'Pressure-compensating clog-resistant dripline for Saurashtra undulating terrain, conserving 45% irrigation water.',
                'usage_instructions' => 'Operate between 1.0 to 3.0 bar pressure. Flush sub-mains fortnightly.',
                'safety_guidelines' => 'Avoid sharp bending; maintain proper sand filtration.',
                'target_crops' => ['cotton', 'banana', 'chilli', 'tomato'],
                'is_featured' => true,
            ],
            [
                'manufacturer_id' => $mfgMap['saurashtra-bio'],
                'category_slug' => 'irrigation-tools',
                'name' => 'Solar-Powered Automatic Agro Insect Trap',
                'slug' => 'solar-powered-automatic-insect-trap',
                'active_ingredient' => 'UV-LED 365nm + 12V 7Ah LiFePO4 Battery',
                'package_size' => 'Complete Solar Unit + Telescopic Stand',
                'price_guide' => 'MRP ₹2,850 / Unit',
                'description' => 'Eco-friendly automatic nocturnal insect trap attracting night-flying moths, whiteflies, and beetles across 2 acres.',
                'usage_instructions' => 'Install 1 unit per 2 acres in clear sunlight. Trap turns on automatically at dusk.',
                'safety_guidelines' => 'Clean collection funnel weekly; ensure solar panel faces true south.',
                'target_crops' => ['cotton', 'tomato', 'cauliflower', 'chilli'],
                'is_featured' => false,
            ],
        ];

        $productRecords = [];
        foreach ($productsData as $p) {
            $record = Product::firstOrCreate(['slug' => $p['slug']], $p);
            $productRecords[] = $record;
        }

        // 4. Seed Distributors (Across key Gujarat districts with 6-digit pincodes)
        $distributorsData = [
            [
                'name' => 'Kisan Seva Kendra Rajkot',
                'business_name' => 'Kisan Seva Kendra & Agro Input Hub',
                'license_number' => 'GJ-RJK-AGR-2024-0012',
                'district' => 'Rajkot',
                'taluka' => 'Rajkot City',
                'address' => 'Near Marketing Yard, Bedi Road, Rajkot',
                'pincode' => '360001',
                'phone' => '+91 9876543220',
                'whatsapp' => '+91 9876543220',
                'authorized_categories' => ['seeds', 'fertilizers', 'crop-protection', 'micronutrients', 'irrigation-tools'],
                'featured_brands' => ['GSFC', 'GSSC Gurjari', 'Saurashtra Bio'],
            ],
            [
                'name' => 'Saurashtra Agri Traders Gondal',
                'business_name' => 'Saurashtra Agro Inputs & Seeds Depot',
                'license_number' => 'GJ-RJK-AGR-2023-0841',
                'district' => 'Rajkot',
                'taluka' => 'Gondal',
                'address' => 'Station Road, Opp APMC Market Yard, Gondal',
                'pincode' => '360311',
                'phone' => '+91 9876543221',
                'whatsapp' => '+91 9876543221',
                'authorized_categories' => ['seeds', 'crop-protection', 'micronutrients'],
                'featured_brands' => ['GSSC Gurjari', 'Saurashtra Bio', 'GSFC'],
            ],
            [
                'name' => 'Charotar Kisan Input Depot Petlad',
                'business_name' => 'Charotar Agro Chemical & Tissue Depot',
                'license_number' => 'GJ-AND-AGR-2024-0315',
                'district' => 'Anand',
                'taluka' => 'Petlad',
                'address' => 'College Road, Near Railway Crossing, Petlad',
                'pincode' => '388001',
                'phone' => '+91 9876543222',
                'whatsapp' => '+91 9876543222',
                'authorized_categories' => ['seeds', 'fertilizers', 'micronutrients'],
                'featured_brands' => ['Charotar Bio', 'GNFC', 'GSFC'],
            ],
            [
                'name' => 'Deesa Agro Centre & Cold Chain Stockist',
                'business_name' => 'Deesa Tuber Inputs & Seed Agency',
                'license_number' => 'GJ-BK-AGR-2022-0499',
                'district' => 'Banaskantha',
                'taluka' => 'Deesa',
                'address' => 'Patan Road, Near Sardar Patel Market, Deesa',
                'pincode' => '385535',
                'phone' => '+91 9876543223',
                'whatsapp' => '+91 9876543223',
                'authorized_categories' => ['crop-protection', 'fertilizers', 'micronutrients'],
                'featured_brands' => ['Deesa Agro', 'GSFC', 'Saurashtra Bio'],
            ],
            [
                'name' => 'Unjha Seed & Spice Input Yard',
                'business_name' => 'Shree Ram Krishi Kendra Unjha',
                'license_number' => 'GJ-MSH-AGR-2023-0118',
                'district' => 'Mehsana',
                'taluka' => 'Unjha',
                'address' => 'APMC Ganj Bazar Gate 2, Unjha',
                'pincode' => '384170',
                'phone' => '+91 9876543224',
                'whatsapp' => '+91 9876543224',
                'authorized_categories' => ['seeds', 'crop-protection', 'micronutrients'],
                'featured_brands' => ['GSSC Gurjari', 'Saurashtra Bio'],
            ],
            [
                'name' => 'Surat Kisan Bio & Sugar Input Store',
                'business_name' => 'Surat District Agro Supply Co-op',
                'license_number' => 'GJ-SRT-AGR-2024-0722',
                'district' => 'Surat',
                'taluka' => 'Bardoli',
                'address' => 'Sardar Chowk, Sugar Factory Road, Bardoli',
                'pincode' => '395003',
                'phone' => '+91 9876543225',
                'whatsapp' => '+91 9876543225',
                'authorized_categories' => ['fertilizers', 'crop-protection', 'irrigation-tools'],
                'featured_brands' => ['GNFC', 'GSFC', 'Charotar Bio'],
            ],
            [
                'name' => 'Junagadh Krishak Sahakari Mandali',
                'business_name' => 'Junagadh Agro Inputs Federation',
                'license_number' => 'GJ-JND-AGR-2023-0610',
                'district' => 'Junagadh',
                'taluka' => 'Junagadh',
                'address' => 'Moti Baug Road, Opp JAU Campus, Junagadh',
                'pincode' => '362001',
                'phone' => '+91 9876543226',
                'whatsapp' => '+91 9876543226',
                'authorized_categories' => ['seeds', 'fertilizers', 'crop-protection'],
                'featured_brands' => ['GSSC Gurjari', 'GSFC', 'Saurashtra Bio'],
            ],
            [
                'name' => 'Bhal Rainfed Grain & Agro Inputs',
                'business_name' => 'Bhal Agro Federation Dhandhuka',
                'license_number' => 'GJ-AHM-AGR-2023-0551',
                'district' => 'Ahmedabad',
                'taluka' => 'Dhandhuka',
                'address' => 'Bhal Highway Crossroad, Dhandhuka',
                'pincode' => '382460',
                'phone' => '+91 9876543227',
                'whatsapp' => '+91 9876543227',
                'authorized_categories' => ['seeds', 'crop-protection'],
                'featured_brands' => ['GSSC Gurjari', 'GSFC'],
            ],
        ];

        $distributorRecords = [];
        foreach ($distributorsData as $d) {
            $record = Distributor::firstOrCreate(['license_number' => $d['license_number']], $d);
            $distributorRecords[] = $record;
        }

        // 5. Link Products to Distributors (In-Stock Associations)
        foreach ($productRecords as $product) {
            // Attach 2 to 4 distributors that service this product's category
            $matchingDistributors = array_filter($distributorRecords, function ($dist) use ($product) {
                return in_array($product->category_slug, $dist->authorized_categories ?? []);
            });

            if (empty($matchingDistributors)) {
                $matchingDistributors = array_slice($distributorRecords, 0, 3);
            }

            foreach ($matchingDistributors as $distributor) {
                $product->distributors()->syncWithoutDetaching([
                    $distributor->id => ['in_stock' => true],
                ]);
            }
        }
    }
}
