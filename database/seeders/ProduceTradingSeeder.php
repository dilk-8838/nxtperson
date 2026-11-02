<?php

namespace Database\Seeders;

use App\Models\ProduceGradingAssessment;
use App\Models\ProduceLot;
use App\Models\ProduceLotImage;
use App\Models\ProduceReservation;
use App\Models\User;
use App\Notifications\ProduceAvailableNotification;
use Illuminate\Database\Seeder;

class ProduceTradingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $farmer = User::where('role', 'farmer')->first();
        $buyer = User::where('role', 'buyer')->first();
        $admin = User::where('role', 'admin')->first();

        if (! $farmer) {
            return;
        }

        // 1. Available Lot: Cotton (Rajkot Shankar-6)
        $cottonLot = ProduceLot::updateOrCreate(
            ['lot_number' => 'LOT-COT-202610-1001'],
            [
                'farmer_id' => $farmer->id,
                'crop_slug' => 'cotton',
                'variety' => 'Shankar-6 (Bt Premium)',
                'quantity' => 45.0,
                'unit' => 'quintal',
                'quantity_in_kg' => 4500.0,
                'district' => 'Rajkot',
                'taluka' => 'Gondal',
                'pincode' => '360311',
                'harvest_date' => now()->subDays(3)->toDateString(),
                'expected_price_per_kg' => 74.00,
                'description' => 'First pick Shankar-6 long staple cotton. Hand-picked with zero rain contact. Moisture tested at 8.2%. Minimal leaf trash.',
                'farming_method' => 'conventional',
                'status' => ProduceLot::STATUS_INVENTORY_AVAILABLE,
                'approved_grade' => 'Good',
                'approved_procurement_price_per_kg' => 72.50,
                'buyer_sale_price_per_kg' => 78.50,
                'confirmed_quantity_kg' => 4500.0,
                'available_quantity_kg' => 4500.0,
                'reserved_quantity_kg' => 0.0,
                'sold_quantity_kg' => 0.0,
                'published_at' => now()->subHours(2),
            ]
        );

        ProduceLotImage::firstOrCreate(
            ['produce_lot_id' => $cottonLot->id, 'image_path' => 'images/crops/cotton.jpg'],
            [
                'original_name' => 'cotton_shankar6_harvest.jpg',
                'is_primary' => true,
                'is_approved_for_buyers' => true,
            ]
        );

        ProduceGradingAssessment::updateOrCreate(
            ['produce_lot_id' => $cottonLot->id],
            [
                'grade' => 'Good',
                'observed_defects' => ['Minimal leaf trash (<1.8%)', 'Bright white luster', 'No bollworm staining detected'],
                'assessment_explanation' => 'Premium Grade A: High staple length (>29mm), superior micronaire, excellent ginning suitability. Assessed across 2 lot sample photograph(s).',
                'confidence_score' => '94% (Demonstration Heuristic)',
                'manual_verification_required' => false,
                'grading_method' => 'Senior Agronomist Physical Audit Verification',
                'engine_version' => 'v1.0.0-sample',
                'assessed_by' => $admin ? $admin->id : null,
                'is_admin_override' => false,
                'is_demonstration' => true,
            ]
        );

        // 2. Available Lot: Banana (Anand Grand Naine)
        $bananaLot = ProduceLot::updateOrCreate(
            ['lot_number' => 'LOT-BAN-202610-1002'],
            [
                'farmer_id' => $farmer->id,
                'crop_slug' => 'banana',
                'variety' => 'Grand Naine (Tissue Culture)',
                'quantity' => 3.2,
                'unit' => 'tonne',
                'quantity_in_kg' => 3200.0,
                'district' => 'Anand',
                'taluka' => 'Petlad',
                'pincode' => '388450',
                'harvest_date' => now()->subDays(1)->toDateString(),
                'expected_price_per_kg' => 19.50,
                'description' => 'Charotar canal irrigated Grand Naine bunches. Caliper grade 39-42, finger length 8 inches. Packed in padded crates.',
                'farming_method' => 'ipm',
                'status' => ProduceLot::STATUS_INVENTORY_AVAILABLE,
                'approved_grade' => 'Good',
                'approved_procurement_price_per_kg' => 19.00,
                'buyer_sale_price_per_kg' => 21.50,
                'confirmed_quantity_kg' => 3200.0,
                'available_quantity_kg' => 3200.0,
                'reserved_quantity_kg' => 0.0,
                'sold_quantity_kg' => 0.0,
                'published_at' => now()->subHours(1),
            ]
        );

        ProduceLotImage::firstOrCreate(
            ['produce_lot_id' => $bananaLot->id, 'image_path' => 'images/crops/banana.jpg'],
            [
                'original_name' => 'banana_grand_naine.jpg',
                'is_primary' => true,
                'is_approved_for_buyers' => true,
            ]
        );

        ProduceGradingAssessment::updateOrCreate(
            ['produce_lot_id' => $bananaLot->id],
            [
                'grade' => 'Good',
                'observed_defects' => ['Clean green peel', 'Minimal superficial freckle', 'Optimal curvature index'],
                'assessment_explanation' => 'Premium Grade A: Export-grade bunch morphology. Calyx ends intact and optimal firmness for long-distance cold transit.',
                'confidence_score' => '92% (Demonstration Heuristic)',
                'manual_verification_required' => false,
                'grading_method' => 'Demonstration Rule-Engine v1.0.0-sample',
                'engine_version' => 'v1.0.0-sample',
                'assessed_by' => $admin ? $admin->id : null,
                'is_admin_override' => false,
                'is_demonstration' => true,
            ]
        );

        // 3. Partially Sold Lot: Potato (Deesa Kufri Jyoti)
        $potatoLot = ProduceLot::updateOrCreate(
            ['lot_number' => 'LOT-POT-202610-1003'],
            [
                'farmer_id' => $farmer->id,
                'crop_slug' => 'potato',
                'variety' => 'Kufri Jyoti (Table & Chipping)',
                'quantity' => 50.0,
                'unit' => 'quintal',
                'quantity_in_kg' => 5000.0,
                'district' => 'Banaskantha',
                'taluka' => 'Deesa',
                'pincode' => '385535',
                'harvest_date' => now()->subDays(5)->toDateString(),
                'expected_price_per_kg' => 15.00,
                'description' => 'Cured cold-store ready Kufri Jyoti table potatoes. Dry harvested on alluvial soil.',
                'farming_method' => 'conventional',
                'status' => ProduceLot::STATUS_PARTIALLY_SOLD,
                'approved_grade' => 'Intermediate',
                'approved_procurement_price_per_kg' => 14.50,
                'buyer_sale_price_per_kg' => 16.50,
                'confirmed_quantity_kg' => 5000.0,
                'available_quantity_kg' => 3500.0,
                'reserved_quantity_kg' => 1500.0,
                'sold_quantity_kg' => 0.0,
                'published_at' => now()->subHours(5),
            ]
        );

        ProduceLotImage::firstOrCreate(
            ['produce_lot_id' => $potatoLot->id, 'image_path' => 'images/crops/potato.jpg'],
            [
                'original_name' => 'potato_deesa.jpg',
                'is_primary' => true,
                'is_approved_for_buyers' => true,
            ]
        );

        ProduceGradingAssessment::updateOrCreate(
            ['produce_lot_id' => $potatoLot->id],
            [
                'grade' => 'Intermediate',
                'observed_defects' => ['Slight tuber size variation (38-55mm)', 'Superficial skinning (<4%)', 'Zero solanine greening'],
                'assessment_explanation' => 'Standard Grade B: High commercial value for fresh wholesale mandis. Sound internal pulp with minor net skinning.',
                'confidence_score' => '88% (Demonstration Heuristic)',
                'manual_verification_required' => false,
                'grading_method' => 'Demonstration Rule-Engine v1.0.0-sample',
                'engine_version' => 'v1.0.0-sample',
                'assessed_by' => $admin ? $admin->id : null,
                'is_admin_override' => false,
                'is_demonstration' => true,
            ]
        );

        // Seed sample reservation for Potato Lot if buyer exists
        if ($buyer) {
            ProduceReservation::firstOrCreate(
                ['reservation_number' => 'RES-POT-202610-001'],
                [
                    'produce_lot_id' => $potatoLot->id,
                    'buyer_id' => $buyer->id,
                    'quantity_kg' => 1500.0,
                    'price_per_kg' => 16.50,
                    'total_price' => 24750.00,
                    'status' => 'confirmed',
                    'notes' => 'Allocated for Ahmedabad wholesale distribution center.',
                ]
            );

            // Seed initial notification in buyer's box
            $alreadyNotified = $buyer->notifications()->where('data->lot_id', $cottonLot->id)->exists();
            if (! $alreadyNotified) {
                $buyer->notify(new ProduceAvailableNotification($cottonLot));
            }
        }

        // 4. Lot Under Review: Chilli (Gondal Resham Patti)
        $chilliLot = ProduceLot::updateOrCreate(
            ['lot_number' => 'LOT-CHL-202610-1004'],
            [
                'farmer_id' => $farmer->id,
                'crop_slug' => 'chilli',
                'variety' => 'Resham Patti Special',
                'quantity' => 12.0,
                'unit' => 'quintal',
                'quantity_in_kg' => 1200.0,
                'district' => 'Rajkot',
                'taluka' => 'Gondal',
                'pincode' => '360311',
                'harvest_date' => now()->subDays(2)->toDateString(),
                'expected_price_per_kg' => 145.00,
                'description' => 'Deep red sun-dried pods. High pungency and glossy skin.',
                'farming_method' => 'organic',
                'status' => ProduceLot::STATUS_UNDER_REVIEW,
                'approved_grade' => 'Good',
                'approved_procurement_price_per_kg' => 146.00,
                'buyer_sale_price_per_kg' => 158.00,
                'confirmed_quantity_kg' => 0.0,
                'available_quantity_kg' => 0.0,
                'reserved_quantity_kg' => 0.0,
                'sold_quantity_kg' => 0.0,
                'published_at' => null,
            ]
        );

        ProduceLotImage::firstOrCreate(
            ['produce_lot_id' => $chilliLot->id, 'image_path' => 'images/crops/chilli.jpg'],
            [
                'original_name' => 'dry_chilli_gondal.jpg',
                'is_primary' => true,
                'is_approved_for_buyers' => true,
            ]
        );

        ProduceGradingAssessment::updateOrCreate(
            ['produce_lot_id' => $chilliLot->id],
            [
                'grade' => 'Good',
                'observed_defects' => ['Deep crimson color', 'Moisture <9%', 'Zero stalk-less pods'],
                'assessment_explanation' => 'Premium Grade A: Exceptional ASTA color unit retention and high capsanthin index.',
                'confidence_score' => '95% (Demonstration Heuristic)',
                'manual_verification_required' => true,
                'grading_method' => 'Demonstration Rule-Engine v1.0.0-sample',
                'engine_version' => 'v1.0.0-sample',
                'assessed_by' => null,
                'is_admin_override' => false,
                'is_demonstration' => true,
            ]
        );
    }
}
