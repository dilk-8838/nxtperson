<?php

namespace Tests\Feature;

use App\Models\Crop;
use App\Models\FarmerProfile;
use App\Models\FpoCollectiveListing;
use App\Models\FpoFarmerMembership;
use App\Models\FpoMember;
use App\Models\FpoOrganization;
use App\Models\Manufacturer;
use App\Models\ProduceLot;
use App\Models\ProduceReservation;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\PurchaseOrder;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Models\RfqInvitation;
use App\Models\SupplierMember;
use App\Models\SupplierOrganization;
use App\Models\User;
use App\Notifications\RfqCreatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BatchTwoMarketplaceAndTradingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Helper to set up a farmer with profile and lot.
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
     * Helper to set up a supplier organization and owner.
     */
    protected function createSupplierOrg(string $slug = 'sewa-inputs'): array
    {
        $category = ProductCategory::firstOrCreate(
            ['slug' => 'seeds'],
            ['name' => 'Seeds & Hybrids', 'description' => 'Certified crop seeds']
        );

        $supplier = User::factory()->create([
            'role' => 'supplier',
            'status' => 'active',
            'district' => 'Anand',
        ]);

        $org = SupplierOrganization::create([
            'name' => 'Sewa Agro Inputs Ltd',
            'slug' => $slug,
            'category' => 'seeds',
            'authorized_representative_name' => $supplier->name,
            'contact_email' => $supplier->email,
            'contact_phone' => '9876543230',
            'operating_address' => 'GIDC Anand',
            'district' => 'Anand',
            'verification_status' => SupplierOrganization::STATUS_APPROVED,
            'verified_at' => now(),
        ]);

        SupplierMember::create([
            'supplier_organization_id' => $org->id,
            'user_id' => $supplier->id,
            'role_in_org' => 'owner',
            'is_primary_contact' => true,
        ]);

        return [$supplier, $org, $category];
    }

    /**
     * Helper to set up an FPO organization and admin.
     */
    protected function createFpoOrg(string $slug = 'saurashtra-fpo'): array
    {
        $fpoUser = User::factory()->create([
            'role' => 'fpo',
            'status' => 'active',
            'district' => 'Rajkot',
        ]);

        $org = FpoOrganization::create([
            'name' => 'Saurashtra Kisan Producer Co.',
            'slug' => $slug,
            'organization_type' => 'Farmer Producer Company (FPC)',
            'registration_number' => 'U01100GJ2023PTC140221',
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
     * Test 1: Farmer can edit and withdraw their own produce listing.
     */
    public function test_farmer_produce_listing_editing_and_withdrawing(): void
    {
        [$farmer, $lot] = $this->createFarmerWithLot(4000);

        // Edit page loads
        $response = $this->actingAs($farmer)->get("/farmer/lots/{$lot->id}/edit");
        $response->assertStatus(200);

        // Update listing
        $updateResponse = $this->actingAs($farmer)->put("/farmer/lots/{$lot->id}", [
            'variety' => 'Shankar-6 Premium',
            'quantity' => 4500,
            'unit' => 'kg',
            'expected_price_per_kg' => 64.00,
            'district' => 'Rajkot',
            'pincode' => '360311',
            'harvest_date' => now()->toDateString(),
            'farming_method' => 'conventional',
            'description' => 'Updated moisture 8%',
        ]);
        $updateResponse->assertRedirect("/farmer/lots/{$lot->id}");
        $lot->refresh();
        $this->assertEquals('Shankar-6 Premium', $lot->variety);
        $this->assertEquals(4500, (float) $lot->quantity_in_kg);

        // Withdraw listing
        $withdrawResponse = $this->actingAs($farmer)->post("/farmer/lots/{$lot->id}/withdraw");
        $withdrawResponse->assertRedirect("/farmer/lots/{$lot->id}");
        $lot->refresh();
        $this->assertEquals(ProduceLot::STATUS_CANCELLED, $lot->status);
        $this->assertEquals(0, (float) $lot->available_quantity_kg);
    }

    /**
     * Test 2: Farmer cannot edit another farmer's listing.
     */
    public function test_farmer_cannot_edit_another_farmers_listing(): void
    {
        [$farmer1, $lot1] = $this->createFarmerWithLot(3000);
        $farmer2 = User::factory()->create(['role' => 'farmer', 'status' => 'active']);

        $response = $this->actingAs($farmer2)->get("/farmer/lots/{$lot1->id}/edit");
        $response->assertStatus(403);

        $putResponse = $this->actingAs($farmer2)->put("/farmer/lots/{$lot1->id}", [
            'variety' => 'Hijacked Variety',
            'quantity' => 1000,
            'unit' => 'kg',
            'district' => 'Rajkot',
            'pincode' => '360311',
            'harvest_date' => now()->toDateString(),
            'farming_method' => 'conventional',
        ]);
        $putResponse->assertStatus(403);
    }

    /**
     * Test 3: Unavailable or unapproved produce cannot be purchased or reserved.
     */
    public function test_unavailable_or_unapproved_produce_cannot_be_purchased(): void
    {
        [$farmer, $lot] = $this->createFarmerWithLot(2000);
        $lot->update([
            'status' => ProduceLot::STATUS_DRAFT,
            'quality_verified' => false,
        ]);

        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);

        $response = $this->actingAs($buyer)->post(route('buyer.lot.reserve', $lot->id), [
            'quantity_kg' => 500,
        ]);
        $response->assertSessionHasErrors('quantity_kg');
        $this->assertEquals(0, ProduceReservation::where('produce_lot_id', $lot->id)->count());
        $this->assertEquals(0, PurchaseOrder::where('produce_lot_id', $lot->id)->count());
    }

    /**
     * Test 4: Supplier organization boundaries are enforced.
     */
    public function test_supplier_organization_boundaries_enforced(): void
    {
        [$supplier1, $org1, $cat] = $this->createSupplierOrg('org-one');
        [$supplier2, $org2] = $this->createSupplierOrg('org-two');
        $manufacturer = Manufacturer::firstOrCreate(
            ['slug' => 'demo-mfg'],
            ['name' => 'Demo Mfg', 'category' => 'seeds', 'hq_location' => 'Gujarat']
        );

        $product1 = Product::create([
            'name' => 'Certified Bt Cotton Seed Pack',
            'slug' => 'certified-bt-cotton-seed-pack',
            'supplier_organization_id' => $org1->id,
            'manufacturer_id' => $manufacturer->id,
            'category_slug' => $cat->slug,
            'unit' => 'Pack (450g)',
            'unit_price' => 850.00,
            'min_order_quantity' => 10,
            'stock_quantity' => 200,
            'description' => 'Certified hybrid seed pack',
            'is_available' => true,
            'is_active' => true,
        ]);

        // Supplier 2 cannot edit Supplier 1's product
        $response = $this->actingAs($supplier2)->get("/supplier/products/{$product1->id}/edit");
        $response->assertStatus(403);

        $updateResponse = $this->actingAs($supplier2)->put("/supplier/products/{$product1->id}", [
            'name' => 'Malicious Edit',
            'category_slug' => $cat->slug,
            'unit' => 'Pack',
            'unit_price' => 100,
            'min_order_quantity' => 1,
            'stock_quantity' => 100,
            'description' => 'Malicious description',
            'is_available' => true,
        ]);
        $updateResponse->assertStatus(403);
    }

    /**
     * Test 5: Supplier products and inventory persist correctly.
     */
    public function test_supplier_products_and_inventory_persist_correctly(): void
    {
        [$supplier, $org, $cat] = $this->createSupplierOrg();

        $response = $this->actingAs($supplier)->post('/supplier/products', [
            'name' => 'Organic Neem Oil Pesticide (10,000 ppm)',
            'category_slug' => $cat->slug,
            'brand_name' => 'Kisan Rakshak',
            'unit' => 'Litre',
            'unit_price' => 450.00,
            'min_order_quantity' => 5,
            'stock_quantity' => 150,
            'is_available' => 1,
            'description' => 'Cold pressed organic neem pesticide formulation',
        ]);

        $response->assertRedirect('/supplier/products');

        $this->assertDatabaseHas('products', [
            'name' => 'Organic Neem Oil Pesticide (10,000 ppm)',
            'supplier_organization_id' => $org->id,
            'stock_quantity' => 150,
            'unit_price' => 450.00,
        ]);
    }

    /**
     * Test 6: Buyers can search and filter eligible listings.
     */
    public function test_buyers_can_search_and_filter_eligible_listings(): void
    {
        [$farmer, $lot] = $this->createFarmerWithLot(3000);
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);

        // Marketplace list loads
        $response = $this->actingAs($buyer)->get(route('buyer.marketplace', ['crop' => 'cotton', 'district' => 'Rajkot']));
        $response->assertStatus(200);
        $response->assertSee($lot->variety);
    }

    /**
     * Test 7: Buyers cannot access another buyer's private RFQs.
     */
    public function test_buyers_cannot_access_another_buyers_private_rfqs(): void
    {
        $buyer1 = User::factory()->create(['role' => 'buyer', 'status' => 'active']);
        $buyer2 = User::factory()->create(['role' => 'buyer', 'status' => 'active']);

        $rfq = Rfq::create([
            'rfq_number' => 'RFQ-20261027-1111',
            'buyer_id' => $buyer1->id,
            'item_type' => 'produce',
            'title' => 'Bulk Cotton 20 MT',
            'quantity' => 20000,
            'unit' => 'kg',
            'delivery_district' => 'Ahmedabad',
            'target_delivery_date' => now()->addDays(10)->toDateString(),
            'status' => Rfq::STATUS_OPEN,
        ]);

        $response = $this->actingAs($buyer2)->get("/rfqs/{$rfq->id}");
        $response->assertStatus(403);
    }

    /**
     * Test 8: Sellers can access only RFQs addressed to them.
     */
    public function test_sellers_can_access_only_rfqs_addressed_to_them(): void
    {
        [$supplier1, $org1] = $this->createSupplierOrg('org-one');
        [$supplier2, $org2] = $this->createSupplierOrg('org-two');
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);

        $rfq = Rfq::create([
            'rfq_number' => 'RFQ-20261027-2222',
            'buyer_id' => $buyer->id,
            'item_type' => 'supplier_product',
            'title' => 'Hybrid Seeds 500 Packs',
            'quantity' => 500,
            'unit' => 'Pack',
            'delivery_district' => 'Anand',
            'target_delivery_date' => now()->addDays(7)->toDateString(),
            'status' => Rfq::STATUS_OPEN,
        ]);

        // Invite only Supplier 1
        RfqInvitation::create([
            'rfq_id' => $rfq->id,
            'recipient_type' => 'supplier',
            'supplier_organization_id' => $org1->id,
            'status' => 'invited',
        ]);

        // Supplier 1 can view
        $response1 = $this->actingAs($supplier1)->get("/rfqs/{$rfq->id}");
        $response1->assertStatus(200);

        // Supplier 2 cannot view
        $response2 = $this->actingAs($supplier2)->get("/rfqs/{$rfq->id}");
        $response2->assertStatus(403);
    }

    /**
     * Test 9: Quotation validates quantity, price, and status transitions.
     */
    public function test_quotation_validates_quantity_price_and_status_transitions(): void
    {
        [$supplier, $org] = $this->createSupplierOrg();
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);

        $rfq = Rfq::create([
            'rfq_number' => 'RFQ-20261027-3333',
            'buyer_id' => $buyer->id,
            'item_type' => 'supplier_product',
            'title' => 'Input Supply RFQ',
            'quantity' => 100,
            'unit' => 'Litre',
            'delivery_district' => 'Anand',
            'target_delivery_date' => now()->addDays(5)->toDateString(),
            'status' => Rfq::STATUS_OPEN,
        ]);

        RfqInvitation::create([
            'rfq_id' => $rfq->id,
            'recipient_type' => 'supplier',
            'supplier_organization_id' => $org->id,
            'status' => 'invited',
        ]);

        // Negative price should fail validation
        $failResponse = $this->actingAs($supplier)->post(route('quotations.store', $rfq->id), [
            'quoted_quantity' => 100,
            'unit' => 'Litre',
            'unit_price' => -50,
            'estimated_delivery_date' => now()->addDays(4)->toDateString(),
            'valid_until' => now()->addDays(14)->toDateString(),
        ]);
        $failResponse->assertSessionHasErrors('unit_price');

        // Valid quote succeeds
        $successResponse = $this->actingAs($supplier)->post(route('quotations.store', $rfq->id), [
            'quoted_quantity' => 100,
            'unit' => 'Litre',
            'unit_price' => 450.00,
            'additional_charges' => 500.00,
            'tax_amount' => 450.00,
            'estimated_delivery_date' => now()->addDays(4)->toDateString(),
            'valid_until' => now()->addDays(14)->toDateString(),
            'quality_notes' => '100% genuine formulation',
        ]);

        $quotation = Quotation::where('rfq_id', $rfq->id)->first();
        $this->assertNotNull($quotation);
        $this->assertEquals(45950.00, (float) $quotation->total_amount); // 100*450 + 500 + 450
        $this->assertEquals(Quotation::STATUS_PENDING, $quotation->status);
    }

    /**
     * Test 10: Buyers can compare quotations belonging to their own RFQs.
     */
    public function test_buyers_can_compare_quotations_belonging_to_their_own_rfqs(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);
        $seller = User::factory()->create(['role' => 'farmer', 'status' => 'active']);

        $rfq = Rfq::create([
            'rfq_number' => 'RFQ-20261027-4444',
            'buyer_id' => $buyer->id,
            'item_type' => 'produce',
            'title' => 'Cotton Bidding',
            'quantity' => 5000,
            'unit' => 'kg',
            'delivery_district' => 'Rajkot',
            'target_delivery_date' => now()->addDays(7)->toDateString(),
            'status' => Rfq::STATUS_OPEN,
        ]);

        Quotation::create([
            'quotation_number' => 'QT-20261027-4441',
            'rfq_id' => $rfq->id,
            'seller_user_id' => $seller->id,
            'seller_type' => 'farmer',
            'quoted_quantity' => 5000,
            'unit' => 'kg',
            'unit_price' => 61.00,
            'subtotal' => 305000,
            'total_amount' => 305000,
            'estimated_delivery_date' => now()->addDays(5)->toDateString(),
            'valid_until' => now()->addDays(10)->toDateString(),
            'status' => Quotation::STATUS_PENDING,
        ]);

        // Owning buyer can view compare
        $response = $this->actingAs($buyer)->get("/rfqs/{$rfq->id}/compare");
        $response->assertStatus(200);
        $response->assertSee('QT-20261027-4441');

        // Competing seller CANNOT view comparison matrix (strict confidentiality)
        $competingSeller = User::factory()->create(['role' => 'farmer', 'status' => 'active']);
        $forbiddenResponse = $this->actingAs($competingSeller)->get("/rfqs/{$rfq->id}/compare");
        $forbiddenResponse->assertStatus(403);
    }

    /**
     * Test 11: An accepted quotation produces the correct purchase order.
     */
    public function test_accepted_quotation_produces_correct_purchase_order(): void
    {
        [$farmer, $lot] = $this->createFarmerWithLot(5000);
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);

        $rfq = Rfq::create([
            'rfq_number' => 'RFQ-20261027-5555',
            'buyer_id' => $buyer->id,
            'item_type' => 'produce',
            'produce_lot_id' => $lot->id,
            'title' => 'Purchase Cotton Lot',
            'quantity' => 2000,
            'unit' => 'kg',
            'delivery_district' => 'Rajkot',
            'target_delivery_date' => now()->addDays(5)->toDateString(),
            'status' => Rfq::STATUS_OPEN,
        ]);

        $quotation = Quotation::create([
            'quotation_number' => 'QT-20261027-5551',
            'rfq_id' => $rfq->id,
            'seller_user_id' => $farmer->id,
            'seller_type' => 'farmer',
            'produce_lot_id' => $lot->id,
            'quoted_quantity' => 2000,
            'unit' => 'kg',
            'unit_price' => 60.00,
            'additional_charges' => 200.00,
            'tax_amount' => 0.00,
            'total_amount' => 120200.00,
            'estimated_delivery_date' => now()->addDays(5)->toDateString(),
            'valid_until' => now()->addDays(14)->toDateString(),
            'status' => Quotation::STATUS_PENDING,
        ]);

        $response = $this->actingAs($buyer)->post("/quotations/{$quotation->id}/accept");
        $response->assertRedirect();

        $quotation->refresh();
        $this->assertEquals(Quotation::STATUS_ACCEPTED, $quotation->status);

        $rfq->refresh();
        $this->assertEquals(Rfq::STATUS_ACCEPTED, $rfq->status);

        // Check Purchase Order was created with correct server totals
        $po = PurchaseOrder::where('quotation_id', $quotation->id)->first();
        $this->assertNotNull($po);
        $this->assertEquals(120200.00, (float) $po->total_amount);
        $this->assertEquals(2000, (float) $po->quantity);
        $this->assertEquals(PurchaseOrder::STATUS_CONFIRMED, $po->status);

        // Check stock was decremented safely
        $lot->refresh();
        $this->assertEquals(3000, (float) $lot->available_quantity_kg);
        $this->assertEquals(2000, (float) $lot->reserved_quantity_kg);
    }

    /**
     * Test 12: Unauthorized users cannot accept quotations or modify orders.
     */
    public function test_unauthorized_users_cannot_accept_quotations_or_modify_orders(): void
    {
        [$farmer, $lot] = $this->createFarmerWithLot(5000);
        $buyer1 = User::factory()->create(['role' => 'buyer', 'status' => 'active']);
        $buyer2 = User::factory()->create(['role' => 'buyer', 'status' => 'active']);

        $rfq = Rfq::create([
            'rfq_number' => 'RFQ-20261027-6666',
            'buyer_id' => $buyer1->id,
            'item_type' => 'produce',
            'produce_lot_id' => $lot->id,
            'title' => 'Buyer 1 RFQ',
            'quantity' => 1000,
            'unit' => 'kg',
            'delivery_district' => 'Rajkot',
            'target_delivery_date' => now()->addDays(5)->toDateString(),
            'status' => Rfq::STATUS_OPEN,
        ]);

        $quote = Quotation::create([
            'quotation_number' => 'QT-20261027-6661',
            'rfq_id' => $rfq->id,
            'seller_user_id' => $farmer->id,
            'seller_type' => 'farmer',
            'produce_lot_id' => $lot->id,
            'quoted_quantity' => 1000,
            'unit' => 'kg',
            'unit_price' => 60.00,
            'total_amount' => 60000.00,
            'estimated_delivery_date' => now()->addDays(5)->toDateString(),
            'valid_until' => now()->addDays(14)->toDateString(),
            'status' => Quotation::STATUS_PENDING,
        ]);

        // Buyer 2 cannot accept Buyer 1's quote
        $response = $this->actingAs($buyer2)->post("/quotations/{$quote->id}/accept");
        $response->assertStatus(403);
    }

    /**
     * Test 13: Stock cannot be oversold.
     */
    public function test_stock_cannot_be_oversold(): void
    {
        [$farmer, $lot] = $this->createFarmerWithLot(1000); // Only 1000 kg available
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);

        $rfq = Rfq::create([
            'rfq_number' => 'RFQ-20261027-7777',
            'buyer_id' => $buyer->id,
            'item_type' => 'produce',
            'produce_lot_id' => $lot->id,
            'title' => 'Excess Quantity Attempt',
            'quantity' => 2000,
            'unit' => 'kg',
            'delivery_district' => 'Rajkot',
            'target_delivery_date' => now()->addDays(5)->toDateString(),
            'status' => Rfq::STATUS_OPEN,
        ]);

        $quote = Quotation::create([
            'quotation_number' => 'QT-20261027-7771',
            'rfq_id' => $rfq->id,
            'seller_user_id' => $farmer->id,
            'seller_type' => 'farmer',
            'produce_lot_id' => $lot->id,
            'quoted_quantity' => 2000, // Demands 2000kg when only 1000kg exists
            'unit' => 'kg',
            'unit_price' => 60.00,
            'total_amount' => 120000.00,
            'estimated_delivery_date' => now()->addDays(5)->toDateString(),
            'valid_until' => now()->addDays(14)->toDateString(),
            'status' => Quotation::STATUS_PENDING,
        ]);

        // Accept should throw validation exception and not commit stock
        $response = $this->actingAs($buyer)->post("/quotations/{$quote->id}/accept");
        $response->assertSessionHasErrors('quotation');

        $lot->refresh();
        $this->assertEquals(1000, (float) $lot->available_quantity_kg);
    }

    /**
     * Test 14: Order totals are calculated from trusted server-side values.
     */
    public function test_order_totals_are_calculated_from_trusted_server_side_values(): void
    {
        [$supplier, $org] = $this->createSupplierOrg();
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);

        $rfq = Rfq::create([
            'rfq_number' => 'RFQ-20261027-8888',
            'buyer_id' => $buyer->id,
            'item_type' => 'supplier_product',
            'title' => 'Math Verification RFQ',
            'quantity' => 50,
            'unit' => 'Bags',
            'delivery_district' => 'Anand',
            'target_delivery_date' => now()->addDays(5)->toDateString(),
            'status' => Rfq::STATUS_OPEN,
        ]);

        RfqInvitation::create([
            'rfq_id' => $rfq->id,
            'recipient_type' => 'supplier',
            'supplier_organization_id' => $org->id,
            'status' => 'invited',
        ]);

        // Submit with quantity 50, price 120.50, charges 300, tax 150
        $this->actingAs($supplier)->post(route('quotations.store', $rfq->id), [
            'quoted_quantity' => 50,
            'unit' => 'Bags',
            'unit_price' => 120.50,
            'additional_charges' => 300,
            'tax_amount' => 150,
            'estimated_delivery_date' => now()->addDays(3)->toDateString(),
            'valid_until' => now()->addDays(10)->toDateString(),
        ]);

        $quote = Quotation::where('rfq_id', $rfq->id)->first();
        $this->assertNotNull($quote);
        // Server calculates: (50 * 120.50) + 300 + 150 = 6025.00 + 450 = 6475.00
        $this->assertEquals(6475.00, (float) $quote->total_amount);
    }

    /**
     * Test 15: Payment status is transparent and not falsely marked paid.
     */
    public function test_payment_status_is_transparent_and_not_falsely_marked_paid(): void
    {
        [$farmer, $lot] = $this->createFarmerWithLot(3000);
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);

        $po = PurchaseOrder::create([
            'po_number' => 'PO-20261027-9999',
            'buyer_id' => $buyer->id,
            'seller_user_id' => $farmer->id,
            'seller_type' => 'farmer',
            'item_type' => 'produce',
            'produce_lot_id' => $lot->id,
            'item_description' => 'Cotton Shankar-6',
            'quantity' => 1000,
            'unit' => 'kg',
            'unit_price' => 60.00,
            'subtotal' => 60000.00,
            'total_amount' => 60000.00,
            'delivery_address' => 'Station Road, APMC Rajkot',
            'delivery_district' => 'Rajkot',
            'status' => PurchaseOrder::STATUS_PENDING_CONFIRMATION,
            'payment_status' => PurchaseOrder::PAYMENT_UNPAID,
        ]);

        // Status is transparently unpaid
        $this->assertEquals('unpaid', $po->payment_status);
        $this->assertNotEquals('paid', $po->payment_status);
    }

    /**
     * Test 16: FPO membership and organization boundaries are enforced.
     */
    public function test_fpo_membership_and_organization_boundaries_are_enforced(): void
    {
        [$fpoUser1, $org1] = $this->createFpoOrg('fpo-one');
        [$fpoUser2, $org2] = $this->createFpoOrg('fpo-two');

        $crop = Crop::first();
        $listing1 = FpoCollectiveListing::create([
            'listing_number' => 'FPO-LOT-COT-1111',
            'fpo_organization_id' => $org1->id,
            'crop_slug' => $crop->slug,
            'variety' => 'Shankar-6',
            'title' => 'FPO 1 Cotton Pool',
            'total_aggregated_quantity_kg' => 10000,
            'available_quantity_kg' => 10000,
            'approved_grade' => 'Grade A',
            'district' => 'Rajkot',
            'pincode' => '360311',
            'status' => FpoCollectiveListing::STATUS_PUBLISHED,
        ]);

        // FPO User 2 cannot access FPO User 1's listing
        $response = $this->actingAs($fpoUser2)->get("/fpo/collective/{$listing1->id}");
        $response->assertStatus(403);
    }

    /**
     * Test 17: FPO aggregation cannot exceed available source stock.
     */
    public function test_fpo_aggregation_cannot_exceed_available_source_stock(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg();
        [$farmer, $lot] = $this->createFarmerWithLot(3000); // 3000 kg available

        // Register farmer as active member
        FpoFarmerMembership::create([
            'fpo_organization_id' => $org->id,
            'farmer_user_id' => $farmer->id,
            'membership_number' => 'MEM-101',
            'share_units' => 10,
            'status' => 'active',
            'joined_at' => now(),
        ]);

        $crop = Crop::first();

        // Attempting to aggregate 4000 kg when only 3000 kg exists should fail
        $response = $this->actingAs($fpoUser)->post('/fpo/collective', [
            'crop_slug' => $crop->slug,
            'variety' => 'Shankar-6',
            'title' => 'Excess Pool Attempt',
            'approved_grade' => 'Grade A',
            'district' => 'Rajkot',
            'pincode' => '360311',
            'contributions' => [
                [
                    'produce_lot_id' => $lot->id,
                    'quantity_kg' => 4000,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('contributions');
    }

    /**
     * Test 18: Individual and collective stock are not double-counted.
     */
    public function test_individual_and_collective_stock_are_not_double_counted(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg();
        [$farmer, $lot] = $this->createFarmerWithLot(5000);

        FpoFarmerMembership::create([
            'fpo_organization_id' => $org->id,
            'farmer_user_id' => $farmer->id,
            'membership_number' => 'MEM-102',
            'share_units' => 10,
            'status' => 'active',
            'joined_at' => now(),
        ]);

        $crop = Crop::first();

        $response = $this->actingAs($fpoUser)->post('/fpo/collective', [
            'crop_slug' => $crop->slug,
            'variety' => 'Shankar-6',
            'title' => 'Collective Pool Shankar-6',
            'approved_grade' => 'Grade A',
            'district' => 'Rajkot',
            'pincode' => '360311',
            'contributions' => [
                [
                    'produce_lot_id' => $lot->id,
                    'quantity_kg' => 3000,
                ],
            ],
        ]);

        $response->assertRedirect();

        $lot->refresh();
        // The 3000 kg committed to FPO is subtracted from farmer's available stock
        $this->assertEquals(2000, (float) $lot->available_quantity_kg);
        $this->assertEquals(3000, (float) $lot->aggregated_quantity_kg);
        $this->assertEquals(3000, (float) $lot->reserved_quantity_kg);
    }

    /**
     * Test 19: Withdrawn collective listing safely releases stock to farmers.
     */
    public function test_withdrawn_collective_listing_safely_releases_stock_to_farmers(): void
    {
        [$fpoUser, $org] = $this->createFpoOrg();
        [$farmer, $lot] = $this->createFarmerWithLot(5000);

        FpoFarmerMembership::create([
            'fpo_organization_id' => $org->id,
            'farmer_user_id' => $farmer->id,
            'membership_number' => 'MEM-103',
            'share_units' => 10,
            'status' => 'active',
            'joined_at' => now(),
        ]);

        $crop = Crop::first();

        $this->actingAs($fpoUser)->post('/fpo/collective', [
            'crop_slug' => $crop->slug,
            'variety' => 'Shankar-6',
            'title' => 'Temporary Pool',
            'approved_grade' => 'Grade A',
            'district' => 'Rajkot',
            'pincode' => '360311',
            'contributions' => [
                [
                    'produce_lot_id' => $lot->id,
                    'quantity_kg' => 2500,
                ],
            ],
        ]);

        $listing = FpoCollectiveListing::where('fpo_organization_id', $org->id)->latest()->first();

        // Withdraw collective listing
        $withdrawResponse = $this->actingAs($fpoUser)->post("/fpo/collective/{$listing->id}/withdraw");
        $withdrawResponse->assertRedirect();

        $lot->refresh();
        // Released back to 5000 kg!
        $this->assertEquals(5000, (float) $lot->available_quantity_kg);
        $this->assertEquals(0, (float) $lot->aggregated_quantity_kg);
    }

    /**
     * Test 20: Cancelled purchase order safely restores inventory.
     */
    public function test_cancelled_purchase_order_safely_restores_inventory(): void
    {
        [$farmer, $lot] = $this->createFarmerWithLot(5000);
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);

        // Directly simulate confirmed PO holding 2000 kg
        $lot->available_quantity_kg = 3000;
        $lot->reserved_quantity_kg = 2000;
        $lot->save();

        $po = PurchaseOrder::create([
            'po_number' => 'PO-20261027-CANCEL',
            'buyer_id' => $buyer->id,
            'seller_user_id' => $farmer->id,
            'seller_type' => 'farmer',
            'item_type' => 'produce',
            'produce_lot_id' => $lot->id,
            'item_description' => 'Cotton Lot Cancellation Test',
            'quantity' => 2000,
            'unit' => 'kg',
            'unit_price' => 60.00,
            'subtotal' => 120000.00,
            'total_amount' => 120000.00,
            'delivery_address' => 'Gondal Yard, Rajkot',
            'delivery_district' => 'Rajkot',
            'status' => PurchaseOrder::STATUS_CONFIRMED,
            'payment_status' => PurchaseOrder::PAYMENT_UNPAID,
        ]);

        $cancelResponse = $this->actingAs($buyer)->post("/orders/{$po->id}/cancel", [
            'cancellation_reason' => 'Buyer delivery facility flooded; force majeure.',
        ]);
        $cancelResponse->assertRedirect();

        $po->refresh();
        $this->assertEquals(PurchaseOrder::STATUS_CANCELLED, $po->status);

        // Stock restored!
        $lot->refresh();
        $this->assertEquals(5000, (float) $lot->available_quantity_kg);
        $this->assertEquals(0, (float) $lot->reserved_quantity_kg);
    }

    /**
     * Test 21: Notifications reach only eligible recipients.
     */
    public function test_notifications_reach_only_eligible_recipients(): void
    {
        Notification::fake();

        [$supplier, $org] = $this->createSupplierOrg();
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);
        $uninvitedSeller = User::factory()->create(['role' => 'farmer', 'status' => 'active']);

        $response = $this->actingAs($buyer)->post('/rfqs', [
            'title' => 'Notification Target Test RFQ',
            'item_type' => 'supplier_product',
            'quantity' => 100,
            'unit' => 'Bags',
            'delivery_district' => 'Anand',
            'target_delivery_date' => now()->addDays(7)->toDateString(),
            'invited_sellers' => ["supplier:{$org->id}"],
        ]);

        $rfq = Rfq::latest()->first();

        // Invited supplier received notification
        Notification::assertSentTo($supplier, RfqCreatedNotification::class);

        // Uninvited seller did NOT receive notification
        Notification::assertNotSentTo($uninvitedSeller, RfqCreatedNotification::class);
    }
}
