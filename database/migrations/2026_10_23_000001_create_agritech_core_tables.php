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
        // 1. Crops Table (8 Key Gujarat Crops)
        Schema::create('crops', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('en_name');
            $table->string('hi_name');
            $table->string('gu_name');
            $table->string('botanical_name')->nullable();
            $table->string('category');
            $table->string('season');
            $table->string('icon', 10)->default('🌾');
            $table->string('badge')->nullable();
            $table->string('optimal_temp');
            $table->json('pests')->nullable();
            $table->string('mandi_price')->nullable();
            $table->string('mandi_trend')->nullable();
            $table->string('mandi_yard')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Gujarat APMC Mandi Prices Table
        Schema::create('mandi_prices', function (Blueprint $table) {
            $table->id();
            $table->string('crop_slug')->index();
            $table->string('commodity_name');
            $table->string('variety')->nullable();
            $table->string('yard_name')->index();
            $table->string('district');
            $table->string('state')->default('Gujarat');
            $table->string('arrival_quantity')->nullable();
            $table->decimal('min_price', 10, 2);
            $table->decimal('modal_price', 10, 2);
            $table->decimal('max_price', 10, 2);
            $table->string('price_unit')->default('₹ / Quintal');
            $table->date('price_date');
            $table->string('trend_percentage')->nullable();
            $table->boolean('is_sample')->default(true);
            $table->timestamps();
        });

        // 3. Weather Snapshots Table (Gujarat Agro Zones)
        Schema::create('weather_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('district')->index();
            $table->string('zone_name');
            $table->decimal('temperature', 5, 2);
            $table->string('condition');
            $table->integer('humidity');
            $table->integer('wind_speed');
            $table->string('wind_direction')->default('NW');
            $table->integer('rainfall_probability');
            $table->string('spray_window');
            $table->string('soil_moisture')->nullable();
            $table->string('heat_index')->nullable();
            $table->json('forecast_json')->nullable();
            $table->boolean('is_sample')->default(true);
            $table->timestamps();
        });

        // 4. Advisories & AI Diagnostics Table
        Schema::create('advisories', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('crop_slug')->index();
            $table->string('farmer_name');
            $table->string('farmer_phone')->nullable();
            $table->string('farmer_district');
            $table->text('issue_description');
            $table->string('image_path')->nullable();
            $table->string('symptom_key')->nullable();
            $table->string('ai_diagnosis_title');
            $table->text('ai_diagnosis_description');
            $table->text('ai_recommended_solution');
            $table->string('ai_confidence')->default('90%');
            $table->string('status')->default('ai_analyzed'); // ai_analyzed, escalated, resolved
            $table->string('assigned_agronomist')->nullable();
            $table->text('agronomist_notes')->nullable();
            $table->boolean('is_demonstration')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advisories');
        Schema::dropIfExists('weather_snapshots');
        Schema::dropIfExists('mandi_prices');
        Schema::dropIfExists('crops');
    }
};
