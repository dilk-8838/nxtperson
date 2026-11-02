<?php

namespace Tests\Feature;

use App\Models\Advisory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class FarmerExperienceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_farmer_can_request_and_verify_simulated_otp(): void
    {
        // 1. Request simulated OTP
        $response = $this->post(route('demo.send-otp'), [
            'phone' => '9876543210',
        ]);
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('otp_sent');

        // 2. Verify simulated OTP
        $loginResponse = $this->post(route('demo.verify-otp'), [
            'phone' => '9876543210',
            'otp' => '123456',
        ]);
        $loginResponse->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->assertEquals('farmer', session('demo_role'));
    }

    public function test_farmer_dashboard_displays_plots_tickets_and_weather(): void
    {
        $farmer = User::where('role', 'farmer')->first();
        $this->actingAs($farmer);
        session(['demo_role' => 'farmer']);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('Enrolled Farm Plots');
        $response->assertSee('Kharif Cotton Plot A');
        $response->assertSee('Crop Advisory');
        $response->assertSee('APMC Mandi Benchmarks');
    }

    public function test_crop_advisory_submission_stores_record_and_generates_diagnosis(): void
    {
        $farmer = User::where('role', 'farmer')->first();
        $this->actingAs($farmer);
        session(['demo_role' => 'farmer']);

        $file = UploadedFile::fake()->image('cotton_leaf.jpg', 600, 600);

        $response = $this->post(route('advisory.store'), [
            'crop_slug' => 'cotton',
            'issue_description' => 'Observed pink caterpillars boring into squares and green bolls in Rajkot.',
            'symptom_key' => 'bollworm',
            'image' => $file,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('advisories', [
            'crop_slug' => 'cotton',
            'status' => 'ai_analyzed',
        ]);

        $advisory = Advisory::where('crop_slug', 'cotton')->orderBy('id', 'desc')->first();
        $this->assertNotNull($advisory);
        $this->assertStringContainsString('NX-COT-', $advisory->ticket_number);
        $this->assertStringContainsString('Pink Bollworm', $advisory->ai_diagnosis_title);

        $response->assertRedirect(route('advisory.show', $advisory->ticket_number));
    }

    public function test_farmer_can_escalate_advisory_to_senior_agronomist(): void
    {
        $advisory = Advisory::create([
            'ticket_number' => 'NX-POT-20261023-1001',
            'crop_slug' => 'potato',
            'farmer_name' => 'Suresh Patel',
            'farmer_phone' => '9876543210',
            'farmer_district' => 'Banaskantha',
            'issue_description' => 'Late blight spots with water-soaked margins in Deesa.',
            'symptom_key' => 'blight',
            'ai_diagnosis_title' => 'Late Blight (Phytophthora infestans)',
            'ai_diagnosis_description' => 'Dark water-soaked necrotic patches.',
            'ai_recommended_solution' => 'Apply bio-copper formulation.',
            'ai_confidence' => '97% Confidence',
            'status' => 'ai_analyzed',
            'is_demonstration' => true,
        ]);

        $response = $this->post(route('advisory.escalate', $advisory->ticket_number));
        $response->assertRedirect(route('advisory.show', $advisory->ticket_number));

        $advisory->refresh();
        $this->assertEquals('escalated', $advisory->status);
        $this->assertNotNull($advisory->assigned_agronomist);
    }
}
