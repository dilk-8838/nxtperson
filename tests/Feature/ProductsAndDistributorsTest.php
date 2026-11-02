<?php

namespace Tests\Feature;

use App\Models\Distributor;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductsAndDistributorsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test products catalog page loads with active items and no-checkout notice.
     */
    public function test_products_catalog_loads_successfully(): void
    {
        $response = $this->get('/products');

        $response->assertStatus(200);
        $response->assertSee('Certified Agricultural Inputs');
        $response->assertSee('No Direct Checkout Policy');
        $response->assertSee('View Stockists');
    }

    /**
     * Test filtering products by category.
     */
    public function test_products_can_be_filtered_by_category(): void
    {
        $response = $this->get('/products?category=seeds');

        $response->assertStatus(200);
        $response->assertSee('Gurjari Certified Shankar-6 Bt-Cotton Seeds');
    }

    /**
     * Test filtering products by crop association.
     */
    public function test_products_can_be_filtered_by_target_crop(): void
    {
        $response = $this->get('/products?crop=banana');

        $response->assertStatus(200);
        $response->assertSee('Grand Naine Tissue Culture Banana Plantlets');
    }

    /**
     * Test search for product by name or active ingredient.
     */
    public function test_products_can_be_searched_by_keyword(): void
    {
        $response = $this->get('/products?search=Urea');

        $response->assertStatus(200);
        $response->assertSee('Sardar Urea 46% Prilled Nitrogen Fertilizer');
    }

    /**
     * Test viewing a product detail page with linked distributors.
     */
    public function test_product_detail_page_displays_specifications_and_distributors(): void
    {
        $response = $this->get('/products/sardar-urea-46-n');

        $response->assertStatus(200);
        $response->assertSee('Sardar Urea 46% Prilled Nitrogen Fertilizer');
        $response->assertSee('Gujarat State Fertilizers');
        $response->assertSee('46% Total Nitrogen');
        $response->assertSee('Authorized Dealers');
    }

    /**
     * Test distributor directory loads and displays 6-digit Gujarat pincode search.
     */
    public function test_distributor_directory_search_by_six_digit_pincode(): void
    {
        // 360001 is Rajkot Kisan Seva Kendra
        $response = $this->get('/distributors?pincode=360001');

        $response->assertStatus(200);
        $response->assertSee('Kisan Seva Kendra');
        $response->assertSee('360001');
    }

    /**
     * Test distributor directory filter by district.
     */
    public function test_distributor_directory_filter_by_district(): void
    {
        $response = $this->get('/distributors?district=Anand');

        $response->assertStatus(200);
        $response->assertSee('Charotar Agro Chemical');
        $response->assertSee('388001');
    }
}
