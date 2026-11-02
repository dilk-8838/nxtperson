<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MegaMenuNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_navigation_configuration_is_loaded_and_complete(): void
    {
        $items = config('navigation.items');
        $this->assertIsArray($items);
        $this->assertNotEmpty($items);

        $ids = array_column($items, 'id');
        $this->assertContains('home', $ids);
        $this->assertContains('solutions', $ids);
        $this->assertContains('farmer-services', $ids);
        $this->assertContains('marketplace', $ids);
        $this->assertContains('about-us', $ids);
        $this->assertCount(5, $ids);
    }

    public function test_all_mega_menu_destinations_resolve_with_ok_status(): void
    {
        // 4 Solutions Pillars & Overview
        $this->get(route('solutions.index'))->assertOk();
        $this->get(route('solutions.quality-grading'))->assertOk();
        $this->get(route('solutions.precision-agri'))->assertOk();
        $this->get(route('solutions.digital-farmer'))->assertOk();
        $this->get(route('solutions.marketplace'))->assertOk();

        // Farmer Services mega menu destinations
        $this->get(route('advisory.index'))->assertOk();
        $this->get(route('advisory.expert'))->assertOk()->assertSee('Connect with Senior Agronomists');
        $this->get(route('advisory.upload'))->assertOk()->assertSee('Upload Crop Disease Photo');
        $this->get(route('weather.index'))->assertOk();
        $this->get(route('weather.stations'))->assertOk()->assertSee('Gujarat Ground Weather Stations');
        $this->get(route('satellite.index'))->assertOk();
        $this->get(route('mandi.index'))->assertOk();

        // Marketplace mega menu destinations
        $this->get(route('marketplace.index'))->assertOk();
        $this->get(route('products.index'))->assertOk();
        $this->get(route('products.index', ['category' => 'seeds']))->assertOk();
        $this->get(route('manufacturers.index'))->assertOk()->assertSee('Manufacturers');
        $this->get(route('manufacturers.index', ['type' => 'seeds']))->assertOk();
        $this->get(route('distributors.index'))->assertOk();
        $this->get(route('distributors.index', ['search' => 'pincode']))->assertOk();

        // Ecosystem & Roadmap
        $this->get(route('ecosystem.index'))->assertOk();
        $this->get(route('roadmap.index'))->assertOk();

        // General routes
        $this->get(route('home'))->assertOk();
        $this->get(route('about'))->assertOk();
    }

    public function test_all_eight_mandatory_crops_resolve_dedicated_profile_pages(): void
    {
        $crops = ['cotton', 'banana', 'potato', 'rice', 'grain', 'tomato', 'cauliflower', 'chilli'];

        foreach ($crops as $slug) {
            $response = $this->get(route('crops.show', ['slug' => $slug]));
            $response->assertOk();
            $response->assertSee('APMC Market Yard Rate');
            $response->assertSee('Optimal Conditions', false);
        }
    }

    public function test_mega_menu_html_contains_proper_aria_attributes_and_links(): void
    {
        $response = $this->get(route('home'));
        $response->assertOk();

        // Trigger buttons for mega menu items
        $response->assertSee('id="menu-btn-solutions"', false);
        $response->assertSee('id="menu-btn-farmer-services"', false);
        $response->assertSee('id="menu-btn-marketplace"', false);

        // Direct links
        $response->assertSee('id="nav-link-home"', false);
        $response->assertSee('id="nav-link-about-us"', false);

        // Header primary CTA
        $response->assertSee('id="header-primary-cta"', false);

        // ARIA attributes
        $response->assertSee('aria-haspopup="true"', false);
        $response->assertSee('aria-expanded="false"', false);

        // Mobile drawer and accordions
        $response->assertSee('id="mobile-drawer"', false);
        $response->assertSee('mobile-accordion-trigger', false);
    }

    public function test_navigation_localization_across_english_hindi_and_gujarati(): void
    {
        // English
        $this->get('/locale/en')->assertRedirect();
        $this->assertEquals('en', session('locale'));
        $this->assertNotEmpty(__('messages.nav.farmer_services'));
        $this->assertNotEmpty(__('messages.nav.solutions'));

        // Hindi
        $this->get('/locale/hi')->assertRedirect();
        $this->assertEquals('hi', session('locale'));
        $this->assertEquals('किसान सेवाएं', __('messages.nav.farmer_services', [], 'hi'));

        // Gujarati
        $this->get('/locale/gu')->assertRedirect();
        $this->assertEquals('gu', session('locale'));
        $this->assertEquals('ખેડૂત સેવાઓ', __('messages.nav.farmer_services', [], 'gu'));
    }
}
