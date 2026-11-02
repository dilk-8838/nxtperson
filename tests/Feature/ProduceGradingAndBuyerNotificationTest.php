<?php

namespace Tests\Feature;

use App\Events\ProduceInventoryPublished;
use App\Models\Crop;
use App\Models\ProduceLot;
use App\Models\ProduceReservation;
use App\Models\User;
use App\Services\Grading\ProduceGradingServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProduceGradingAndBuyerNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $farmer;

    protected User $admin;

    protected User $buyer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $targetDir = public_path('uploads'.DIRECTORY_SEPARATOR.'produce_lots');
        if (! file_exists($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        $this->farmer = User::where('role', 'farmer')->first();
        $this->admin = User::where('role', 'admin')->first();
        $this->buyer = User::where('role', 'buyer')->first();
    }

    /**
     * Test 1: Farmer submits multiple photos and lot is registered with assessments.
     */
    public function test_farmer_submits_multiple_photos_and_receives_crop_specific_assessment(): void
    {
        $this->actingAs($this->farmer);
        session(['demo_role' => 'farmer']);

        $image1 = UploadedFile::fake()->image('cotton_sample_1.jpg', 800, 600);
        $image2 = UploadedFile::fake()->image('cotton_sample_2.jpg', 800, 600);

        $response = $this->post(route('farmer.sell.store'), [
            'crop_slug' => 'cotton',
            'variety' => 'Shankar-6 Certified',
            'quantity' => 1500,
            'unit' => 'kg',
            'district' => 'Rajkot',
            'taluka' => 'Gondal',
            'pincode' => '360311',
            'harvest_date' => now()->subDays(2)->toDateString(),
            'expected_price_per_kg' => 74.00,
            'farming_method' => 'ipm',
            'description' => 'First-pick hand-harvested cotton bolls from canal-irrigated plot.',
            'images' => [$image1, $image2],
        ]);

        $response->assertSessionHas('success');
        $lot = ProduceLot::where('variety', 'Shankar-6 Certified')->first();
        $this->assertNotNull($lot);

        $response->assertRedirect(route('farmer.lots.show', $lot->id));

        // Assert 2 images stored
        $this->assertCount(2, $lot->images);
        $this->assertDatabaseHas('produce_lot_images', [
            'produce_lot_id' => $lot->id,
            'is_primary' => true,
        ]);

        // Assert quality assessment created
        $this->assertDatabaseHas('produce_grading_assessments', [
            'produce_lot_id' => $lot->id,
            'is_demonstration' => true,
        ]);

        $this->assertEquals(ProduceLot::STATUS_ASSESSED, $lot->status);
        $this->assertEquals(1500.0, (float) $lot->quantity_in_kg);
    }

    /**
     * Test 2: Farmer cannot edit or view another farmer's private lot.
     */
    public function test_farmer_cannot_view_or_modify_another_farmers_lot(): void
    {
        $otherFarmer = User::create([
            'name' => 'Bhavik Prajapati',
            'email' => 'bhavik@demo.nxtperson.farm',
            'phone' => '9876543999',
            'role' => 'farmer',
            'district' => 'Anand',
            'state' => 'Gujarat',
            'pincode' => '388001',
            'password' => Hash::make('secret123'),
        ]);

        $otherLot = ProduceLot::create([
            'lot_number' => 'LOT-BAN-TEST-001',
            'farmer_id' => $otherFarmer->id,
            'crop_slug' => 'banana',
            'variety' => 'Grand Naine Private',
            'quantity' => 500,
            'unit' => 'kg',
            'quantity_in_kg' => 500,
            'district' => 'Anand',
            'pincode' => '388001',
            'harvest_date' => now()->toDateString(),
            'farming_method' => 'Conventional',
            'status' => ProduceLot::STATUS_ASSESSED,
        ]);

        // Acting as first farmer
        $this->actingAs($this->farmer);
        session(['demo_role' => 'farmer']);

        // Attempt viewing other farmer's private lot -> 403
        $response = $this->get(route('farmer.lots.show', $otherLot->id));
        $response->assertStatus(403);

        // Attempt submitting other farmer's lot for review -> 403
        $submitResponse = $this->post(route('farmer.lots.submit', $otherLot->id));
        $submitResponse->assertStatus(403);
    }

    /**
     * Test 3: Simulated grading is clearly labeled and crop-specific.
     */
    public function test_simulated_grading_is_clearly_labeled_with_crop_specific_criteria(): void
    {
        $gradingService = app(ProduceGradingServiceInterface::class);

        $lot = ProduceLot::where('crop_slug', 'cotton')->first();
        $this->assertNotNull($lot);

        $assessment = $gradingService->assess($lot, ['fake/path.jpg']);

        $this->assertContains($assessment['grade'], ['Good', 'Intermediate', 'Bad']);
        $this->assertTrue($assessment['manual_verification_required']);
        $this->assertStringContainsString('Demonstration', $assessment['grading_method']);

        // Assert crop standards exist and have crop-specific criteria
        $standards = $gradingService->getCropStandards('cotton');
        $this->assertStringContainsString('Cotton', $standards['name']);
        $this->assertIsArray($standards['good_defects']);
        $this->assertStringContainsString('bollworm', strtolower(implode(' ', $standards['bad_defects'])));
    }

    /**
     * Test 4: Admin reviews grading, applies manual override, and approves procurement price.
     */
    public function test_admin_can_review_override_grade_and_approve_procurement_price(): void
    {
        $lot = ProduceLot::where('status', ProduceLot::STATUS_UNDER_REVIEW)->first()
            ?? ProduceLot::where('status', ProduceLot::STATUS_ASSESSED)->first();
        $this->assertNotNull($lot);

        $this->actingAs($this->admin);
        session(['demo_role' => 'admin']);

        // 1. View admin review page
        $viewResponse = $this->get(route('admin.produce.show', $lot->id));
        $viewResponse->assertOk();
        $viewResponse->assertSee('Visual Defects');

        // 2. Override Grade to Good
        $overrideResponse = $this->post(route('admin.produce.override-grade', $lot->id), [
            'grade' => 'Good',
            'override_notes' => 'Physical fiber inspection verified Grade Good staple uniformity.',
        ]);
        $overrideResponse->assertSessionHas('success');

        $lot->refresh();
        $this->assertEquals('Good', $lot->approved_grade);
        $this->assertDatabaseHas('produce_grading_assessments', [
            'produce_lot_id' => $lot->id,
            'is_admin_override' => true,
            'grade' => 'Good',
        ]);

        // 3. Approve Procurement Price
        $priceResponse = $this->post(route('admin.produce.approve-procurement', $lot->id), [
            'approved_procurement_price_per_kg' => 72.50,
            'buyer_sale_price_per_kg' => 78.00,
            'admin_notes' => 'Aligned with APMC Rajkot modal benchmarks.',
        ]);
        $priceResponse->assertSessionHas('success');

        $lot->refresh();
        $this->assertEquals(ProduceLot::STATUS_APPROVED_FOR_PROCUREMENT, $lot->status);
        $this->assertEquals(72.50, (float) $lot->approved_procurement_price_per_kg);
        $this->assertEquals(78.00, (float) $lot->buyer_sale_price_per_kg);
    }

    /**
     * Test 5: Admin confirms procurement purchase.
     */
    public function test_admin_confirms_procurement_purchase(): void
    {
        $lot = ProduceLot::where('status', ProduceLot::STATUS_APPROVED_FOR_PROCUREMENT)->first();
        if (! $lot) {
            $lot = ProduceLot::first();
            $lot->update([
                'status' => ProduceLot::STATUS_APPROVED_FOR_PROCUREMENT,
                'approved_procurement_price_per_kg' => 70.00,
            ]);
        }

        $this->actingAs($this->admin);
        session(['demo_role' => 'admin']);

        $response = $this->post(route('admin.produce.confirm-purchase', $lot->id), [
            'confirmed_quantity_kg' => 950.0,
            'admin_notes' => 'Received at Nxtperson Rajkot Aggregation Hub with zero transit damage.',
        ]);

        $response->assertSessionHas('success');
        $lot->refresh();
        $this->assertEquals(ProduceLot::STATUS_PURCHASE_CONFIRMED, $lot->status);
        $this->assertEquals(950.0, (float) $lot->confirmed_quantity_kg);
    }

    /**
     * Test 6: Stock becomes available on marketplace upon INVENTORY_AVAILABLE publication.
     */
    public function test_stock_publication_transitions_to_inventory_available(): void
    {
        $lot = ProduceLot::where('status', ProduceLot::STATUS_PURCHASE_CONFIRMED)->first();
        if (! $lot) {
            $lot = ProduceLot::first();
            $lot->update([
                'status' => ProduceLot::STATUS_PURCHASE_CONFIRMED,
                'confirmed_quantity_kg' => 800.0,
                'approved_procurement_price_per_kg' => 68.00,
            ]);
        }

        $this->actingAs($this->admin);
        session(['demo_role' => 'admin']);

        $response = $this->post(route('admin.produce.publish', $lot->id), [
            'buyer_sale_price_per_kg' => 76.50,
        ]);

        $response->assertSessionHas('success');
        $lot->refresh();
        $this->assertEquals(ProduceLot::STATUS_INVENTORY_AVAILABLE, $lot->status);
        $this->assertEquals(800.0, (float) $lot->available_quantity_kg);
        $this->assertEquals(76.50, (float) $lot->buyer_sale_price_per_kg);
        $this->assertNotNull($lot->published_at);
    }

    /**
     * Test 7: Live polling endpoint returns new notifications without refreshing.
     */
    public function test_buyer_receives_new_notification_without_refreshing_via_polling(): void
    {
        $this->actingAs($this->buyer);
        session(['demo_role' => 'buyer']);

        // Create a new un-notified lot
        $newLot = ProduceLot::create([
            'lot_number' => 'LOT-POLL-TEST-001',
            'farmer_id' => $this->farmer->id,
            'crop_slug' => 'chilli',
            'variety' => 'G4 Resham Patti',
            'quantity' => 200,
            'unit' => 'kg',
            'quantity_in_kg' => 200,
            'district' => 'Rajkot',
            'pincode' => '360001',
            'harvest_date' => now()->toDateString(),
            'farming_method' => 'Conventional',
            'status' => ProduceLot::STATUS_INVENTORY_AVAILABLE,
            'approved_grade' => 'Good',
            'available_quantity_kg' => 200,
            'buyer_sale_price_per_kg' => 145.0,
        ]);

        // Publish event for this new lot
        event(new ProduceInventoryPublished($newLot));

        // Poll endpoint
        $pollResponse = $this->get(route('buyer.notifications.poll'));
        $pollResponse->assertOk();
        $pollResponse->assertJsonStructure([
            'status',
            'unread_count',
            'new_count',
            'has_new',
            'polled_at',
            'notifications' => [
                '*' => [
                    'id',
                    'title',
                    'message',
                    'crop_name',
                    'variety',
                    'grade',
                    'available_quantity_kg',
                    'district',
                    'price_per_kg',
                    'image_url',
                    'view_url',
                    'created_at',
                    'time_ago',
                ],
            ],
        ]);

        $data = $pollResponse->json();
        $this->assertTrue($data['has_new']);
        $this->assertGreaterThanOrEqual(1, count($data['notifications']));

        // Verify Privacy: Farmer PII is completely scrubbed from notifications
        $firstNotif = $data['notifications'][0];
        $this->assertArrayNotHasKey('farmer_name', $firstNotif);
        $this->assertArrayNotHasKey('farmer_phone', $firstNotif);
    }

    /**
     * Test 8: Offline buyer sees notification history on login and can mark read.
     */
    public function test_offline_buyer_sees_notification_history_on_login(): void
    {
        $this->actingAs($this->buyer);
        session(['demo_role' => 'buyer']);

        $response = $this->get(route('buyer.notifications'));
        $response->assertOk();
        $response->assertSee('Notification Center');

        $unreadNotif = $this->buyer->unreadNotifications()->first();
        if ($unreadNotif) {
            $markResponse = $this->post(route('buyer.notifications.read', $unreadNotif->id));
            $markResponse->assertRedirect();
            $unreadNotif->refresh();
            $this->assertNotNull($unreadNotif->read_at);
        }
    }

    /**
     * Test 9: Buyer cannot see unpublished or draft produce lots.
     */
    public function test_buyer_cannot_view_unpublished_or_draft_produce_lots(): void
    {
        $draftLot = ProduceLot::create([
            'lot_number' => 'LOT-DRAFT-SECRET-999',
            'farmer_id' => $this->farmer->id,
            'crop_slug' => 'tomato',
            'variety' => 'Secret Hybrid',
            'quantity' => 100,
            'unit' => 'kg',
            'quantity_in_kg' => 100,
            'district' => 'Surat',
            'pincode' => '395001',
            'harvest_date' => now()->toDateString(),
            'farming_method' => 'Organic',
            'status' => ProduceLot::STATUS_UNDER_REVIEW,
        ]);

        $this->actingAs($this->buyer);
        session(['demo_role' => 'buyer']);

        // Check marketplace listing does NOT show unpublished lot
        $marketplaceResponse = $this->get(route('buyer.marketplace'));
        $marketplaceResponse->assertOk();
        $marketplaceResponse->assertDontSee('LOT-DRAFT-SECRET-999');

        // Check direct access returns 404
        $detailResponse = $this->get(route('buyer.lot.show', $draftLot->id));
        $detailResponse->assertStatus(404);
    }

    /**
     * Test 10: Duplicate publications do not generate duplicate alerts.
     */
    public function test_duplicate_publications_do_not_generate_duplicate_alerts(): void
    {
        $testLot = ProduceLot::create([
            'lot_number' => 'LOT-DUP-TEST-001',
            'farmer_id' => $this->farmer->id,
            'crop_slug' => 'grain',
            'variety' => 'Bhalia Wheat Special',
            'quantity' => 500,
            'unit' => 'kg',
            'quantity_in_kg' => 500,
            'district' => 'Ahmedabad',
            'pincode' => '382401',
            'harvest_date' => now()->toDateString(),
            'farming_method' => 'Rainfed GI',
            'status' => ProduceLot::STATUS_INVENTORY_AVAILABLE,
            'approved_grade' => 'Good',
            'available_quantity_kg' => 500,
            'buyer_sale_price_per_kg' => 32.0,
        ]);

        $initialNotificationCount = $this->buyer->notifications()->count();

        // Dispatch publication event
        event(new ProduceInventoryPublished($testLot));
        $countAfterFirst = $this->buyer->notifications()->count();
        $this->assertEquals($initialNotificationCount + 1, $countAfterFirst);

        // Dispatch same publication event again
        event(new ProduceInventoryPublished($testLot));
        $countAfterSecond = $this->buyer->notifications()->count();

        // Idempotency check: Duplicate event did not create duplicate database notification
        $this->assertEquals($countAfterFirst, $countAfterSecond);
    }

    /**
     * Test 11: Concurrent reservations cannot exceed available inventory.
     */
    public function test_concurrent_reservations_cannot_exceed_available_inventory(): void
    {
        $lot = ProduceLot::where('status', ProduceLot::STATUS_INVENTORY_AVAILABLE)
            ->where('available_quantity_kg', '>', 50)
            ->first();

        $this->assertNotNull($lot);
        $initialAvailable = (float) $lot->available_quantity_kg;

        $this->actingAs($this->buyer);
        session(['demo_role' => 'buyer']);

        // 1. Attempt to reserve more than available inventory (must fail validation)
        $excessQuantity = $initialAvailable + 500;
        $failResponse = $this->post(route('buyer.lot.reserve', $lot->id), [
            'quantity_kg' => $excessQuantity,
            'notes' => 'Excess order',
        ]);

        $failResponse->assertSessionHasErrors('quantity_kg');
        $lot->refresh();
        $this->assertEquals($initialAvailable, (float) $lot->available_quantity_kg);

        // 2. Reserve a valid portion (e.g. 20 kg)
        $validQuantity = 20.0;
        $successResponse = $this->post(route('buyer.lot.reserve', $lot->id), [
            'quantity_kg' => $validQuantity,
            'notes' => 'Valid allocation test',
        ]);

        $successResponse->assertSessionHas('success');
        $lot->refresh();
        $this->assertEquals($initialAvailable - $validQuantity, (float) $lot->available_quantity_kg);
        $this->assertEquals($validQuantity, (float) $lot->reserved_quantity_kg);

        // Assert reservation created
        $this->assertDatabaseHas('produce_reservations', [
            'produce_lot_id' => $lot->id,
            'buyer_id' => $this->buyer->id,
            'quantity_kg' => $validQuantity,
            'status' => 'confirmed',
        ]);
    }

    /**
     * Test 12: Admin can cancel or fulfill buyer reservations and restore inventory safely.
     */
    public function test_admin_can_manage_reservations_and_restore_inventory_on_cancellation(): void
    {
        $reservation = ProduceReservation::where('status', 'confirmed')->first();
        if (! $reservation) {
            $lot = ProduceLot::where('status', ProduceLot::STATUS_INVENTORY_AVAILABLE)->first();
            $reservation = ProduceReservation::create([
                'reservation_number' => 'RES-TEST-001',
                'produce_lot_id' => $lot->id,
                'buyer_id' => $this->buyer->id,
                'quantity_kg' => 50,
                'price_per_kg' => 75.0,
                'total_price' => 3750.0,
                'notes' => 'Test Warehouse, Rajkot',
                'status' => 'confirmed',
            ]);
            $lot->decrement('available_quantity_kg', 50);
            $lot->increment('reserved_quantity_kg', 50);
        }

        $lot = $reservation->lot;
        $initialAvailable = (float) $lot->available_quantity_kg;
        $initialReserved = (float) $lot->reserved_quantity_kg;
        $reservedQty = (float) $reservation->quantity_kg;

        $this->actingAs($this->admin);
        session(['demo_role' => 'admin']);

        // Cancel reservation -> should restore inventory
        $cancelResponse = $this->post(route('admin.produce.reservations.status', $reservation->id), [
            'status' => 'cancelled',
        ]);

        $cancelResponse->assertSessionHas('success');
        $reservation->refresh();
        $lot->refresh();

        $this->assertEquals('cancelled', $reservation->status);
        $this->assertEquals($initialAvailable + $reservedQty, (float) $lot->available_quantity_kg);
        $this->assertEquals(max(0, $initialReserved - $reservedQty), (float) $lot->reserved_quantity_kg);
    }
}
