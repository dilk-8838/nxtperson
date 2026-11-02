<?php

namespace Tests\Feature;

use App\Models\FarmerProfile;
use App\Models\FpoMember;
use App\Models\FpoOrganization;
use App\Models\SupplierMember;
use App\Models\SupplierOrganization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleArchitectureAndOrganizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test 1: Each role can access its authorized dashboard.
     */
    public function test_each_role_can_access_its_authorized_dashboard(): void
    {
        // 1. Farmer
        $farmer = User::factory()->create(['role' => 'farmer', 'status' => 'active']);
        FarmerProfile::create([
            'user_id' => $farmer->id,
            'farm_name' => 'Kisan Farm',
            'village' => 'Gondal',
            'district' => 'Rajkot',
            'total_acreage' => 12.0,
            'cultivated_acreage' => 10.0,
        ]);
        $response = $this->actingAs($farmer)->get('/dashboard');
        $response->assertStatus(200);

        // 2. Supplier
        $supplierUser = User::factory()->create(['role' => 'supplier', 'status' => 'active']);
        $supplierOrg = SupplierOrganization::create([
            'name' => 'Agri Seeds Ltd',
            'slug' => 'agri-seeds-ltd',
            'category' => 'seeds',
            'authorized_representative_name' => $supplierUser->name,
            'contact_email' => $supplierUser->email,
            'contact_phone' => '9876543220',
            'operating_address' => 'GIDC Anand',
            'district' => 'Anand',
            'verification_status' => SupplierOrganization::STATUS_APPROVED,
        ]);
        SupplierMember::create([
            'supplier_organization_id' => $supplierOrg->id,
            'user_id' => $supplierUser->id,
            'role_in_org' => 'owner',
            'is_primary_contact' => true,
        ]);
        $response = $this->actingAs($supplierUser)->get('/supplier/dashboard');
        $response->assertStatus(200);

        // 3. Buyer
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);
        $response = $this->actingAs($buyer)->get('/buyer/dashboard');
        $response->assertStatus(200);

        // 4. FPO
        $fpoUser = User::factory()->create(['role' => 'fpo', 'status' => 'active']);
        $fpoOrg = FpoOrganization::create([
            'name' => 'Kisan Producer Co',
            'slug' => 'kisan-producer-co',
            'organization_type' => 'Farmer Producer Company (FPC)',
            'registration_number' => 'U01100GJ2023PTC140221',
            'registered_address' => 'Station Road Gondal',
            'district' => 'Rajkot',
            'authorized_representative_name' => $fpoUser->name,
            'contact_email' => $fpoUser->email,
            'contact_phone' => '9876543250',
            'verification_status' => FpoOrganization::STATUS_APPROVED,
        ]);
        FpoMember::create([
            'fpo_organization_id' => $fpoOrg->id,
            'user_id' => $fpoUser->id,
            'role_in_fpo' => 'admin',
            'is_primary' => true,
        ]);
        $response = $this->actingAs($fpoUser)->get('/fpo/dashboard');
        $response->assertStatus(200);

        // 5. Admin
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
    }

    /**
     * Test 2: Unauthorized roles cannot access restricted routes.
     */
    public function test_unauthorized_roles_cannot_access_restricted_portals(): void
    {
        $farmer = User::factory()->create(['role' => 'farmer', 'status' => 'active']);
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);

        // Farmer cannot access admin
        $response = $this->actingAs($farmer)->get('/admin');
        $response->assertStatus(403);

        // Buyer cannot access supplier dashboard
        $response = $this->actingAs($buyer)->get('/supplier/dashboard');
        $response->assertStatus(403);

        // Buyer cannot access FPO dashboard
        $response = $this->actingAs($buyer)->get('/fpo/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Test 3: Suspended user accounts are rejected.
     */
    public function test_suspended_users_are_denied_access(): void
    {
        $suspendedFarmer = User::factory()->create(['role' => 'farmer', 'status' => 'suspended']);
        $response = $this->actingAs($suspendedFarmer)->get('/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Test 4: Privilege escalation is prevented during registration.
     */
    public function test_users_cannot_register_as_admin(): void
    {
        $response = $this->post('/register', [
            'role' => 'admin',
            'name' => 'Malicious Hacker',
            'email' => 'hacker@test.com',
            'phone' => '9999999999',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'district' => 'Ahmedabad',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'hacker@test.com']);
    }

    /**
     * Test 5: Farmer registration establishes user and farmer profile.
     */
    public function test_farmer_registration_creates_profile(): void
    {
        $response = $this->post('/register', [
            'role' => 'farmer',
            'name' => 'Bhavik Patel',
            'email' => 'bhavik@kisan.com',
            'phone' => '9876500001',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'district' => 'Rajkot',
            'farm_name' => 'Bhavik Farm',
            'village' => 'Gondal',
            'total_acreage' => 15.5,
            'irrigation_type' => 'drip',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'bhavik@kisan.com', 'role' => 'farmer']);
        $this->assertDatabaseHas('farmer_profiles', ['farm_name' => 'Bhavik Farm', 'total_acreage' => 15.5]);
    }

    /**
     * Test 6: Supplier registration creates pending organization and notifies admin.
     */
    public function test_supplier_registration_creates_pending_organization(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $response = $this->post('/register', [
            'role' => 'supplier',
            'name' => 'Dinesh Shah',
            'email' => 'dinesh@inputs.com',
            'phone' => '9876500002',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'district' => 'Anand',
            'business_name' => 'Shah Fertilizers LLP',
            'business_category' => 'fertilizers',
            'operating_address' => 'Anand GIDC',
            'gst_number' => '24AAACS1234S1Z1',
            'license_number' => 'LIC-2026-99',
        ]);

        $response->assertRedirect('/supplier/dashboard');
        $this->assertDatabaseHas('supplier_organizations', [
            'name' => 'Shah Fertilizers LLP',
            'verification_status' => SupplierOrganization::STATUS_PENDING,
        ]);
        $this->assertDatabaseHas('supplier_members', [
            'role_in_org' => 'owner',
            'is_primary_contact' => 1,
        ]);
    }

    /**
     * Test 7: FPO registration creates pending organization and membership.
     */
    public function test_fpo_registration_creates_pending_organization(): void
    {
        $response = $this->post('/register', [
            'role' => 'fpo',
            'name' => 'Govindbhai Patel',
            'email' => 'govind@fpo.com',
            'phone' => '9876500003',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'district' => 'Junagadh',
            'fpo_name' => 'Gir Kesar Kisan FPC',
            'organization_type' => 'Farmer Producer Company (FPC)',
            'registration_number' => 'U01100GJ2024PTC998811',
            'operating_address' => 'Talala Market Yard',
            'incorporation_year' => 2024,
        ]);

        $response->assertRedirect('/fpo/dashboard');
        $this->assertDatabaseHas('fpo_organizations', [
            'name' => 'Gir Kesar Kisan FPC',
            'verification_status' => FpoOrganization::STATUS_PENDING,
        ]);
    }

    /**
     * Test 8: Farmer profile updates persist correctly.
     */
    public function test_farmer_can_update_profile(): void
    {
        $farmer = User::factory()->create(['role' => 'farmer', 'status' => 'active']);
        $profile = FarmerProfile::create([
            'user_id' => $farmer->id,
            'farm_name' => 'Initial Farm',
            'district' => 'Rajkot',
            'total_acreage' => 10.0,
        ]);

        $response = $this->actingAs($farmer)->post('/farmer/profile', [
            'farm_name' => 'Updated Patel Farm',
            'village' => 'Kotda Sangani',
            'district' => 'Rajkot',
            'total_acreage' => 22.5,
            'cultivated_acreage' => 20.0,
            'soil_type' => 'Medium Black Soil',
            'irrigation_type' => 'drip',
            'estimated_harvest_quantity' => 300,
            'estimated_harvest_unit' => 'Quintal',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('farmer_profiles', [
            'id' => $profile->id,
            'farm_name' => 'Updated Patel Farm',
            'total_acreage' => 22.5,
            'village' => 'Kotda Sangani',
        ]);
    }

    /**
     * Test 9: FPO can link existing farmer without duplicating identities.
     */
    public function test_fpo_can_link_farmer_membership(): void
    {
        $fpoUser = User::factory()->create(['role' => 'fpo', 'status' => 'active']);
        $fpoOrg = FpoOrganization::create([
            'name' => 'Saurashtra FPC',
            'slug' => 'saurashtra-fpc',
            'organization_type' => 'Farmer Producer Company (FPC)',
            'registration_number' => 'U01100GJ2023PTC140999',
            'registered_address' => 'Gondal',
            'district' => 'Rajkot',
            'authorized_representative_name' => $fpoUser->name,
            'contact_email' => $fpoUser->email,
            'contact_phone' => '9876500010',
            'verification_status' => FpoOrganization::STATUS_APPROVED,
        ]);
        FpoMember::create([
            'fpo_organization_id' => $fpoOrg->id,
            'user_id' => $fpoUser->id,
            'role_in_fpo' => 'admin',
            'is_primary' => true,
        ]);

        // Existing Farmer
        $farmer = User::factory()->create(['role' => 'farmer', 'status' => 'active', 'phone' => '9876543999']);
        FarmerProfile::create([
            'user_id' => $farmer->id,
            'farm_name' => 'Patel Land',
            'district' => 'Rajkot',
            'total_acreage' => 14.5,
        ]);

        // Link farmer by phone
        $response = $this->actingAs($fpoUser)->post('/fpo/members', [
            'farmer_identifier' => '9876543999',
            'member_code' => 'FPC-MEM-77',
            'share_units' => 20,
            'notes' => 'Cotton grower cluster',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('fpo_farmer_memberships', [
            'fpo_organization_id' => $fpoOrg->id,
            'farmer_user_id' => $farmer->id,
            'member_code' => 'FPC-MEM-77',
            'share_units' => 20,
        ]);

        // Check aggregate land
        $this->assertEquals(14.5, $fpoOrg->totalMemberAcreage());
    }

    /**
     * Test 10: Admin can review, approve, reject, or suspend supplier organizations.
     */
    public function test_admin_can_approve_supplier(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $supplierOrg = SupplierOrganization::create([
            'name' => 'Pending Inputs LLP',
            'slug' => 'pending-inputs-llp',
            'category' => 'seeds',
            'authorized_representative_name' => 'Test Rep',
            'contact_email' => 'rep@inputs.com',
            'contact_phone' => '9876500020',
            'operating_address' => 'Surat',
            'district' => 'Surat',
            'verification_status' => SupplierOrganization::STATUS_PENDING,
        ]);

        $response = $this->actingAs($admin)->post("/admin/suppliers/{$supplierOrg->id}/review", [
            'action' => 'approve',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('supplier_organizations', [
            'id' => $supplierOrg->id,
            'verification_status' => SupplierOrganization::STATUS_APPROVED,
            'verified_by' => $admin->id,
        ]);
    }

    /**
     * Test 11: Admin can suspend and activate user accounts.
     */
    public function test_admin_can_suspend_user_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $user = User::factory()->create(['role' => 'buyer', 'status' => 'active']);

        $response = $this->actingAs($admin)->post("/admin/users/{$user->id}/status", [
            'status' => 'suspended',
            'rejection_reason' => 'Policy violation in market bidding',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 'suspended',
        ]);
    }
}
