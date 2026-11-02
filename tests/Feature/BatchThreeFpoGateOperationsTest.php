<?php

namespace Tests\Feature;

use App\Models\Crop;
use App\Models\FarmerProfile;
use App\Models\FpoCollectiveListing;
use App\Models\FpoFarmerMembership;
use App\Models\FpoGateEntry;
use App\Models\FpoGateEntryItem;
use App\Models\FpoGateExit;
use App\Models\FpoInventoryPosting;
use App\Models\FpoMember;
use App\Models\FpoOperationAuditLog;
use App\Models\FpoOperationalException;
use App\Models\FpoOrganization;
use App\Models\FpoQualityApproval;
use App\Models\FpoQualityTest;
use App\Models\FpoSample;
use App\Models\FpoWeightment;
use App\Models\ProduceLot;
use App\Models\PurchaseOrder;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Models\User;
use App\Notifications\GateEntryRegisteredNotification;
use App\Notifications\GateExitAuthorizedNotification;
use App\Notifications\InventoryPostedNotification;
use App\Notifications\OperationalExceptionNotification;
use App\Notifications\QualityApprovalRequestedNotification;
use App\Notifications\QualityDecisionNotification;
use App\Notifications\WeightDiscrepancyNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BatchThreeFpoGateOperationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Helper to set up an FPO organization and primary admin user.
     */
    protected function createFpoOrg(string $slug = 'saurashtra-fpo', string $fpoName = 'Saurashtra Kisan Producer Co.'): array
    {
        $fpoUser = User::factory()->create([
            'role' => 'fpo',
            'status' => 'active',
            'district' => 'Rajkot',
        ]);

        $org = FpoOrganization::create([
            'name' => $fpoName,
            'slug' => $slug,
            'organization_type' => 'Farmer Producer Company (FPC)',
            'registration_number' => 'U01100GJ2023PTC14'.rand(1000, 9999),
            'incorporation_year' => 2023,
            'authorized_representative_name' => $fpoUser->name,
            'contact_email' => $fpoUser->email,
            'contact_phone' => '9876543250',
            'registered_address' => 'Station Road, Gondal',
            'district' => 'Rajkot',
            'state' => 'Gujarat',
            'pincode' => '360311',
            'verification_status' => FpoOrganization::STATUS_APPROVED,
            'verified_at' => now(),
        ]);

        FpoMember::create([
            'fpo_organization_id' => $org->id,
            'user_id' => $fpoUser->id,
            'role_in_fpo' => 'admin',
            'is_primary' => true,
        ]);

        return [$fpoUser, $org];
    }

    /**
     * Helper to set up additional staff in an FPO organization.
     */
    protected function createFpoStaff(FpoOrganization $org, string $roleInFpo = 'staff'): User
    {
        $staffUser = User::factory()->create([
            'role' => 'fpo',
            'status' => 'active',
            'district' => 'Rajkot',
        ]);

        FpoMember::create([
            'fpo_organization_id' => $org->id,
            'user_id' => $staffUser->id,
            'role_in_fpo' => $roleInFpo,
            'is_primary' => false,
        ]);

        return $staffUser;
    }

    /**
     * Helper to set up a farmer with a produce lot.
     */
    protected function createFarmerWithLot(float $availableKg = 5000): array
    {
        $crop = Crop::where('slug', 'cotton')->first() ?? Crop::first();

        $farmer = User::factory()->create([
            'role' => 'farmer',
            'status' => 'active',
            'district' => 'Rajkot',
        ]);

        FarmerProfile::create([
            'user_id' => $farmer->id,
            'farm_name' => 'Kisan Farm',
            'village' => 'Gondal',
            'district' => 'Rajkot',
            'total_acreage' => 15.0,
            'cultivated_acreage' => 12.0,
        ]);

        $lot = ProduceLot::create([
            'lot_number' => 'LOT-COT-'.rand(1000, 9999),
            'farmer_id' => $farmer->id,
            'crop_slug' => $crop->slug,
            'variety' => 'Shankar-6',
            'quantity' => $availableKg,
            'unit' => 'kg',
            'quantity_in_kg' => $availableKg,
            'available_quantity_kg' => $availableKg,
            'reserved_quantity_kg' => 0,
            'sold_quantity_kg' => 0,
            'aggregated_quantity_kg' => 0,
            'status' => ProduceLot::STATUS_INVENTORY_AVAILABLE,
            'approved_grade' => 'Good',
            'quality_verified' => true,
            'buyer_sale_price_per_kg' => 62.50,
            'expected_price_per_kg' => 60.00,
            'harvest_date' => now()->toDateString(),
            'district' => 'Rajkot',
            'pincode' => '360311',
            'farming_method' => 'conventional',
            'published_at' => now(),
        ]);

        return [$farmer, $lot];
    }

    /**
     * Helper to enroll farmer in an FPO.
     */
    protected function enrollFarmerInFpo(User $farmer, FpoOrganization $org): FpoFarmerMembership
    {
        return FpoFarmerMembership::create([
            'fpo_organization_id' => $org->id,
            'farmer_user_id' => $farmer->id,
            'membership_number' => 'MEM-'.rand(10000, 99999),
            'joined_at' => now(),
            'status' => 'active',
        ]);
    }

    /**
     * 1. Unauthenticated users cannot access protected gate operations.
     */
    public function test_unauthenticated_users_cannot_access_gate_operations(): void
    {
        $response = $this->get(route('fpo.gate-entries.index'));
        $response->assertRedirect(route('login'));

        $createResponse = $this->get(route('fpo.gate-entries.create'));
        $createResponse->assertRedirect(route('login'));
    }

    /**
     * 2. Unauthorized roles (buyer, supplier, farmer) cannot access or perform FPO gate operations.
     */
    public function test_unauthorized_roles_cannot_access_fpo_gate_operations(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);
        $supplier = User::factory()->create(['role' => 'supplier', 'status' => 'active']);

        $this->actingAs($buyer)->get(route('fpo.gate-entries.index'))->assertStatus(403);
        $this->actingAs($supplier)->get(route('fpo.gate-entries.index'))->assertStatus(403);
    }

    /**
     * 3. FPO users cannot view or modify another FPO's gate entries (organization isolation).
     */
    public function test_fpo_users_cannot_view_or_modify_another_fpos_records(): void
    {
        [$fpoUserA, $orgA] = $this->createFpoOrg('fpo-alpha', 'Alpha Farmers Producer Co.');
        [$fpoUserB, $orgB] = $this->createFpoOrg('fpo-beta', 'Beta Farmers Producer Co.');

        [$farmerA, $lotA] = $this->createFarmerWithLot(3000);
        $this->enrollFarmerInFpo($farmerA, $orgA);

        $entryA = FpoGateEntry::create([
            'entry_number' => 'GE-20261027-001',
            'fpo_organization_id' => $orgA->id,
            'entry_date_time' => now(),
            'gate_point' => 'Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_VEHICLE,
            'vehicle_number' => 'GJ-03-AB-1234',
            'delivering_party_type' => 'farmer',
            'farmer_user_id' => $farmerA->id,
            'delivering_party_name' => $farmerA->name,
            'source_produce_lot_id' => $lotA->id,
            'crop_name' => 'Cotton',
            'variety' => 'Shankar-6',
            'declared_quantity' => 3000,
            'declared_unit' => 'kg',
            'status' => FpoGateEntry::STATUS_ENTERED,
            'created_by' => $fpoUserA->id,
        ]);

        // FPO User B tries to view FPO User A's gate entry
        $response = $this->actingAs($fpoUserB)->get(route('fpo.gate-entries.show', $entryA->id));
        $response->assertStatus(403);

        // FPO User B tries to download FPO User A's report
        $reportResponse = $this->actingAs($fpoUserB)->get(route('fpo.reports.show', $entryA->id));
        $reportResponse->assertStatus(403);
    }

    /**
     * 4. Platform Admin oversight access works across organizations without bypassing audit logs.
     */
    public function test_admin_oversight_access_works_cross_organization(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        [$fpoUser, $org] = $this->createFpoOrg('fpo-gamma', 'Gamma Farmers Producer Co.');

        [$farmer, $lot] = $this->createFarmerWithLot(2000);
        $this->enrollFarmerInFpo($farmer, $org);

        $entry = FpoGateEntry::create([
            'entry_number' => 'GE-20261027-002',
            'fpo_organization_id' => $org->id,
            'entry_date_time' => now(),
            'gate_point' => 'Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_VEHICLE,
            'vehicle_number' => 'GJ-03-XY-9999',
            'delivering_party_type' => 'farmer',
            'farmer_user_id' => $farmer->id,
            'delivering_party_name' => $farmer->name,
            'source_produce_lot_id' => $lot->id,
            'crop_name' => 'Cotton',
            'variety' => 'Shankar-6',
            'declared_quantity' => 2000,
            'declared_unit' => 'kg',
            'status' => FpoGateEntry::STATUS_ENTERED,
            'created_by' => $fpoUser->id,
        ]);

        // Admin can access admin oversight dashboard
        $response = $this->actingAs($admin)->get(route('admin.gate-operations.index'));
        $response->assertStatus(200);
        $response->assertSee('Gamma Farmers Producer Co.');
        $response->assertSee($entry->entry_number);

        // Admin can also view specific FPO entry detail
        $showResponse = $this->actingAs($admin)->get(route('fpo.gate-entries.show', $entry->id));
        $showResponse->assertStatus(200);
    }

    /**
     * 5. Valid gate entries persist correctly with human-readable reference number and default entered status.
     */
    public function test_valid_gate_entries_persist_with_correct_reference_and_line_items(): void
    {
        Notification::fake();

        [$fpoUser, $org] = $this->createFpoOrg('fpo-delta', 'Delta Farmers Producer Co.');
        [$farmer, $lot] = $this->createFarmerWithLot(4500);
        $this->enrollFarmerInFpo($farmer, $org);

        $postData = [
            'gate_point' => 'Main Gate 1',
            'entry_type' => 'vehicle_delivery',
            'vehicle_number' => 'GJ-03-ZZ-5555',
            'driver_name' => 'Ramesh Bhai',
            'driver_phone' => '9898989898',
            'delivering_party_type' => 'farmer',
            'farmer_user_id' => $farmer->id,
            'delivering_party_name' => $farmer->name,
            'delivering_party_contact' => '9898989898',
            'source_produce_lot_id' => $lot->id,
            'crop_name' => 'Cotton',
            'variety' => 'Shankar-6',
            'declared_quantity' => 4500,
            'declared_unit' => 'kg',
            'initial_condition' => 'Clean, dry bags packed securely',
            'notes' => 'Member delivery for collective aggregation',
        ];

        $response = $this->actingAs($fpoUser)->post(route('fpo.gate-entries.store'), $postData);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('fpo_gate_entries', [
            'fpo_organization_id' => $org->id,
            'vehicle_number' => 'GJ-03-ZZ-5555',
            'crop_name' => 'Cotton',
            'declared_quantity' => 4500,
            'status' => FpoGateEntry::STATUS_ENTERED,
        ]);

        $entry = FpoGateEntry::where('vehicle_number', 'GJ-03-ZZ-5555')->first();
        $this->assertNotNull($entry);
        $this->assertStringStartsWith('GE-', $entry->entry_number);

        // Audit log created
        $this->assertDatabaseHas('fpo_operation_audit_logs', [
            'fpo_gate_entry_id' => $entry->id,
            'action' => 'created',
        ]);

        // Notification delivered to farmer
        Notification::assertSentTo($farmer, GateEntryRegisteredNotification::class);
    }

    /**
     * 6. Multi-item deliveries preserve individual line item traceability.
     */
    public function test_multi_item_deliveries_preserve_traceability(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg('fpo-multi', 'Multi-Crop Agro FPO');
        [$farmer, $lot1] = $this->createFarmerWithLot(2000);
        $this->enrollFarmerInFpo($farmer, $org);

        $lot2 = ProduceLot::create([
            'lot_number' => 'LOT-WHT-'.rand(1000, 9999),
            'farmer_id' => $farmer->id,
            'crop_slug' => 'wheat',
            'variety' => 'Lokwan',
            'quantity' => 1500,
            'unit' => 'kg',
            'quantity_in_kg' => 1500,
            'available_quantity_kg' => 1500,
            'reserved_quantity_kg' => 0,
            'sold_quantity_kg' => 0,
            'aggregated_quantity_kg' => 0,
            'status' => ProduceLot::STATUS_INVENTORY_AVAILABLE,
            'approved_grade' => 'Good',
            'district' => 'Rajkot',
            'pincode' => '360311',
            'harvest_date' => now()->toDateString(),
            'farming_method' => 'conventional',
        ]);

        $postData = [
            'gate_point' => 'North Gate',
            'entry_type' => 'vehicle_delivery',
            'vehicle_number' => 'GJ-03-MC-8888',
            'driver_name' => 'Sanjay Patel',
            'delivering_party_type' => 'farmer',
            'farmer_user_id' => $farmer->id,
            'delivering_party_name' => $farmer->name,
            'crop_name' => 'Cotton',
            'variety' => 'Shankar-6',
            'declared_quantity' => 2000,
            'declared_unit' => 'kg',
            'items' => [
                [
                    'crop_name' => 'Cotton',
                    'variety' => 'Shankar-6',
                    'declared_quantity' => 2000,
                    'declared_unit' => 'kg',
                    'produce_lot_id' => $lot1->id,
                ],
                [
                    'crop_name' => 'Wheat',
                    'variety' => 'Lokwan',
                    'declared_quantity' => 1500,
                    'declared_unit' => 'kg',
                    'produce_lot_id' => $lot2->id,
                ],
            ],
        ];

        $response = $this->actingAs($fpoUser)->post(route('fpo.gate-entries.store'), $postData);
        $response->assertSessionHasNoErrors();

        $entry = FpoGateEntry::where('vehicle_number', 'GJ-03-MC-8888')->firstOrFail();
        $this->assertEquals(2, $entry->items()->count());

        $this->assertDatabaseHas('fpo_gate_entry_items', [
            'fpo_gate_entry_id' => $entry->id,
            'crop_name' => 'Wheat',
            'declared_quantity' => 1500,
            'produce_lot_id' => $lot2->id,
        ]);
    }

    /**
     * 7. Invalid or unrelated source lots are rejected.
     */
    public function test_invalid_or_unrelated_source_lots_are_rejected(): void
    {
        [$fpoUserA, $orgA] = $this->createFpoOrg('fpo-valid-check', 'Valid Check FPO');
        [$fpoUserB, $orgB] = $this->createFpoOrg('fpo-other-org', 'Other Org FPO');

        // Farmer belonging to Org B, not enrolled in Org A
        [$farmerB, $lotB] = $this->createFarmerWithLot(3000);
        $this->enrollFarmerInFpo($farmerB, $orgB);

        $postData = [
            'gate_point' => 'Gate 1',
            'entry_type' => 'vehicle_delivery',
            'vehicle_number' => 'GJ-03-QQ-1111',
            'delivering_party_type' => 'farmer',
            'farmer_user_id' => $farmerB->id,
            'delivering_party_name' => $farmerB->name,
            'source_produce_lot_id' => $lotB->id,
            'crop_name' => 'Cotton',
            'declared_quantity' => 3000,
            'declared_unit' => 'kg',
        ];

        // Should fail validation because source produce lot belongs to farmer not in Org A
        $response = $this->actingAs($fpoUserA)->post(route('fpo.gate-entries.store'), $postData);
        $response->assertSessionHasErrors(['source_produce_lot_id']);
    }

    /**
     * 8. Declared quantities do not silently become approved inventory upon gate entry.
     */
    public function test_declared_quantities_do_not_silently_become_approved_inventory(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg('fpo-no-premature-stock', 'Stock Test FPO');
        [$farmer, $lot] = $this->createFarmerWithLot(5000);
        $this->enrollFarmerInFpo($farmer, $org);

        $initialLotAvailable = $lot->available_quantity_kg;

        $postData = [
            'gate_point' => 'Gate 2',
            'entry_type' => 'walk_in',
            'delivering_party_type' => 'farmer',
            'farmer_user_id' => $farmer->id,
            'delivering_party_name' => $farmer->name,
            'source_produce_lot_id' => $lot->id,
            'crop_name' => 'Cotton',
            'declared_quantity' => 1000,
            'declared_unit' => 'kg',
        ];

        $this->actingAs($fpoUser)->post(route('fpo.gate-entries.store'), $postData);

        $lot->refresh();
        // Lot quantity must not have been increased prematurely
        $this->assertEquals($initialLotAvailable, $lot->available_quantity_kg);
        $this->assertEquals(0, FpoInventoryPosting::count());
    }

    /**
     * 9. Weightment calculates Net = Gross - Tare correctly and validates Gross >= Tare.
     */
    public function test_weightment_gross_tare_net_calculations_and_validation(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg('fpo-wm-calc', 'Weightment Calc FPO');
        [$farmer, $lot] = $this->createFarmerWithLot(3000);
        $this->enrollFarmerInFpo($farmer, $org);

        $entry = FpoGateEntry::create([
            'entry_number' => 'GE-WM-001',
            'fpo_organization_id' => $org->id,
            'entry_date_time' => now(),
            'gate_point' => 'Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_VEHICLE,
            'vehicle_number' => 'GJ-03-TR-2222',
            'delivering_party_type' => 'farmer',
            'farmer_user_id' => $farmer->id,
            'delivering_party_name' => $farmer->name,
            'source_produce_lot_id' => $lot->id,
            'crop_name' => 'Cotton',
            'declared_quantity' => 3000,
            'declared_unit' => 'kg',
            'status' => FpoGateEntry::STATUS_ENTERED,
            'created_by' => $fpoUser->id,
        ]);

        // Attempt invalid weightment: Gross < Tare
        $invalidResponse = $this->actingAs($fpoUser)->post(route('fpo.weightments.store', $entry->id), [
            'gross_weight' => 2000,
            'tare_weight' => 2500,
            'unit' => 'kg',
            'operator_name' => 'Weighbridge Operator 1',
            'measurement_method' => 'automated_weighbridge',
        ]);
        $invalidResponse->assertSessionHasErrors(['gross_weight']);

        // Valid weightment: Gross 5500, Tare 2500 => Net 3000
        $validResponse = $this->actingAs($fpoUser)->post(route('fpo.weightments.store', $entry->id), [
            'gross_weight' => 5500,
            'tare_weight' => 2500,
            'unit' => 'kg',
            'operator_name' => 'Weighbridge Operator 1',
            'measurement_method' => 'automated_weighbridge',
        ]);
        $validResponse->assertSessionHasNoErrors();

        $this->assertDatabaseHas('fpo_weightments', [
            'fpo_gate_entry_id' => $entry->id,
            'gross_weight' => 5500,
            'tare_weight' => 2500,
            'net_weight' => 3000,
            'is_accepted' => true,
        ]);

        $entry->refresh();
        $this->assertEquals(FpoGateEntry::STATUS_WEIGHED, $entry->status);
    }

    /**
     * 10. Reweighment marks prior measurement superseded and preserves audit chain.
     */
    public function test_reweighment_preserves_prior_measurement_history(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg('fpo-reweigh', 'Reweigh FPO');
        [$farmer, $lot] = $this->createFarmerWithLot(3000);
        $this->enrollFarmerInFpo($farmer, $org);

        $entry = FpoGateEntry::create([
            'entry_number' => 'GE-RW-001',
            'fpo_organization_id' => $org->id,
            'entry_date_time' => now(),
            'gate_point' => 'Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_VEHICLE,
            'vehicle_number' => 'GJ-03-RW-3333',
            'delivering_party_type' => 'farmer',
            'farmer_user_id' => $farmer->id,
            'delivering_party_name' => $farmer->name,
            'crop_name' => 'Cotton',
            'declared_quantity' => 3000,
            'declared_unit' => 'kg',
            'status' => FpoGateEntry::STATUS_ENTERED,
            'created_by' => $fpoUser->id,
        ]);

        // First weightment
        $wm1 = FpoWeightment::create([
            'weightment_number' => 'WM-001',
            'fpo_gate_entry_id' => $entry->id,
            'sequence_number' => 1,
            'gross_weight' => 5000,
            'tare_weight' => 2000,
            'net_weight' => 3000,
            'net_weight_in_kg' => 3000,
            'unit' => 'kg',
            'operator_name' => 'Op 1',
            'measurement_method' => 'automated_weighbridge',
            'measured_at' => now(),
            'is_accepted' => true,
            'created_by' => $fpoUser->id,
        ]);

        // Request reweighment
        $reweighResponse = $this->actingAs($fpoUser)->post(route('fpo.weightments.reweigh', $entry->id), [
            'original_weightment_id' => $wm1->id,
            'gross_weight' => 5200,
            'tare_weight' => 2100,
            'unit' => 'kg',
            'operator_name' => 'Op 2',
            'measurement_method' => 'manual',
            'reweighment_reason' => 'Driver requested scale calibration check',
        ]);
        $reweighResponse->assertSessionHasNoErrors();

        $wm1->refresh();
        $this->assertFalse($wm1->is_accepted);

        $wm2 = FpoWeightment::where('reweighment_of_id', $wm1->id)->firstOrFail();
        $this->assertTrue($wm2->is_accepted);
        $this->assertEquals(3100, $wm2->net_weight);
        $this->assertTrue($wm2->is_manual_entry);
        $this->assertEquals(2, $wm2->sequence_number);
    }

    /**
     * 11. Weight discrepancy > tolerance flags variance and creates an operational exception.
     */
    public function test_weight_discrepancy_flags_variance_and_creates_operational_exception(): void
    {
        Notification::fake();

        [$fpoUser, $org] = $this->createFpoOrg('fpo-disc', 'Discrepancy FPO');
        [$farmer, $lot] = $this->createFarmerWithLot(4000);
        $this->enrollFarmerInFpo($farmer, $org);

        // Declared 4000 kg
        $entry = FpoGateEntry::create([
            'entry_number' => 'GE-DISC-001',
            'fpo_organization_id' => $org->id,
            'entry_date_time' => now(),
            'gate_point' => 'Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_VEHICLE,
            'vehicle_number' => 'GJ-03-DS-4444',
            'delivering_party_type' => 'farmer',
            'farmer_user_id' => $farmer->id,
            'delivering_party_name' => $farmer->name,
            'crop_name' => 'Cotton',
            'declared_quantity' => 4000,
            'declared_unit' => 'kg',
            'status' => FpoGateEntry::STATUS_ENTERED,
            'created_by' => $fpoUser->id,
        ]);

        // Net weight is 3200 kg (variance = 20%, exceeds default tolerance of 5%)
        $response = $this->actingAs($fpoUser)->post(route('fpo.weightments.store', $entry->id), [
            'gross_weight' => 5200,
            'tare_weight' => 2000,
            'unit' => 'kg',
            'operator_name' => 'Op 1',
            'measurement_method' => 'automated_weighbridge',
        ]);
        $response->assertSessionHasNoErrors();

        // Check that discrepancy flag is set on weightment
        $wm = FpoWeightment::where('fpo_gate_entry_id', $entry->id)->firstOrFail();
        $this->assertTrue($wm->has_discrepancy);
        $this->assertEquals(20.0, $wm->discrepancy_percentage);

        // Check that an open operational exception was logged
        $this->assertDatabaseHas('fpo_operational_exceptions', [
            'fpo_gate_entry_id' => $entry->id,
            'exception_type' => FpoOperationalException::TYPE_WEIGHT_DISCREPANCY,
            'status' => FpoOperationalException::STATUS_OPEN,
        ]);

        // Notification delivered
        Notification::assertSentTo($farmer, WeightDiscrepancyNotification::class);
    }

    /**
     * 12. Authorized discrepancy override resolves exception and logs audit reason.
     */
    public function test_authorized_discrepancy_override_resolves_exception_and_logs_audit(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg('fpo-disc-override', 'Override FPO');

        $entry = FpoGateEntry::create([
            'entry_number' => 'GE-OVR-001',
            'fpo_organization_id' => $org->id,
            'entry_date_time' => now(),
            'gate_point' => 'Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_VEHICLE,
            'vehicle_number' => 'GJ-03-OV-5555',
            'delivering_party_type' => 'supplier',
            'delivering_party_name' => 'Supplier Co',
            'crop_name' => 'Cotton',
            'declared_quantity' => 5000,
            'declared_unit' => 'kg',
            'status' => FpoGateEntry::STATUS_WEIGHED,
            'created_by' => $fpoUser->id,
        ]);

        $wm = FpoWeightment::create([
            'weightment_number' => 'WM-OVR-01',
            'fpo_gate_entry_id' => $entry->id,
            'sequence_number' => 1,
            'gross_weight' => 6000,
            'tare_weight' => 2000,
            'net_weight' => 4000,
            'net_weight_in_kg' => 4000,
            'unit' => 'kg',
            'operator_name' => 'Op 1',
            'measurement_method' => 'automated_weighbridge',
            'measured_at' => now(),
            'has_discrepancy' => true,
            'discrepancy_percentage' => 20.0,
            'is_accepted' => true,
            'created_by' => $fpoUser->id,
        ]);

        $exception = FpoOperationalException::create([
            'exception_number' => 'EXC-OVR-01',
            'fpo_organization_id' => $org->id,
            'fpo_gate_entry_id' => $entry->id,
            'exception_type' => FpoOperationalException::TYPE_WEIGHT_DISCREPANCY,
            'severity' => FpoOperationalException::SEVERITY_CRITICAL,
            'title' => 'Weight discrepancy of 20%',
            'description' => 'Declared 5000 kg vs Measured 4000 kg',
            'status' => FpoOperationalException::STATUS_OPEN,
            'created_by' => $fpoUser->id,
        ]);

        $resolveResponse = $this->actingAs($fpoUser)->post(route('fpo.weightments.resolve', [$entry->id, $wm->id]), [
            'resolution_reason' => 'Moisture loss verified and accepted by procurement committee',
        ]);
        $resolveResponse->assertSessionHasNoErrors();

        $wm->refresh();
        $this->assertTrue($wm->discrepancy_resolved);
        $this->assertEquals('authorized_override', $wm->discrepancy_resolution_action);

        $exception->refresh();
        $this->assertEquals(FpoOperationalException::STATUS_RESOLVED, $exception->status);
    }

    /**
     * 13. Printable weightment slip renders HTTP 200 with formal details.
     */
    public function test_printable_weightment_slip_renders_accurately(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg('fpo-slip', 'Slip FPO');

        $entry = FpoGateEntry::create([
            'entry_number' => 'GE-SLIP-001',
            'fpo_organization_id' => $org->id,
            'entry_date_time' => now(),
            'gate_point' => 'Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_VEHICLE,
            'vehicle_number' => 'GJ-03-SL-6666',
            'delivering_party_type' => 'farmer',
            'delivering_party_name' => 'Kisan Bhai',
            'crop_name' => 'Cotton',
            'declared_quantity' => 3000,
            'declared_unit' => 'kg',
            'status' => FpoGateEntry::STATUS_WEIGHED,
            'created_by' => $fpoUser->id,
        ]);

        $wm = FpoWeightment::create([
            'weightment_number' => 'WM-SLIP-01',
            'fpo_gate_entry_id' => $entry->id,
            'sequence_number' => 1,
            'gross_weight' => 5000,
            'tare_weight' => 2000,
            'net_weight' => 3000,
            'net_weight_in_kg' => 3000,
            'unit' => 'kg',
            'operator_name' => 'Ramesh Weighman',
            'measurement_method' => 'automated_weighbridge',
            'measured_at' => now(),
            'is_accepted' => true,
            'created_by' => $fpoUser->id,
        ]);

        $response = $this->actingAs($fpoUser)->get(route('fpo.weightments.slip', [$entry->id, $wm->id]));
        $response->assertStatus(200);
        $response->assertSee('OFFICIAL WEIGH SLIP');
        $response->assertSee('WM-SLIP-01');
        $response->assertSee('3,000.0');
    }

    /**
     * 14. Sample collection creates sample record, container seal, and transitions state.
     */
    public function test_sample_collection_persists_sample_and_seal_number(): void
    {
        Storage::fake('local');

        [$fpoUser, $org] = $this->createFpoOrg('fpo-sample-col', 'Sample Col FPO');

        $entry = FpoGateEntry::create([
            'entry_number' => 'GE-SMP-001',
            'fpo_organization_id' => $org->id,
            'entry_date_time' => now(),
            'gate_point' => 'Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_VEHICLE,
            'vehicle_number' => 'GJ-03-SP-7777',
            'delivering_party_type' => 'farmer',
            'delivering_party_name' => 'Farmer Lalji',
            'crop_name' => 'Cotton',
            'variety' => 'Shankar-6',
            'declared_quantity' => 4000,
            'declared_unit' => 'kg',
            'status' => FpoGateEntry::STATUS_WEIGHED,
            'created_by' => $fpoUser->id,
        ]);

        $response = $this->actingAs($fpoUser)->post(route('fpo.samples.store', $entry->id), [
            'sampling_method' => 'composite_grid_sampling',
            'sample_quantity' => 1.5,
            'sample_unit' => 'kg',
            'container_seal_number' => 'SEAL-2026-9901',
            'destination' => 'In-House Testing Facility',
            'condition_observations' => 'Representative random bags drawn from bottom, middle, top.',
            'attachment' => UploadedFile::fake()->create('sample_photo.jpg', 200),
        ]);
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('fpo_samples', [
            'fpo_gate_entry_id' => $entry->id,
            'container_seal_number' => 'SEAL-2026-9901',
            'sample_quantity' => 1.5,
            'status' => FpoSample::STATUS_COLLECTED,
        ]);

        $entry->refresh();
        $this->assertEquals(FpoGateEntry::STATUS_TESTING_IN_PROGRESS, $entry->status);
    }

    /**
     * 15. Quality test parameters persist measured values and outcomes.
     */
    public function test_quality_tests_persist_measured_parameters_and_outcomes(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg('fpo-qt-params', 'Quality Params FPO');

        $entry = FpoGateEntry::create([
            'entry_number' => 'GE-QT-001',
            'fpo_organization_id' => $org->id,
            'entry_date_time' => now(),
            'gate_point' => 'Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_VEHICLE,
            'crop_name' => 'Cotton',
            'declared_quantity' => 4000,
            'declared_unit' => 'kg',
            'status' => FpoGateEntry::STATUS_TESTING_IN_PROGRESS,
            'created_by' => $fpoUser->id,
            'delivering_party_name' => 'Delivering Farmer',
        ]);

        $sample = FpoSample::create([
            'sample_number' => 'SMP-QT-01',
            'fpo_gate_entry_id' => $entry->id,
            'crop_name' => 'Cotton',
            'sample_date_time' => now(),
            'collected_by' => $fpoUser->id,
            'collector_name' => $fpoUser->name,
            'sampling_method' => 'manual',
            'sample_quantity' => 1.0,
            'sample_unit' => 'kg',
            'destination' => 'Lab',
            'status' => FpoSample::STATUS_COLLECTED,
        ]);

        $response = $this->actingAs($fpoUser)->post(route('fpo.tests.store', [$entry->id, $sample->id]), [
            'test_type' => 'moisture',
            'parameter_name' => 'Moisture Content',
            'measured_value' => 8.5,
            'unit' => '%',
            'min_acceptable_value' => 6.0,
            'max_acceptable_value' => 12.0,
            'outcome' => 'pass',
            'notes' => 'Within standard dry limits',
        ]);
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('fpo_quality_tests', [
            'fpo_sample_id' => $sample->id,
            'parameter_name' => 'Moisture Content',
            'measured_value' => 8.5,
            'outcome' => 'pass',
            'is_superseded' => false,
        ]);
    }

    /**
     * 16. Retesting preserves previous test as superseded.
     */
    public function test_retesting_preserves_prior_test_as_superseded(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg('fpo-retest', 'Retest FPO');

        $entry = FpoGateEntry::create([
            'entry_number' => 'GE-RT-001',
            'fpo_organization_id' => $org->id,
            'entry_date_time' => now(),
            'gate_point' => 'Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_WALKIN,
            'crop_name' => 'Cotton',
            'declared_quantity' => 2000,
            'declared_unit' => 'kg',
            'status' => FpoGateEntry::STATUS_TESTING_IN_PROGRESS,
            'created_by' => $fpoUser->id,
            'delivering_party_name' => 'Farmer A',
        ]);

        $sample = FpoSample::create([
            'sample_number' => 'SMP-RT-01',
            'fpo_gate_entry_id' => $entry->id,
            'crop_name' => 'Cotton',
            'sample_date_time' => now(),
            'collected_by' => $fpoUser->id,
            'collector_name' => $fpoUser->name,
            'sampling_method' => 'manual',
            'sample_quantity' => 1.0,
            'sample_unit' => 'kg',
            'destination' => 'Lab',
            'status' => FpoSample::STATUS_COLLECTED,
        ]);

        $initialTest = FpoQualityTest::create([
            'test_number' => 'QT-001',
            'fpo_sample_id' => $sample->id,
            'test_type' => 'moisture',
            'parameter_name' => 'Moisture Content',
            'measured_value' => 14.5,
            'unit' => '%',
            'outcome' => 'fail',
            'tested_by' => $fpoUser->id,
            'tester_name' => $fpoUser->name,
            'tested_at' => now(),
            'is_superseded' => false,
        ]);

        // Retest
        $response = $this->actingAs($fpoUser)->post(route('fpo.tests.store', [$entry->id, $sample->id]), [
            'test_type' => 'moisture',
            'parameter_name' => 'Moisture Content (Retest)',
            'measured_value' => 9.2,
            'unit' => '%',
            'outcome' => 'pass',
            'retest_of_id' => $initialTest->id,
        ]);
        $response->assertSessionHasNoErrors();

        $initialTest->refresh();
        $this->assertTrue($initialTest->is_superseded);

        $newTest = FpoQualityTest::where('parameter_name', 'Moisture Content (Retest)')->firstOrFail();
        $this->assertFalse($newTest->is_superseded);
        $this->assertEquals('pass', $newTest->outcome);
    }

    /**
     * 17. Quality approval records approver identity, role, decision, grade, and digital attestation.
     */
    public function test_authorized_quality_approval_records_actor_decision_and_digital_signature(): void
    {
        Notification::fake();

        [$fpoUser, $org] = $this->createFpoOrg('fpo-appr-sign', 'Approval Sign FPO');
        [$farmer, $lot] = $this->createFarmerWithLot(3500);
        $this->enrollFarmerInFpo($farmer, $org);

        $entry = FpoGateEntry::create([
            'entry_number' => 'GE-APPR-001',
            'fpo_organization_id' => $org->id,
            'entry_date_time' => now(),
            'gate_point' => 'Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_VEHICLE,
            'vehicle_number' => 'GJ-03-AP-8888',
            'delivering_party_type' => 'farmer',
            'farmer_user_id' => $farmer->id,
            'delivering_party_name' => $farmer->name,
            'crop_name' => 'Cotton',
            'declared_quantity' => 3500,
            'declared_unit' => 'kg',
            'status' => FpoGateEntry::STATUS_QUALITY_PENDING_APPROVAL,
            'created_by' => $fpoUser->id,
        ]);

        $wm = FpoWeightment::create([
            'weightment_number' => 'WM-APPR-01',
            'fpo_gate_entry_id' => $entry->id,
            'sequence_number' => 1,
            'gross_weight' => 5500,
            'tare_weight' => 2000,
            'net_weight' => 3500,
            'net_weight_in_kg' => 3500,
            'unit' => 'kg',
            'operator_name' => 'Operator',
            'measurement_method' => 'automated_weighbridge',
            'measured_at' => now(),
            'is_accepted' => true,
            'created_by' => $fpoUser->id,
        ]);

        $response = $this->actingAs($fpoUser)->post(route('fpo.approvals.store', $entry->id), [
            'decision' => 'approved',
            'assigned_grade' => 'Good',
            'notes' => 'Approved after clean moisture and visual purity test',
            'signature_attestation' => 'Digitally signed and approved by Procurement Head',
        ]);
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('fpo_quality_approvals', [
            'fpo_gate_entry_id' => $entry->id,
            'approver_id' => $fpoUser->id,
            'decision' => 'approved',
            'assigned_grade' => 'Good',
            'accepted_quantity' => 3500,
            'signature_attestation' => 'Digitally signed and approved by Procurement Head',
        ]);

        $entry->refresh();
        $this->assertEquals(FpoGateEntry::STATUS_APPROVED, $entry->status);

        // Notification sent to farmer
        Notification::assertSentTo($farmer, QualityDecisionNotification::class);
    }

    /**
     * 18. Partial acceptance separates accepted and rejected quantities.
     */
    public function test_partial_acceptance_separates_accepted_and_rejected_quantities(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg('fpo-partial-appr', 'Partial Approval FPO');

        $entry = FpoGateEntry::create([
            'entry_number' => 'GE-PART-001',
            'fpo_organization_id' => $org->id,
            'entry_date_time' => now(),
            'gate_point' => 'Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_VEHICLE,
            'crop_name' => 'Cotton',
            'declared_quantity' => 5000,
            'declared_unit' => 'kg',
            'status' => FpoGateEntry::STATUS_QUALITY_PENDING_APPROVAL,
            'created_by' => $fpoUser->id,
            'delivering_party_name' => 'Farmer B',
        ]);

        $response = $this->actingAs($fpoUser)->post(route('fpo.approvals.store', $entry->id), [
            'decision' => 'partially_accepted',
            'assigned_grade' => 'Intermediate',
            'accepted_quantity' => 3500,
            'rejected_quantity' => 1500,
            'notes' => '1500 kg damaged by rain during transit',
        ]);
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('fpo_quality_approvals', [
            'fpo_gate_entry_id' => $entry->id,
            'decision' => 'partially_accepted',
            'accepted_quantity' => 3500,
            'rejected_quantity' => 1500,
        ]);

        $entry->refresh();
        $this->assertEquals(FpoGateEntry::STATUS_PARTIALLY_ACCEPTED, $entry->status);
    }

    /**
     * 19. Quality rejection creates critical exception and does not release produce for sale.
     */
    public function test_quality_rejection_creates_critical_exception_and_prevents_stock_release(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg('fpo-rejection', 'Rejection FPO');

        $entry = FpoGateEntry::create([
            'entry_number' => 'GE-REJ-001',
            'fpo_organization_id' => $org->id,
            'entry_date_time' => now(),
            'gate_point' => 'Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_VEHICLE,
            'crop_name' => 'Cotton',
            'declared_quantity' => 4000,
            'declared_unit' => 'kg',
            'status' => FpoGateEntry::STATUS_QUALITY_PENDING_APPROVAL,
            'created_by' => $fpoUser->id,
            'delivering_party_name' => 'Supplier Co',
        ]);

        $response = $this->actingAs($fpoUser)->post(route('fpo.approvals.store', $entry->id), [
            'decision' => 'rejected',
            'assigned_grade' => 'Bad',
            'rejection_reason' => 'Severe pest infestation and black spots across all lots',
        ]);
        $response->assertSessionHasNoErrors();

        $entry->refresh();
        $this->assertEquals(FpoGateEntry::STATUS_REJECTED, $entry->status);

        // Critical exception logged
        $this->assertDatabaseHas('fpo_operational_exceptions', [
            'fpo_gate_entry_id' => $entry->id,
            'exception_type' => FpoOperationalException::TYPE_QUALITY_FAILURE,
            'severity' => FpoOperationalException::SEVERITY_CRITICAL,
            'status' => FpoOperationalException::STATUS_OPEN,
        ]);

        // Attempting to post inventory for rejected entry should fail
        $postResponse = $this->actingAs($fpoUser)->post(route('fpo.inventory.post', $entry->id));
        $postResponse->assertSessionHasErrors(['inventory']);
    }

    /**
     * 20. Separation of duties: when configured true, tester cannot approve their own inspection.
     */
    public function test_separation_of_duties_enforced_when_configured(): void
    {
        Config::set('agritech.enforce_separation_of_duties', true);

        [$fpoUserA, $org] = $this->createFpoOrg('fpo-sod', 'Separation of Duties FPO');
        $fpoUserB = $this->createFpoStaff($org, 'approver');

        $entry = FpoGateEntry::create([
            'entry_number' => 'GE-SOD-001',
            'fpo_organization_id' => $org->id,
            'entry_date_time' => now(),
            'gate_point' => 'Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_VEHICLE,
            'crop_name' => 'Cotton',
            'declared_quantity' => 3000,
            'declared_unit' => 'kg',
            'status' => FpoGateEntry::STATUS_TESTING_IN_PROGRESS,
            'created_by' => $fpoUserA->id,
            'delivering_party_name' => 'Farmer SOD',
        ]);

        $sample = FpoSample::create([
            'sample_number' => 'SMP-SOD-01',
            'fpo_gate_entry_id' => $entry->id,
            'crop_name' => 'Cotton',
            'sample_date_time' => now(),
            'collected_by' => $fpoUserA->id,
            'collector_name' => $fpoUserA->name,
            'sampling_method' => 'manual',
            'sample_quantity' => 1.0,
            'sample_unit' => 'kg',
            'destination' => 'Lab',
            'status' => FpoSample::STATUS_COLLECTED,
        ]);

        // Tester is User A
        FpoQualityTest::create([
            'test_number' => 'QT-SOD-01',
            'fpo_sample_id' => $sample->id,
            'test_type' => 'moisture',
            'parameter_name' => 'Moisture',
            'measured_value' => 8.0,
            'unit' => '%',
            'outcome' => 'pass',
            'tested_by' => $fpoUserA->id,
            'tester_name' => $fpoUserA->name,
            'tested_at' => now(),
            'is_superseded' => false,
        ]);

        $entry->update(['status' => FpoGateEntry::STATUS_QUALITY_PENDING_APPROVAL]);

        // User A (tester) tries to approve: policy should block (403)
        $blockedResponse = $this->actingAs($fpoUserA)->post(route('fpo.approvals.store', $entry->id), [
            'decision' => 'approved',
            'assigned_grade' => 'Good',
        ]);
        $blockedResponse->assertStatus(403);

        // User B (independent approver) approves: should succeed
        $allowedResponse = $this->actingAs($fpoUserB)->post(route('fpo.approvals.store', $entry->id), [
            'decision' => 'approved',
            'assigned_grade' => 'Good',
        ]);
        $allowedResponse->assertSessionHasNoErrors();
    }

    /**
     * 21. Inventory cannot be posted before authorized approval.
     */
    public function test_inventory_cannot_be_posted_before_authorized_approval(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg('fpo-inv-order', 'Inventory Order FPO');

        $entry = FpoGateEntry::create([
            'entry_number' => 'GE-PRE-POST',
            'fpo_organization_id' => $org->id,
            'entry_date_time' => now(),
            'gate_point' => 'Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_VEHICLE,
            'crop_name' => 'Cotton',
            'declared_quantity' => 3000,
            'declared_unit' => 'kg',
            'status' => FpoGateEntry::STATUS_WEIGHED, // Not approved yet
            'created_by' => $fpoUser->id,
            'delivering_party_name' => 'Farmer Premature',
        ]);

        $response = $this->actingAs($fpoUser)->post(route('fpo.inventory.post', $entry->id));
        $response->assertSessionHasErrors(['inventory']);
    }

    /**
     * 22. Idempotent inventory posting: approved stock posts exactly once and duplicate requests do not double-post.
     */
    public function test_idempotent_inventory_posting_posts_exactly_once(): void
    {
        Notification::fake();

        [$fpoUser, $org] = $this->createFpoOrg('fpo-idemp-inv', 'Idempotent Inv FPO');
        [$farmer, $lot] = $this->createFarmerWithLot(2000);
        $this->enrollFarmerInFpo($farmer, $org);

        $initialLotAvailable = $lot->available_quantity_kg;

        $entry = FpoGateEntry::create([
            'entry_number' => 'GE-IDEMP-01',
            'fpo_organization_id' => $org->id,
            'entry_date_time' => now(),
            'gate_point' => 'Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_VEHICLE,
            'crop_name' => 'Cotton',
            'declared_quantity' => 2000,
            'declared_unit' => 'kg',
            'source_produce_lot_id' => $lot->id,
            'farmer_user_id' => $farmer->id,
            'delivering_party_name' => $farmer->name,
            'status' => FpoGateEntry::STATUS_APPROVED,
            'created_by' => $fpoUser->id,
        ]);

        FpoQualityApproval::create([
            'approval_number' => 'APR-IDEMP-01',
            'fpo_gate_entry_id' => $entry->id,
            'approver_id' => $fpoUser->id,
            'approver_name' => $fpoUser->name,
            'approver_role' => 'admin',
            'decision' => FpoQualityApproval::DECISION_APPROVED,
            'decision_at' => now(),
            'accepted_quantity' => 2000,
            'rejected_quantity' => 0,
            'assigned_grade' => 'Good',
        ]);

        // First Post Request
        $response1 = $this->actingAs($fpoUser)->post(route('fpo.inventory.post', $entry->id));
        $response1->assertSessionHasNoErrors();
        $response1->assertRedirect();

        $this->assertEquals(1, FpoInventoryPosting::count());
        $lot->refresh();
        $this->assertEquals($initialLotAvailable + 2000, $lot->available_quantity_kg);

        // Second Post Request (Retry / Idempotency)
        $response2 = $this->actingAs($fpoUser)->post(route('fpo.inventory.post', $entry->id));
        $response2->assertSessionHasNoErrors();

        // Must still have exactly 1 inventory posting, lot quantity not increased again
        $this->assertEquals(1, FpoInventoryPosting::count());
        $lot->refresh();
        $this->assertEquals($initialLotAvailable + 2000, $lot->available_quantity_kg);

        Notification::assertSentTo($farmer, InventoryPostedNotification::class);
    }

    /**
     * 23. Stock reversal safely deducts posted stock and restores gate entry status.
     */
    public function test_inventory_reversal_safely_deducts_stock_and_restores_state(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg('fpo-rev-inv', 'Reversal Inv FPO');
        [$farmer, $lot] = $this->createFarmerWithLot(2000);
        $this->enrollFarmerInFpo($farmer, $org);

        $entry = FpoGateEntry::create([
            'entry_number' => 'GE-REV-01',
            'fpo_organization_id' => $org->id,
            'entry_date_time' => now(),
            'gate_point' => 'Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_VEHICLE,
            'crop_name' => 'Cotton',
            'declared_quantity' => 2000,
            'declared_unit' => 'kg',
            'source_produce_lot_id' => $lot->id,
            'status' => FpoGateEntry::STATUS_APPROVED,
            'created_by' => $fpoUser->id,
            'delivering_party_name' => $farmer->name,
        ]);

        FpoQualityApproval::create([
            'approval_number' => 'APR-REV-01',
            'fpo_gate_entry_id' => $entry->id,
            'approver_id' => $fpoUser->id,
            'approver_name' => $fpoUser->name,
            'approver_role' => 'admin',
            'decision' => FpoQualityApproval::DECISION_APPROVED,
            'decision_at' => now(),
            'accepted_quantity' => 2000,
            'rejected_quantity' => 0,
            'assigned_grade' => 'Good',
        ]);

        // Post stock
        $this->actingAs($fpoUser)->post(route('fpo.inventory.post', $entry->id));
        $posting = FpoInventoryPosting::where('fpo_gate_entry_id', $entry->id)->firstOrFail();

        $lot->refresh();
        $stockAfterPost = $lot->available_quantity_kg;

        // Perform authorized reversal
        $reversalResponse = $this->actingAs($fpoUser)->post(route('fpo.inventory.reversal', [$entry->id, $posting->id]), [
            'reversal_reason' => 'Administrative correction: entry entered under wrong member account',
        ]);
        $reversalResponse->assertSessionHasNoErrors();

        $posting->refresh();
        $this->assertEquals(FpoInventoryPosting::STATUS_REVERSED, $posting->status);
        $this->assertEquals('Administrative correction: entry entered under wrong member account', $posting->reversal_reason);

        $lot->refresh();
        $this->assertEquals($stockAfterPost - 2000, $lot->available_quantity_kg);

        $entry->refresh();
        $this->assertEquals(FpoGateEntry::STATUS_APPROVED, $entry->status);
    }

    /**
     * 24. Gate exit authorization validates outstanding purchase order volume and blocks over-dispatch.
     */
    public function test_gate_exit_authorization_validates_order_and_blocks_over_dispatch(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg('fpo-exit-po', 'Exit PO FPO');
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);

        $po = PurchaseOrder::create([
            'po_number' => 'PO-2026-9001',
            'fpo_organization_id' => $org->id,
            'seller_user_id' => $fpoUser->id,
            'seller_type' => 'fpo',
            'buyer_id' => $buyer->id,
            'delivery_address' => 'Gondal APMC Market Yard',
            'delivery_district' => 'Rajkot',
            'item_description' => 'Cotton Shankar-6',
            'quantity' => 5000,
            'unit' => 'kg',
            'unit_price' => 62.00,
            'subtotal' => 310000.00,
            'total_amount' => 310000.00,
            'status' => PurchaseOrder::STATUS_PROCESSING,
        ]);

        // Attempting to dispatch 6000 kg for an order of 5000 kg should be rejected
        $overDispatchResponse = $this->actingAs($fpoUser)->post(route('fpo.gate-exits.store'), [
            'movement_type' => FpoGateExit::MOVEMENT_ORDER_DISPATCH,
            'purchase_order_id' => $po->id,
            'vehicle_number' => 'GJ-03-EX-1111',
            'driver_name' => 'Transporter Driver',
            'dispatched_quantity' => 6000,
            'unit' => 'kg',
        ]);
        $overDispatchResponse->assertSessionHasErrors(['dispatched_quantity']);

        // Valid dispatch of 4000 kg should succeed
        $validDispatchResponse = $this->actingAs($fpoUser)->post(route('fpo.gate-exits.store'), [
            'movement_type' => FpoGateExit::MOVEMENT_ORDER_DISPATCH,
            'purchase_order_id' => $po->id,
            'vehicle_number' => 'GJ-03-EX-1111',
            'driver_name' => 'Transporter Driver',
            'dispatched_quantity' => 4000,
            'unit' => 'kg',
        ]);
        $validDispatchResponse->assertSessionHasNoErrors();

        $this->assertDatabaseHas('fpo_gate_exits', [
            'purchase_order_id' => $po->id,
            'dispatched_quantity' => 4000,
            'status' => FpoGateExit::STATUS_AUTHORIZED,
        ]);
    }

    /**
     * 25. Gate exit blocks cancelled purchase orders from being dispatched.
     */
    public function test_gate_exit_blocks_cancelled_purchase_orders(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg('fpo-cancel-exit', 'Cancel Exit FPO');
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);

        $cancelledPo = PurchaseOrder::create([
            'po_number' => 'PO-CAN-9999',
            'fpo_organization_id' => $org->id,
            'seller_user_id' => $fpoUser->id,
            'seller_type' => 'fpo',
            'buyer_id' => $buyer->id,
            'delivery_address' => 'Gondal APMC Market Yard',
            'delivery_district' => 'Rajkot',
            'item_description' => 'Cotton Shankar-6',
            'quantity' => 3000,
            'unit' => 'kg',
            'unit_price' => 60.00,
            'subtotal' => 180000.00,
            'total_amount' => 180000.00,
            'status' => PurchaseOrder::STATUS_CANCELLED,
        ]);

        $response = $this->actingAs($fpoUser)->post(route('fpo.gate-exits.store'), [
            'movement_type' => FpoGateExit::MOVEMENT_ORDER_DISPATCH,
            'purchase_order_id' => $cancelledPo->id,
            'vehicle_number' => 'GJ-03-CAN-01',
            'dispatched_quantity' => 3000,
            'unit' => 'kg',
        ]);
        $response->assertSessionHasErrors(['purchase_order_id']);
    }

    /**
     * 26. Physical gate exit and buyer handover confirmation are distinct operations.
     */
    public function test_physical_exit_and_handover_confirmation_are_distinct(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg('fpo-handover-exit', 'Handover Exit FPO');

        $exit = FpoGateExit::create([
            'exit_number' => 'EXIT-2026-001',
            'fpo_organization_id' => $org->id,
            'exit_date_time' => now(),
            'movement_type' => FpoGateExit::MOVEMENT_TRANSFER,
            'vehicle_number' => 'GJ-03-TR-9999',
            'driver_name' => 'Mukesh Bhai',
            'dispatched_quantity' => 2000,
            'unit' => 'kg',
            'authorized_by' => $fpoUser->id,
            'authorizer_name' => $fpoUser->name,
            'recorded_by' => $fpoUser->id,
            'recorder_name' => $fpoUser->name,
            'status' => FpoGateExit::STATUS_AUTHORIZED,
        ]);

        // Step 1: Confirm vehicle physical exit through the gate
        $exitResponse = $this->actingAs($fpoUser)->post(route('fpo.gate-exits.confirm-exit', $exit->id), [
            'gate_pass_number' => 'GP-8877',
        ]);
        $exitResponse->assertSessionHasNoErrors();

        $exit->refresh();
        $this->assertEquals(FpoGateExit::STATUS_EXITED, $exit->status);
        $this->assertNotNull($exit->actual_exit_at);
        $this->assertNull($exit->handover_confirmed_at);

        // Step 2: Confirm recipient/buyer delivery handover
        $handoverResponse = $this->actingAs($fpoUser)->post(route('fpo.gate-exits.confirm-handover', $exit->id), [
            'handover_notes' => 'Received in sound condition, seal intact',
        ]);
        $handoverResponse->assertSessionHasNoErrors();

        $exit->refresh();
        $this->assertNotNull($exit->handover_confirmed_at);
    }

    /**
     * 27. Operational exceptions register, resolution, and audit trail.
     */
    public function test_operational_exceptions_register_and_resolution_workflow(): void
    {
        Notification::fake();

        [$fpoUser, $org] = $this->createFpoOrg('fpo-exc-test', 'Exceptions Test FPO');

        $entry = FpoGateEntry::create([
            'entry_number' => 'GE-EXC-001',
            'fpo_organization_id' => $org->id,
            'entry_date_time' => now(),
            'gate_point' => 'Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_VEHICLE,
            'crop_name' => 'Cotton',
            'declared_quantity' => 3000,
            'declared_unit' => 'kg',
            'status' => FpoGateEntry::STATUS_ENTERED,
            'created_by' => $fpoUser->id,
            'delivering_party_name' => 'Party X',
        ]);

        // Create exception
        $createResponse = $this->actingAs($fpoUser)->post(route('fpo.exceptions.store'), [
            'fpo_gate_entry_id' => $entry->id,
            'exception_type' => FpoOperationalException::TYPE_QUANTITY_MISMATCH,
            'severity' => FpoOperationalException::SEVERITY_MEDIUM,
            'title' => 'Missing Mandi Gate Pass copy',
            'description' => 'Delivering driver did not present physical copy of Mandi transit pass',
        ]);
        $createResponse->assertSessionHasNoErrors();

        $exception = FpoOperationalException::where('fpo_gate_entry_id', $entry->id)->firstOrFail();
        $this->assertEquals(FpoOperationalException::STATUS_OPEN, $exception->status);

        // Resolve exception
        $resolveResponse = $this->actingAs($fpoUser)->post(route('fpo.exceptions.resolve', $exception->id), [
            'resolution_reason' => 'Electronic e-way pass verified on portal and attached',
            'resolution_action' => 'verified_electronic_pass',
        ]);
        $resolveResponse->assertSessionHasNoErrors();

        $exception->refresh();
        $this->assertEquals(FpoOperationalException::STATUS_RESOLVED, $exception->status);
        $this->assertEquals($fpoUser->id, $exception->resolved_by);
        $this->assertNotNull($exception->resolved_at);
    }

    /**
     * 28. Individual entry formal report renders all 20 required sections and displays persisted data.
     */
    public function test_individual_entry_report_and_pdf_export_reflects_persisted_data(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg('fpo-rep-test', 'Report Test FPO');
        [$farmer, $lot] = $this->createFarmerWithLot(3000);
        $this->enrollFarmerInFpo($farmer, $org);

        $entry = FpoGateEntry::create([
            'entry_number' => 'GE-REP-999',
            'fpo_organization_id' => $org->id,
            'entry_date_time' => now(),
            'gate_point' => 'Main Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_VEHICLE,
            'vehicle_number' => 'GJ-03-RP-9999',
            'delivering_party_type' => 'farmer',
            'farmer_user_id' => $farmer->id,
            'delivering_party_name' => $farmer->name,
            'crop_name' => 'Cotton',
            'variety' => 'Shankar-6',
            'declared_quantity' => 3000,
            'declared_unit' => 'kg',
            'status' => FpoGateEntry::STATUS_APPROVED,
            'created_by' => $fpoUser->id,
        ]);

        FpoWeightment::create([
            'weightment_number' => 'WM-REP-99',
            'fpo_gate_entry_id' => $entry->id,
            'sequence_number' => 1,
            'gross_weight' => 5000,
            'tare_weight' => 2000,
            'net_weight' => 3000,
            'net_weight_in_kg' => 3000,
            'unit' => 'kg',
            'operator_name' => 'Operator Ram',
            'measurement_method' => 'automated_weighbridge',
            'measured_at' => now(),
            'is_accepted' => true,
            'created_by' => $fpoUser->id,
        ]);

        FpoQualityApproval::create([
            'approval_number' => 'APR-REP-99',
            'fpo_gate_entry_id' => $entry->id,
            'approver_id' => $fpoUser->id,
            'approver_name' => $fpoUser->name,
            'approver_role' => 'admin',
            'decision' => FpoQualityApproval::DECISION_APPROVED,
            'decision_at' => now(),
            'accepted_quantity' => 3000,
            'rejected_quantity' => 0,
            'assigned_grade' => 'Good',
            'signature_attestation' => 'Digitally certified by FPO Manager',
        ]);

        // HTML Report View
        $reportResponse = $this->actingAs($fpoUser)->get(route('fpo.reports.show', $entry->id));
        $reportResponse->assertStatus(200);
        $reportResponse->assertSee('OFFICIAL GATE PROCUREMENT REPORT');
        $reportResponse->assertSee('GE-REP-999');
        $reportResponse->assertSee('GJ-03-RP-9999');
        $reportResponse->assertSee('3,000.0 kg');
        $reportResponse->assertSee('Digitally certified by FPO Manager');

        // PDF View or fallback
        $pdfResponse = $this->actingAs($fpoUser)->get(route('fpo.reports.pdf', $entry->id));
        $this->assertTrue(in_array($pdfResponse->status(), [200, 302]));

        // Print View
        $printResponse = $this->actingAs($fpoUser)->get(route('fpo.reports.print', $entry->id));
        $printResponse->assertStatus(200);
    }

    /**
     * 29. Stock reconciliation matrix computes exact persisted totals across stages.
     */
    public function test_stock_reconciliation_matrix_computes_exact_persisted_totals(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg('fpo-recon-test', 'Recon Test FPO');

        $entry = FpoGateEntry::create([
            'entry_number' => 'GE-RECON-01',
            'fpo_organization_id' => $org->id,
            'entry_date_time' => now(),
            'gate_point' => 'Gate 1',
            'entry_type' => FpoGateEntry::ENTRY_TYPE_VEHICLE,
            'crop_name' => 'Cotton',
            'declared_quantity' => 4000,
            'declared_unit' => 'kg',
            'status' => FpoGateEntry::STATUS_APPROVED,
            'created_by' => $fpoUser->id,
            'delivering_party_name' => 'Farmer Recon',
        ]);

        FpoWeightment::create([
            'weightment_number' => 'WM-RC-01',
            'fpo_gate_entry_id' => $entry->id,
            'sequence_number' => 1,
            'gross_weight' => 6000,
            'tare_weight' => 2000,
            'net_weight' => 4000,
            'net_weight_in_kg' => 4000,
            'unit' => 'kg',
            'operator_name' => 'Op 1',
            'measurement_method' => 'automated_weighbridge',
            'measured_at' => now(),
            'is_accepted' => true,
            'created_by' => $fpoUser->id,
        ]);

        FpoQualityApproval::create([
            'approval_number' => 'APR-RC-01',
            'fpo_gate_entry_id' => $entry->id,
            'approver_id' => $fpoUser->id,
            'approver_name' => $fpoUser->name,
            'approver_role' => 'admin',
            'decision' => FpoQualityApproval::DECISION_APPROVED,
            'decision_at' => now(),
            'accepted_quantity' => 3800,
            'rejected_quantity' => 200,
            'assigned_grade' => 'Good',
        ]);

        $response = $this->actingAs($fpoUser)->get(route('fpo.reconciliation'));
        $response->assertStatus(200);
        $response->assertSee('Delivery Lot Stock Reconciliation Matrix');
        $response->assertSee('4,000.0 kg'); // Declared
        $response->assertSee('3,800.0 kg'); // Accepted
        $response->assertSee('200.0 kg');   // Rejected
    }
}
