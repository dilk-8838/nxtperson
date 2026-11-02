<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemainingServicePagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test Weather Intelligence interface loads with sample telemetry and 7-day spray window.
     */
    public function test_weather_page_loads_with_sample_telemetry(): void
    {
        $response = $this->get('/weather');

        $response->assertStatus(200);
        $response->assertSee('Agro-Meteorological Advisory');
        $response->assertSee('Current Telemetry');
        $response->assertSee('Hourly Field Spraying Suitability');
        $response->assertSee('7-Day Gujarat Agro Zone Outlook');
    }

    /**
     * Test Weather interface district switching (e.g. Anand).
     */
    public function test_weather_page_supports_district_switching(): void
    {
        $response = $this->get('/weather?district=Anand');

        $response->assertStatus(200);
        $response->assertSee('Anand Station');
        $response->assertSee('Middle Gujarat Agro-Climatic Zone');
    }

    /**
     * Test Satellite NDVI Field Health page loads with plot and spectral controls.
     */
    public function test_satellite_page_loads_with_interactive_vegetative_analysis(): void
    {
        $response = $this->get('/satellite');

        $response->assertStatus(200);
        $response->assertSee('Satellite Field Health');
        $response->assertSee('Sentinel-2 10-Meter Multi-Spectral Radiometry');
        $response->assertSee('NDVI (Vegetative Health)');
        $response->assertSee('NDWI (Canopy Moisture)');
        $response->assertSee('True-Color Natural RGB');
        $response->assertSee('Mean NDVI');
    }

    /**
     * Test APMC Mandi Price Discovery loads with benchmark rates and daily arrivals.
     */
    public function test_mandi_page_loads_with_benchmark_rates(): void
    {
        $response = $this->get('/mandi');

        $response->assertStatus(200);
        $response->assertSee('Daily Agricultural Commodity Price Discovery');
        $response->assertSee('Real-Time Spot Market Realizations');
        $response->assertSee('Modal Price');
        $response->assertSee('APMC Yard');
    }

    /**
     * Test Mandi filtering by APMC yard.
     */
    public function test_mandi_page_can_be_filtered_by_yard(): void
    {
        $response = $this->get('/mandi?yard=APMC+Rajkot');

        $response->assertStatus(200);
        $response->assertSee('APMC Rajkot');
    }

    /**
     * Test Mandi filtering by crop commodity.
     */
    public function test_mandi_page_can_be_filtered_by_crop(): void
    {
        $response = $this->get('/mandi?crop=cotton');

        $response->assertStatus(200);
        $response->assertSee('Cotton');
    }
}
