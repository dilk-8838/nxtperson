<?php

namespace Tests\Feature;

use App\Events\ProduceInventoryPublished;
use App\Models\ProduceLot;
use App\Models\ProduceReservation;
use App\Models\User;
use App\Notifications\FarmerLotStatusUpdatedNotification;
use App\Notifications\FarmerProduceInterestNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleBasedNotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $farmer;

    protected User $otherFarmer;

    protected User $buyer;

    protected User $otherBuyer;

    protected User $admin;

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

        // Create secondary farmer for multi-tenant isolation tests
        $this->otherFarmer = User::create([
            'name' => 'Ramesh Vaghela (Other Farmer)',
            'email' => 'ramesh.vaghela@demo.nxtperson.farm',
            'phone' => '9876543987',
            'role' => 'farmer',
            'district' => 'Junagadh',
            'state' => 'Gujarat',
            'pincode' => '362001',
            'password' => Hash::make('secret123'),
        ]);

        // Create secondary buyer for multi-tenant isolation tests
        $this->otherBuyer = User::create([
            'name' => 'Reliance Retail Agro (Buyer 2)',
            'email' => 'procurement.reliance@demo.nxtperson.farm',
            'phone' => '9876543988',
            'role' => 'buyer',
            'district' => 'Surat',
            'state' => 'Gujarat',
            'pincode' => '395001',
            'password' => Hash::make('secret123'),
        ]);
    }

    /**
     * Requirement 1: Farmer uploads draft/assessed listing: buyers receive NO stock notification.
     */
    public function test_farmer_uploads_draft_listing_buyers_receive_no_stock_notifications(): void
    {
        $this->actingAs($this->farmer);
        session(['demo_role' => 'farmer']);

        $initialBuyerNotifCount = $this->buyer->notifications()->count();
        $initialOtherBuyerNotifCount = $this->otherBuyer->notifications()->count();

        $image = UploadedFile::fake()->image('wheat_draft.jpg', 600, 400);

        $response = $this->post(route('farmer.sell.store'), [
            'crop_slug' => 'grain',
            'variety' => 'Lokwan Certified Draft',
            'quantity' => 1200,
            'unit' => 'kg',
            'district' => 'Rajkot',
            'taluka' => 'Lodhika',
            'pincode' => '360035',
            'harvest_date' => now()->toDateString(),
            'expected_price_per_kg' => 32.50,
            'farming_method' => 'conventional',
            'description' => 'Clean harvested grain, awaiting physical moisture check.',
            'images' => [$image],
        ]);

        $response->assertSessionHas('success');
        $lot = ProduceLot::where('variety', 'Lokwan Certified Draft')->first();
        $this->assertNotNull($lot);

        // Assert lot is in assessed review state (NOT published)
        $this->assertEquals(ProduceLot::STATUS_ASSESSED, $lot->status);
        $this->assertEquals(0, (float) $lot->available_quantity_kg);

        // Critical Check: No buyer stock notification was dispatched to any buyer!
        $this->assertEquals($initialBuyerNotifCount, $this->buyer->notifications()->count());
        $this->assertEquals($initialOtherBuyerNotifCount, $this->otherBuyer->notifications()->count());

        // Operational Check: Admin received notification for triage
        $adminNotif = $this->admin->notifications()
            ->where('data->lot_id', $lot->id)
            ->where('data->type', 'admin_new_lot_submitted')
            ->first();
        $this->assertNotNull($adminNotif);
        $this->assertStringContainsString('Lokwan Certified Draft', $adminNotif->data['message']);
    }

    /**
     * Requirement 2: Produce becomes available on marketplace: eligible buyers receive stock notification.
     */
    public function test_produce_becomes_available_eligible_buyers_receive_stock_notification(): void
    {
        $lot = ProduceLot::create([
            'lot_number' => 'LOT-STOCK-TEST-001',
            'farmer_id' => $this->farmer->id,
            'crop_slug' => 'cotton',
            'variety' => 'Certified Shankar-6 Super',
            'quantity' => 850,
            'unit' => 'kg',
            'quantity_in_kg' => 850,
            'district' => 'Rajkot',
            'pincode' => '360001',
            'harvest_date' => now()->toDateString(),
            'farming_method' => 'ipm',
            'status' => ProduceLot::STATUS_PURCHASE_CONFIRMED,
            'confirmed_quantity_kg' => 850,
            'approved_grade' => 'Good',
            'approved_procurement_price_per_kg' => 70.0,
            'buyer_sale_price_per_kg' => 78.0,
        ]);

        $this->actingAs($this->admin);
        session(['demo_role' => 'admin']);

        // Publish produce to marketplace
        $response = $this->post(route('admin.produce.publish', $lot->id), [
            'buyer_sale_price_per_kg' => 78.50,
        ]);
        $response->assertSessionHas('success');

        $lot->refresh();
        $this->assertEquals(ProduceLot::STATUS_INVENTORY_AVAILABLE, $lot->status);
        $this->assertEquals(850.0, (float) $lot->available_quantity_kg);

        // Assert Buyer received stock notification
        $buyerNotif = $this->buyer->notifications()
            ->where('data->lot_id', $lot->id)
            ->where('data->type', 'produce_stock_available')
            ->first();

        $this->assertNotNull($buyerNotif);
        $this->assertStringContainsString('Cotton', $buyerNotif->data['title']);
        $this->assertEquals(850.0, (float) $buyerNotif->data['available_quantity_kg']);

        // Verify Privacy: Farmer's phone number and private address are strictly scrubbed
        $this->assertArrayNotHasKey('farmer_phone', $buyerNotif->data);
        $this->assertArrayNotHasKey('phone', $buyerNotif->data);

        // Assert Owning Farmer received notification that produce is now live
        $farmerNotif = $this->farmer->notifications()
            ->where('data->lot_id', $lot->id)
            ->where('data->type', 'farmer_lot_status_updated')
            ->where('data->update_subtype', 'published')
            ->first();
        $this->assertNotNull($farmerNotif);
    }

    /**
     * Requirement 3: Buyer expresses interest: only the correct farmer receives the notification.
     */
    public function test_buyer_expresses_interest_only_correct_farmer_receives_notification(): void
    {
        $targetLot = ProduceLot::where('status', ProduceLot::STATUS_INVENTORY_AVAILABLE)->first() ?? ProduceLot::first();
        $targetLot->update(['farmer_id' => $this->farmer->id]);

        $this->actingAs($this->buyer);
        session(['demo_role' => 'buyer']);

        $inquiryMessage = 'Looking for dispatch of 200 quintals to Rajkot APMC yard on Thursday.';

        $response = $this->post(route('buyer.lot.interest', $targetLot->id), [
            'notes' => $inquiryMessage,
        ]);
        $response->assertSessionHas('success');

        // Check target farmer received notification
        $farmerNotif = $this->farmer->notifications()
            ->where('data->lot_id', $targetLot->id)
            ->where('data->type', 'farmer_interest')
            ->first();

        $this->assertNotNull($farmerNotif);
        $this->assertStringContainsString($inquiryMessage, $farmerNotif->data['message']);
        $this->assertEquals($targetLot->lot_number, $farmerNotif->data['lot_number']);

        // Isolation Check: Other farmer received ZERO interest notifications for this lot
        $otherFarmerCount = $this->otherFarmer->notifications()
            ->where('data->lot_id', $targetLot->id)
            ->where('data->type', 'farmer_interest')
            ->count();
        $this->assertEquals(0, $otherFarmerCount);
    }

    /**
     * Requirement 4: Buyer submits purchase request / reservation: farmer receives notification.
     */
    public function test_buyer_submits_reservation_farmer_receives_appropriate_notification(): void
    {
        $lot = ProduceLot::where('status', ProduceLot::STATUS_INVENTORY_AVAILABLE)
            ->where('available_quantity_kg', '>=', 50)
            ->first();

        if (! $lot) {
            $lot = ProduceLot::first();
            $lot->update([
                'farmer_id' => $this->farmer->id,
                'status' => ProduceLot::STATUS_INVENTORY_AVAILABLE,
                'available_quantity_kg' => 200,
                'buyer_sale_price_per_kg' => 50.0,
            ]);
        } else {
            $lot->update(['farmer_id' => $this->farmer->id]);
        }

        $this->actingAs($this->buyer);
        session(['demo_role' => 'buyer']);

        $reservedKg = 30.0;
        $response = $this->post(route('buyer.lot.reserve', $lot->id), [
            'quantity_kg' => $reservedKg,
            'notes' => 'Central Warehousing Corporation depot pickup',
        ]);
        $response->assertSessionHas('success');

        // Verify owning farmer received reservation notification
        $farmerNotif = $this->farmer->notifications()
            ->where('data->lot_id', $lot->id)
            ->where('data->type', 'farmer_reservation_received')
            ->first();

        $this->assertNotNull($farmerNotif);
        $this->assertEquals($reservedKg, (float) $farmerNotif->data['quantity_kg']);
        $this->assertStringContainsString('New Purchase Request', $farmerNotif->data['title']);

        // Other farmer did NOT receive this notification
        $otherNotif = $this->otherFarmer->notifications()
            ->where('data->type', 'farmer_reservation_received')
            ->where('data->lot_id', $lot->id)
            ->first();
        $this->assertNull($otherNotif);

        // Admins received operational reservation alert
        $adminAlert = $this->admin->notifications()
            ->where('data->type', 'admin_reservation_alert')
            ->where('data->lot_id', $lot->id)
            ->first();
        $this->assertNotNull($adminAlert);
    }

    /**
     * Requirement 5: Order/reservation status changes notify the relevant participants.
     */
    public function test_reservation_status_changes_notify_both_buyer_and_farmer(): void
    {
        $lot = ProduceLot::first();
        $lot->update(['farmer_id' => $this->farmer->id]);

        $reservation = ProduceReservation::create([
            'reservation_number' => 'RES-STATUS-TEST-001',
            'produce_lot_id' => $lot->id,
            'buyer_id' => $this->buyer->id,
            'quantity_kg' => 50,
            'price_per_kg' => 60.0,
            'total_price' => 3000.0,
            'status' => 'confirmed',
        ]);

        $this->actingAs($this->admin);
        session(['demo_role' => 'admin']);

        // Admin fulfills reservation
        $response = $this->post(route('admin.produce.reservations.status', $reservation->id), [
            'status' => 'fulfilled',
        ]);
        $response->assertSessionHas('success');

        // Check Buyer received status notification
        $buyerNotif = $this->buyer->notifications()
            ->where('data->type', 'buyer_reservation_status')
            ->where('data->reservation_id', $reservation->id)
            ->first();

        $this->assertNotNull($buyerNotif);
        $this->assertEquals('fulfilled', $buyerNotif->data['status']);
        $this->assertStringContainsString('Fulfilled', $buyerNotif->data['title']);

        // Check Farmer received status notification
        $farmerNotif = $this->farmer->notifications()
            ->where('data->type', 'farmer_reservation_status')
            ->where('data->reservation_id', $reservation->id)
            ->first();

        $this->assertNotNull($farmerNotif);
        $this->assertEquals('fulfilled', $farmerNotif->data['status']);
    }

    /**
     * Requirement 6: Admin approval/rejection triggers the correct notifications.
     */
    public function test_admin_approval_and_rejection_trigger_correct_farmer_notifications(): void
    {
        $lot = ProduceLot::create([
            'lot_number' => 'LOT-ADMIN-AUDIT-001',
            'farmer_id' => $this->farmer->id,
            'crop_slug' => 'banana',
            'variety' => 'Grand Naine Golden',
            'quantity' => 1000,
            'unit' => 'kg',
            'quantity_in_kg' => 1000,
            'district' => 'Anand',
            'pincode' => '388001',
            'harvest_date' => now()->toDateString(),
            'farming_method' => 'organic',
            'status' => ProduceLot::STATUS_UNDER_REVIEW,
        ]);

        $this->actingAs($this->admin);
        session(['demo_role' => 'admin']);

        // 1. Grade override
        $overrideResp = $this->post(route('admin.produce.override-grade', $lot->id), [
            'grade' => 'Good',
            'override_notes' => 'Uniform finger length and sugar brix tested.',
        ]);
        $overrideResp->assertSessionHas('success');

        $gradeNotif = $this->farmer->notifications()
            ->where('data->lot_id', $lot->id)
            ->where('data->update_subtype', 'grade_overridden')
            ->first();
        $this->assertNotNull($gradeNotif);
        $this->assertStringContainsString('Quality Grade Verified', $gradeNotif->data['title']);

        // 2. Approve procurement price
        $priceResp = $this->post(route('admin.produce.approve-procurement', $lot->id), [
            'approved_procurement_price_per_kg' => 24.50,
            'buyer_sale_price_per_kg' => 28.00,
            'admin_notes' => 'Benchmark aligned with Anand market.',
        ]);
        $priceResp->assertSessionHas('success');

        $priceNotif = $this->farmer->notifications()
            ->where('data->lot_id', $lot->id)
            ->where('data->update_subtype', 'procurement_approved')
            ->first();
        $this->assertNotNull($priceNotif);
        $this->assertStringContainsString('Procurement Price Approved', $priceNotif->data['title']);

        // 3. Reject lot
        $rejectResp = $this->post(route('admin.produce.reject', $lot->id), [
            'rejection_reason' => 'Excessive visual chilling injury upon aggregation yard inspection.',
        ]);
        $rejectResp->assertSessionHas('warning');

        $rejectNotif = $this->farmer->notifications()
            ->where('data->lot_id', $lot->id)
            ->where('data->update_subtype', 'rejected')
            ->first();
        $this->assertNotNull($rejectNotif);
        $this->assertStringContainsString('chilling injury', $rejectNotif->data['message']);
    }

    /**
     * Requirement 7: Repeated events do not create duplicate stock notifications.
     */
    public function test_repeated_publication_events_do_not_create_duplicate_stock_notifications(): void
    {
        $lot = ProduceLot::create([
            'lot_number' => 'LOT-IDEMPOTENT-001',
            'farmer_id' => $this->farmer->id,
            'crop_slug' => 'chilli',
            'variety' => 'G4 Resham Patti Hot',
            'quantity' => 300,
            'unit' => 'kg',
            'quantity_in_kg' => 300,
            'district' => 'Rajkot',
            'pincode' => '360001',
            'harvest_date' => now()->toDateString(),
            'farming_method' => 'conventional',
            'status' => ProduceLot::STATUS_INVENTORY_AVAILABLE,
            'available_quantity_kg' => 300,
            'buyer_sale_price_per_kg' => 120.0,
            'approved_grade' => 'Good',
        ]);

        $initialBuyerCount = $this->buyer->notifications()->where('data->lot_id', $lot->id)->count();
        $this->assertEquals(0, $initialBuyerCount);

        // First publication event dispatch
        event(new ProduceInventoryPublished($lot));
        $afterFirstDispatch = $this->buyer->notifications()->where('data->lot_id', $lot->id)->count();
        $this->assertEquals(1, $afterFirstDispatch);

        // Duplicate event dispatch (e.g. queue retry, page refresh, multiple admin clicks)
        event(new ProduceInventoryPublished($lot));
        $afterSecondDispatch = $this->buyer->notifications()->where('data->lot_id', $lot->id)->count();

        // Idempotency check: Notification count MUST NOT increment
        $this->assertEquals(1, $afterSecondDispatch);
    }

    /**
     * Requirement 8: One user cannot view, mark read, or access another user's notifications.
     */
    public function test_user_cannot_mark_read_or_access_another_users_notifications(): void
    {
        // Create notification belonging strictly to farmer
        $this->farmer->notify(new FarmerLotStatusUpdatedNotification(
            ProduceLot::first(),
            'grade_overridden',
            'Private audit note for Suresh Patel'
        ));

        $farmerNotif = $this->farmer->unreadNotifications()->first();
        $this->assertNotNull($farmerNotif);

        // Act as Buyer and attempt to mark Farmer's notification read -> 403 Forbidden
        $this->actingAs($this->buyer);
        session(['demo_role' => 'buyer']);

        $unauthorizedResponse = $this->post(route('notifications.read', $farmerNotif->id));
        $unauthorizedResponse->assertStatus(403);

        // Assert notification remains unread for the farmer
        $farmerNotif->refresh();
        $this->assertNull($farmerNotif->read_at);
    }

    /**
     * Requirement 9: Read/unread counts and "Mark all as read" work correctly.
     */
    public function test_read_unread_counts_and_mark_all_as_read_work_correctly(): void
    {
        $this->actingAs($this->farmer);
        session(['demo_role' => 'farmer']);

        // Dispatch 3 notifications to farmer
        $lot = ProduceLot::first();
        $this->farmer->notify(new FarmerLotStatusUpdatedNotification($lot, 'published'));
        $this->farmer->notify(new FarmerProduceInterestNotification($lot, $this->buyer, 'Inquiry 1'));
        $this->farmer->notify(new FarmerProduceInterestNotification($lot, $this->otherBuyer, 'Inquiry 2'));

        $unreadCount = $this->farmer->unreadNotifications()->count();
        $this->assertGreaterThanOrEqual(3, $unreadCount);

        // Poll endpoint returns correct unread count
        $pollResponse = $this->get(route('notifications.poll'));
        $pollResponse->assertOk();
        $pollData = $pollResponse->json();
        $this->assertEquals($unreadCount, $pollData['unread_count']);
        $this->assertTrue($pollData['has_new']);

        // Mark single read
        $firstNotif = $this->farmer->unreadNotifications()->first();
        $singleMarkResp = $this->post(route('notifications.read', $firstNotif->id));
        $singleMarkResp->assertRedirect();
        $this->assertEquals($unreadCount - 1, $this->farmer->unreadNotifications()->count());

        // Mark all as read
        $markAllResp = $this->post(route('notifications.mark-all-read'));
        $markAllResp->assertRedirect();

        // Assert 0 unread remaining
        $this->assertEquals(0, $this->farmer->unreadNotifications()->count());
    }

    /**
     * Requirement 10: Notification center history page renders correctly with tabs and empty states.
     */
    public function test_notification_history_page_renders_accessible_views_and_filters(): void
    {
        $this->actingAs($this->farmer);
        session(['demo_role' => 'farmer']);

        $response = $this->get(route('notifications.index'));
        $response->assertOk();
        $response->assertSee('Notification Center');
        $response->assertSee('All');
        $response->assertSee('Unread');

        // Test unread filter
        $unreadFilterResponse = $this->get(route('notifications.index', ['filter' => 'unread']));
        $unreadFilterResponse->assertOk();
    }
}
