<?php

namespace Tests\Feature;

use App\Models\Advisory;
use App\Models\Distributor;
use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFunctionalityTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $farmerUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->adminUser = User::where('role', 'admin')->first();
        $this->farmerUser = User::where('role', 'farmer')->first();
    }

    /**
     * Test role authorization: Farmer cannot access admin portal (receives 403).
     */
    public function test_farmer_cannot_access_admin_portal(): void
    {
        $response = $this->actingAs($this->farmerUser)
            ->withSession(['demo_role' => 'farmer'])
            ->get('/admin');

        $response->assertStatus(403);
    }

    /**
     * Test admin can view admin overview dashboard with metrics and triage queue.
     */
    public function test_admin_can_view_overview_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->withSession(['demo_role' => 'admin'])
            ->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Agronomic Operations & Platform Oversight', false);
        $response->assertSee('Farmer Crop Advisory Triage Queue');
    }

    /**
     * Test admin can update advisory status and add agronomist notes.
     */
    public function test_admin_can_update_advisory_status(): void
    {
        $advisory = Advisory::first();

        $response = $this->actingAs($this->adminUser)
            ->withSession(['demo_role' => 'admin'])
            ->post("/admin/advisories/{$advisory->id}/status", [
                'status' => 'resolved',
                'agronomist_notes' => 'Field inspected by Junagadh expert. Spray Azadirachtin applied successfully.',
            ]);

        $response->assertRedirect();
        $advisory->refresh();
        $this->assertEquals('resolved', $advisory->status);
        $this->assertStringContainsString('Azadirachtin applied successfully', $advisory->agronomist_notes);
    }

    /**
     * Test admin product CRUD: Create, Update, Delete.
     */
    public function test_admin_can_create_update_and_delete_product(): void
    {
        $mfg = Manufacturer::first();

        // 1. Create Product
        $createResponse = $this->actingAs($this->adminUser)
            ->withSession(['demo_role' => 'admin'])
            ->post('/admin/products', [
                'name' => 'Demo Bio-Stimulant Ultra',
                'manufacturer_id' => $mfg->id,
                'category_slug' => 'micronutrients',
                'active_ingredient' => 'Seaweed Extract 20%',
                'package_size' => '500 ml',
                'price_guide' => 'MRP ₹450',
                'description' => 'High-grade liquid formulation for root cell elongation.',
                'target_crops' => ['cotton', 'banana'],
                'is_featured' => 1,
            ]);

        $createResponse->assertRedirect('/admin/products');
        $this->assertDatabaseHas('products', ['name' => 'Demo Bio-Stimulant Ultra']);

        $product = Product::where('name', 'Demo Bio-Stimulant Ultra')->first();

        // 2. Update Product
        $updateResponse = $this->actingAs($this->adminUser)
            ->withSession(['demo_role' => 'admin'])
            ->put("/admin/products/{$product->id}", [
                'name' => 'Demo Bio-Stimulant Ultra Plus',
                'manufacturer_id' => $mfg->id,
                'category_slug' => 'micronutrients',
                'active_ingredient' => 'Seaweed Extract 25%',
                'package_size' => '1 Liter',
                'price_guide' => 'MRP ₹800',
                'description' => 'Updated formulation for Gujarat summer crops.',
                'target_crops' => ['cotton'],
                'is_featured' => 0,
            ]);

        $updateResponse->assertRedirect('/admin/products');
        $this->assertDatabaseHas('products', ['name' => 'Demo Bio-Stimulant Ultra Plus']);

        // 3. Delete Product
        $deleteResponse = $this->actingAs($this->adminUser)
            ->withSession(['demo_role' => 'admin'])
            ->delete("/admin/products/{$product->id}");

        $deleteResponse->assertRedirect('/admin/products');
        $this->assertDatabaseMissing('products', ['name' => 'Demo Bio-Stimulant Ultra Plus']);
    }

    /**
     * Test admin distributor CRUD: Create, Update, Delete.
     */
    public function test_admin_can_create_update_and_delete_distributor(): void
    {
        // 1. Register Distributor with valid 6-digit Gujarat pincode
        $createResponse = $this->actingAs($this->adminUser)
            ->withSession(['demo_role' => 'admin'])
            ->post('/admin/distributors', [
                'business_name' => 'Morbi Kisan Agro Hub',
                'name' => 'Hiteshbhai Patel',
                'license_number' => 'GJ-MRB-AGR-2026-0991',
                'district' => 'Rajkot',
                'taluka' => 'Morbi',
                'address' => 'Near APMC Gate 1, Morbi Road',
                'pincode' => '363641',
                'phone' => '+91 9876543299',
                'authorized_categories' => ['fertilizers', 'seeds'],
            ]);

        $createResponse->assertRedirect('/admin/distributors');
        $this->assertDatabaseHas('distributors', ['pincode' => '363641']);

        $distributor = Distributor::where('pincode', '363641')->first();

        // 2. Update Distributor
        $updateResponse = $this->actingAs($this->adminUser)
            ->withSession(['demo_role' => 'admin'])
            ->put("/admin/distributors/{$distributor->id}", [
                'business_name' => 'Morbi Kisan Agro Hub & Cold Storage',
                'name' => 'Hiteshbhai Patel',
                'license_number' => 'GJ-MRB-AGR-2026-0991',
                'district' => 'Rajkot',
                'taluka' => 'Morbi',
                'address' => 'Near APMC Gate 1, Morbi Road',
                'pincode' => '363641',
                'phone' => '+91 9876543299',
                'authorized_categories' => ['fertilizers', 'seeds', 'crop-protection'],
            ]);

        $updateResponse->assertRedirect('/admin/distributors');
        $this->assertDatabaseHas('distributors', ['business_name' => 'Morbi Kisan Agro Hub & Cold Storage']);

        // 3. Delete Distributor
        $deleteResponse = $this->actingAs($this->adminUser)
            ->withSession(['demo_role' => 'admin'])
            ->delete("/admin/distributors/{$distributor->id}");

        $deleteResponse->assertRedirect('/admin/distributors');
        $this->assertDatabaseMissing('distributors', ['pincode' => '363641']);
    }
}
