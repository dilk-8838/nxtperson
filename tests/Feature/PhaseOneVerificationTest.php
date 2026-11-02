<?php

namespace Tests\Feature;

use App\Models\Crop;
use App\Models\User;
use App\Services\Advisory\CropAdvisoryEngineInterface;
use App\Services\Advisory\SampleAiCropDiagnosticEngine;
use App\Services\Mandi\GujaratApmcSeededProvider;
use App\Services\Mandi\MandiPriceServiceInterface;
use App\Services\Weather\GujaratSampleWeatherProvider;
use App\Services\Weather\WeatherServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseOneVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed database for testing
        $this->seed();
    }

    /**
     * Test homepage loads successfully with all 8 crops from database.
     */
    public function test_homepage_loads_successfully_with_database_crops_and_mandi(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('NxtPerson');
        $response->assertSee('AgriTech');
        $response->assertSee('Demonstration Output');

        // Verify 8 required crops are present
        $crops = Crop::all();
        $this->assertCount(8, $crops);
        $response->assertSee('Cotton');
        $response->assertSee('Banana');
        $response->assertSee('Potato');
        $response->assertSee('Rice');
        $response->assertSee('Wheat');
        $response->assertSee('Tomato');
        $response->assertSee('Cauliflower');
        $response->assertSee('Chilli');

        // Verify Mandi rates are present
        $response->assertSee('APMC Rajkot');
        $response->assertSee('APMC Gondal');
        $response->assertSee('₹7,150');
    }

    /**
     * Test localization switcher across English, Gujarati, and Hindi.
     */
    public function test_localization_switches_languages_correctly(): void
    {
        // 1. Switch to Gujarati
        $responseGu = $this->get('/locale/gu');
        $responseGu->assertSessionHas('locale', 'gu');

        $guPage = $this->withSession(['locale' => 'gu'])->get('/');
        $guPage->assertStatus(200);
        $guPage->assertSee('કપાસ'); // Gujarati Cotton
        $guPage->assertSee('કેળા'); // Gujarati Banana
        $guPage->assertSee('બટાકા'); // Gujarati Potato

        // 2. Switch to Hindi
        $responseHi = $this->get('/locale/hi');
        $responseHi->assertSessionHas('locale', 'hi');

        $hiPage = $this->withSession(['locale' => 'hi'])->get('/');
        $hiPage->assertStatus(200);
        $hiPage->assertSee('कपास'); // Hindi Cotton
        $hiPage->assertSee('आलू'); // Hindi Potato
        $hiPage->assertSee('मिर्च'); // Hindi Chilli

        // 3. Switch back to English
        $responseEn = $this->get('/locale/en');
        $responseEn->assertSessionHas('locale', 'en');
    }

    /**
     * Test demo role switcher creates and authenticates proper users.
     */
    public function test_demo_role_switcher_authenticates_users(): void
    {
        // Switch to Farmer
        $this->post('/demo/switch-role', ['role' => 'farmer'])
            ->assertRedirect();
        $this->assertAuthenticated();
        $this->assertEquals('farmer', auth()->user()->role);

        // Switch to Distributor
        $this->post('/demo/switch-role', ['role' => 'distributor'])
            ->assertRedirect();
        $this->assertAuthenticated();
        $this->assertEquals('distributor', auth()->user()->role);

        // Switch to Admin
        $this->post('/demo/switch-role', ['role' => 'admin'])
            ->assertRedirect();
        $this->assertAuthenticated();
        $this->assertEquals('admin', auth()->user()->role);
    }

    /**
     * Test backend role authorization on protected endpoints.
     */
    public function test_role_authorization_middleware_enforces_boundaries(): void
    {
        $farmer = User::where('role', 'farmer')->first();
        $admin = User::where('role', 'admin')->first();

        // Farmer attempting to access Admin Portal should be rejected with 403 Forbidden
        $responseForbidden = $this->actingAs($farmer)->get('/admin');
        $responseForbidden->assertStatus(403);

        // Admin accessing Admin Portal succeeds
        $responseAllowed = $this->actingAs($admin)->get('/admin');
        $responseAllowed->assertStatus(200);
    }

    /**
     * Test simulated OTP verification workflow for farmer onboarding.
     */
    public function test_simulated_otp_farmer_verification(): void
    {
        // 1. Send simulated OTP
        $sendResponse = $this->post('/demo/send-otp', ['phone' => '9876543210']);
        $sendResponse->assertRedirect(route('login'));
        $sendResponse->assertSessionHas('otp_sent');

        // 2. Reject incorrect OTP
        $invalidResponse = $this->post('/demo/verify-otp', [
            'phone' => '9876543210',
            'otp' => '999999',
        ]);
        $invalidResponse->assertSessionHasErrors('otp');

        // 3. Accept simulated demo OTP 123456
        $validResponse = $this->post('/demo/verify-otp', [
            'phone' => '9876543210',
            'otp' => '123456',
        ]);
        $validResponse->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->assertEquals('farmer', auth()->user()->role);
    }

    /**
     * Test modular service container bindings.
     */
    public function test_modular_service_container_bindings(): void
    {
        $weatherService = app(WeatherServiceInterface::class);
        $this->assertInstanceOf(GujaratSampleWeatherProvider::class, $weatherService);

        $advisoryEngine = app(CropAdvisoryEngineInterface::class);
        $this->assertInstanceOf(SampleAiCropDiagnosticEngine::class, $advisoryEngine);

        $mandiService = app(MandiPriceServiceInterface::class);
        $this->assertInstanceOf(GujaratApmcSeededProvider::class, $mandiService);

        // Verify diagnostic diagnosis returns simulated disclaimer
        $diagnosis = $advisoryEngine->diagnose('cotton', 'bollworm');
        $this->assertTrue($diagnosis['is_demonstration']);
        $this->assertStringContainsString('Pink Bollworm', $diagnosis['title']);
    }
}
