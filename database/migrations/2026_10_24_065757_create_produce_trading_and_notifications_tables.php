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
        // 1. Produce Lots Table (Farmer Harvest Submissions & Inventory)
        Schema::create('produce_lots', function (Blueprint $table) {
            $table->id();
            $table->string('lot_number')->unique();
            $table->foreignId('farmer_id')->constrained('users')->cascadeOnDelete();
            $table->string('crop_slug')->index();
            $table->string('variety');
            $table->decimal('quantity', 10, 2);
            $table->string('unit', 20)->default('kg'); // kg, quintal, tonne
            $table->decimal('quantity_in_kg', 12, 2)->index(); // Normalized to KG for calculations
            $table->string('district')->index();
            $table->string('taluka')->nullable();
            $table->string('pincode', 6)->index();
            $table->date('harvest_date');
            $table->decimal('expected_price_per_kg', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('farming_method')->default('conventional'); // conventional, organic, natural, ipm

            // Workflow state machine
            // draft -> submitted -> assessed -> under_review -> approved_for_procurement
            // -> purchase_confirmed -> inventory_available -> partially_sold -> sold_out
            // (also rejected, cancelled)
            $table->string('status', 40)->default('submitted')->index();
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();

            // Grading & Pricing Approvals
            $table->string('approved_grade', 20)->nullable(); // Good, Intermediate, Bad
            $table->decimal('approved_procurement_price_per_kg', 10, 2)->nullable();
            $table->decimal('buyer_sale_price_per_kg', 10, 2)->nullable();

            // Inventory quantities (strictly tracked)
            $table->decimal('confirmed_quantity_kg', 12, 2)->default(0);
            $table->decimal('available_quantity_kg', 12, 2)->default(0)->index();
            $table->decimal('reserved_quantity_kg', 12, 2)->default(0);
            $table->decimal('sold_quantity_kg', 12, 2)->default(0);

            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // 2. Produce Lot Images Table
        Schema::create('produce_lot_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produce_lot_id')->constrained('produce_lots')->cascadeOnDelete();
            $table->string('image_path');
            $table->string('original_name')->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->string('mime_type', 50)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_approved_for_buyers')->default(true);
            $table->timestamps();
        });

        // 3. Produce Quality Assessments History Table
        Schema::create('produce_grading_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produce_lot_id')->constrained('produce_lots')->cascadeOnDelete();
            $table->string('grade', 20); // Good, Intermediate, Bad
            $table->json('observed_defects')->nullable();
            $table->text('assessment_explanation');
            $table->string('confidence_score')->default('90% (Demonstration Heuristic)');
            $table->boolean('manual_verification_required')->default(true);
            $table->string('grading_method')->default('Demonstration Rule-Engine v1.0.0-sample');
            $table->string('engine_version')->default('v1.0.0-sample');
            $table->foreignId('assessed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_admin_override')->default(false);
            $table->text('override_notes')->nullable();
            $table->boolean('is_demonstration')->default(true);
            $table->timestamps();
        });

        // 4. Produce Buyer Reservations Table
        Schema::create('produce_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_number')->unique();
            $table->foreignId('produce_lot_id')->constrained('produce_lots')->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('quantity_kg', 12, 2);
            $table->decimal('price_per_kg', 10, 2);
            $table->decimal('total_price', 12, 2);
            $table->string('status', 30)->default('confirmed'); // requested, confirmed, fulfilled, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Standard Laravel Notifications Table (if not exists)
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produce_reservations');
        Schema::dropIfExists('produce_grading_assessments');
        Schema::dropIfExists('produce_lot_images');
        Schema::dropIfExists('produce_lots');
        Schema::dropIfExists('notifications');
    }
};
