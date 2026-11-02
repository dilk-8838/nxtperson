<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Manufacturers Table
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category'); // fertilizer, seeds, crop-protection, equipment
            $table->string('logo_url')->nullable();
            $table->string('hq_location');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('description')->nullable();
            $table->integer('authorized_dealers_count')->default(100);
            $table->boolean('is_verified')->default(true);
            $table->timestamps();
        });

        // 2. Product Categories Table
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('📦');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 3. Products Table
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manufacturer_id')->constrained()->cascadeOnDelete();
            $table->string('category_slug')->index();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('active_ingredient')->nullable();
            $table->string('package_size')->nullable();
            $table->string('price_guide')->nullable();
            $table->text('description');
            $table->text('usage_instructions')->nullable();
            $table->text('safety_guidelines')->nullable();
            $table->json('target_crops')->nullable(); // ['cotton', 'chilli', 'potato']
            $table->string('image_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Distributors Table
        Schema::create('distributors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('business_name');
            $table->string('license_number');
            $table->string('district')->index();
            $table->string('taluka');
            $table->string('address');
            $table->string('pincode', 6)->index();
            $table->string('phone');
            $table->string('whatsapp')->nullable();
            $table->json('authorized_categories')->nullable();
            $table->json('featured_brands')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Distributor Product Pivot Table
        Schema::create('distributor_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distributor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->boolean('in_stock')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_product');
        Schema::dropIfExists('distributors');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('manufacturers');
    }
};
