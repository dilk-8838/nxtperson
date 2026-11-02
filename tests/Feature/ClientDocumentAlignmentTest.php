<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientDocumentAlignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test all 4 solution pillars resolve with status 200 and correct client document concepts.
     */
    public function test_all_four_solution_pillars_resolve_successfully(): void
    {
        // Pillar 1: AI Quality Assurance & Farm-Gate Grading
        $response = $this->get(route('solutions.quality-grading'));
        $response->assertOk();
        $response->assertSee('AI-Powered Quality Assurance & Farm-Gate Grading');
        $response->assertSee('90,000');
        $response->assertSee('Planned Roadmap Capability');

        // Pillar 2: Precision Agriculture & Smart Irrigation
        $response = $this->get(route('solutions.precision-agri'));
        $response->assertOk();
        $response->assertSee('Precision Agriculture & Smart Irrigation');
        $response->assertSee('30%');
        $response->assertSee('Sentinel-2');

        // Pillar 3: AI Advisory & Digital Farmer Platform
        $response = $this->get(route('solutions.digital-farmer'));
        $response->assertOk();
        $response->assertSee('AI Advisory & Digital Farmer Platform');
        $response->assertSee('Multilingual');

        // Pillar 4: Integrated Digital Agricultural Marketplace
        $response = $this->get(route('solutions.marketplace'));
        $response->assertOk();
        $response->assertSee('Integrated Digital Agricultural Marketplace');
        $response->assertSee('Input Catalog Live');

        // Solutions Overview
        $response = $this->get(route('solutions.index'));
        $response->assertOk();
        $response->assertSee('Four Pillars of the Integrated Agri-Ecosystem');
        $response->assertSee('How Nxtperson Works: The 8-Stage Agricultural Journey');
    }

    /**
     * Test marketplace overview and separation between inputs (demo) and produce (planned).
     */
    public function test_marketplace_overview_separates_inputs_from_produce(): void
    {
        $response = $this->get(route('marketplace.index'));
        $response->assertOk();
        $response->assertSee('Agricultural Inputs Discovery', false);
        $response->assertSee('Agricultural Produce Marketplace & Institutional Sourcing', false);
        $response->assertSee('High-Yield Seeds', false);
        $response->assertSee('Planned Roadmap Capabilities', false);
    }

    /**
     * Test 10-stakeholder ecosystem page.
     */
    public function test_ecosystem_page_displays_all_stakeholder_groups(): void
    {
        $response = $this->get(route('ecosystem.index'));
        $response->assertOk();
        $response->assertSee('The Nxtperson Agricultural Ecosystem', false);
        $response->assertSee('Smallholder Farmers', false);
        $response->assertSee('FPOs & Cooperatives', false);
        $response->assertSee('Input Manufacturers', false);
        $response->assertSee('Produce Buyers & Industry', false);
        $response->assertSee('Agri Advisors & Experts', false);
        $response->assertSee('Equipment Providers', false);
        $response->assertSee('Cold Storage & Warehouses', false);
        $response->assertSee('Logistics & Transport', false);
        $response->assertSee('Financial Institutions', false);
        $response->assertSee('Exporters & Global Corridors', false);
    }

    /**
     * Test planned company roadmap page (2026-2031).
     */
    public function test_roadmap_page_displays_phased_timeline(): void
    {
        $response = $this->get(route('roadmap.index'));
        $response->assertOk();
        $response->assertSee('Planned Company Roadmap (2026–2031)', false);
        $response->assertSee('Phase 1: Foundation & MVP', false);
        $response->assertSee('Phase 2: Commercial Launch', false);
        $response->assertSee('Phase 3: Regional Expansion', false);
        $response->assertSee('Phase 4: Pan-India Scale', false);
        $response->assertSee('Phase 5: Global Expansion', false);
        $response->assertSee('Ahmedabad and North Gujarat', false);
    }

    /**
     * Test About Us page contains official corporate identity and verified leadership.
     */
    public function test_about_us_contains_official_corporate_identity_and_leadership(): void
    {
        $response = $this->get(route('about'));
        $response->assertOk();
        $response->assertSee('Digitising Agriculture, Empowering Farmer', false);
        $response->assertSee('Snehal Talati', false);
        $response->assertSee('Rajesh Talati', false);
        $response->assertSee('Karan Bhatt', false);
        $response->assertSee('Fragmented Landholdings', false);
        $response->assertSee('90,000', false);
    }

    /**
     * Test multilingual support on new routes.
     */
    public function test_solutions_page_localization_in_hindi_and_gujarati(): void
    {
        // Switch to Hindi
        $this->get('/locale/hi')->assertRedirect();
        $response = $this->get(route('home'));
        $response->assertOk();
        $response->assertSee('कृषि को जोड़ना। किसानों को सशक्त बनाना।');

        // Switch to Gujarati
        $this->get('/locale/gu')->assertRedirect();
        $response = $this->get(route('home'));
        $response->assertOk();
        $response->assertSee('ખેતીને જોડવી. ખેડૂતને સક્ષમ બનાવવો.');
    }
}
