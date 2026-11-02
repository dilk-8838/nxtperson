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
        // 1. Additive columns for existing Produce Lots (FPO Aggregation tracking)
        Schema::table('produce_lots', function (Blueprint $table) {
            $table->decimal('aggregated_quantity_kg', 12, 2)->default(0)->after('sold_quantity_kg');
        });

        // 2. Additive columns for existing Products (Supplier Organization & Inventory management)
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('supplier_organization_id')->nullable()->after('manufacturer_id')->constrained('supplier_organizations')->nullOnDelete();
            $table->string('unit', 50)->default('unit')->after('package_size');
            $table->decimal('unit_price', 10, 2)->nullable()->after('price_guide');
            $table->decimal('min_order_quantity', 10, 2)->default(1)->after('unit_price');
            $table->decimal('stock_quantity', 12, 2)->default(100)->after('min_order_quantity');
            $table->boolean('is_available')->default(true)->after('is_active');
            $table->text('specifications')->nullable()->after('description');
            $table->string('brand_name')->nullable()->after('name');
        });

        // 3. Buyer Bulk Procurement Requirements
        Schema::create('procurement_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('requirement_number')->unique();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 30)->default('produce'); // 'produce' or 'input'
            $table->string('title');
            $table->string('item_name');
            $table->string('crop_slug')->nullable()->index();
            $table->string('category_slug')->nullable()->index();
            $table->decimal('target_quantity', 12, 2);
            $table->string('unit', 30)->default('kg');
            $table->decimal('target_price_per_unit', 10, 2)->nullable();
            $table->string('delivery_district');
            $table->string('delivery_address')->nullable();
            $table->date('desired_delivery_date')->nullable();
            $table->text('quality_specifications')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('open')->index(); // 'open', 'rfq_created', 'fulfilled', 'closed', 'cancelled'
            $table->timestamps();
        });

        // 4. FPO Collective Bulk Produce Listings
        Schema::create('fpo_collective_listings', function (Blueprint $table) {
            $table->id();
            $table->string('listing_number')->unique();
            $table->foreignId('fpo_organization_id')->constrained('fpo_organizations')->cascadeOnDelete();
            $table->string('crop_slug')->index();
            $table->string('variety');
            $table->string('title');
            $table->decimal('total_aggregated_quantity_kg', 12, 2)->default(0);
            $table->decimal('available_quantity_kg', 12, 2)->default(0)->index();
            $table->decimal('reserved_quantity_kg', 12, 2)->default(0);
            $table->decimal('sold_quantity_kg', 12, 2)->default(0);
            $table->decimal('asking_price_per_kg', 10, 2)->nullable();
            $table->string('approved_grade', 40)->default('Certified FPO Bulk');
            $table->string('district');
            $table->string('taluka')->nullable();
            $table->string('pincode', 6);
            $table->string('harvest_period')->nullable();
            $table->text('description')->nullable();
            $table->string('status', 30)->default('published')->index(); // 'draft', 'published', 'partially_sold', 'sold_out', 'withdrawn'
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // 5. Traceable FPO Member Produce Aggregation Linkages
        Schema::create('fpo_member_aggregations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fpo_organization_id')->constrained('fpo_organizations')->cascadeOnDelete();
            $table->foreignId('fpo_collective_listing_id')->nullable()->constrained('fpo_collective_listings')->nullOnDelete();
            $table->foreignId('farmer_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('produce_lot_id')->constrained('produce_lots')->cascadeOnDelete();
            $table->decimal('committed_quantity_kg', 12, 2);
            $table->string('status', 30)->default('pooled'); // 'committed', 'pooled', 'sold', 'released'
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 6. Request for Quotations (RFQs)
        Schema::create('rfqs', function (Blueprint $table) {
            $table->id();
            $table->string('rfq_number')->unique();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('procurement_requirement_id')->nullable()->constrained('procurement_requirements')->nullOnDelete();
            $table->string('item_type', 30)->default('produce'); // 'produce', 'supplier_product', 'collective_produce'
            $table->foreignId('produce_lot_id')->nullable()->constrained('produce_lots')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('fpo_collective_listing_id')->nullable()->constrained('fpo_collective_listings')->nullOnDelete();
            $table->string('title');
            $table->decimal('quantity', 12, 2);
            $table->string('unit', 30)->default('kg');
            $table->decimal('target_price_per_unit', 10, 2)->nullable();
            $table->string('delivery_district');
            $table->text('delivery_address')->nullable();
            $table->date('target_delivery_date');
            $table->text('specifications')->nullable();
            $table->string('status', 30)->default('open')->index(); // 'open', 'quotations_received', 'partially_accepted', 'accepted', 'closed', 'cancelled'
            $table->timestamp('closes_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 7. RFQ Recipient Invitations
        Schema::create('rfq_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rfq_id')->constrained('rfqs')->cascadeOnDelete();
            $table->string('recipient_type', 30); // 'farmer', 'supplier', 'fpo'
            $table->foreignId('recipient_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('supplier_organization_id')->nullable()->constrained('supplier_organizations')->nullOnDelete();
            $table->foreignId('fpo_organization_id')->nullable()->constrained('fpo_organizations')->nullOnDelete();
            $table->string('status', 30)->default('invited'); // 'invited', 'viewed', 'quoted', 'declined'
            $table->timestamps();
        });

        // 8. Quotations Submitted by Sellers
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique();
            $table->foreignId('rfq_id')->constrained('rfqs')->cascadeOnDelete();
            $table->foreignId('seller_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('seller_type', 30); // 'farmer', 'supplier', 'fpo'
            $table->foreignId('supplier_organization_id')->nullable()->constrained('supplier_organizations')->nullOnDelete();
            $table->foreignId('fpo_organization_id')->nullable()->constrained('fpo_organizations')->nullOnDelete();
            $table->foreignId('produce_lot_id')->nullable()->constrained('produce_lots')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('fpo_collective_listing_id')->nullable()->constrained('fpo_collective_listings')->nullOnDelete();
            $table->decimal('quoted_quantity', 12, 2);
            $table->string('unit', 30)->default('kg');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('additional_charges', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 12, 2); // Calculated server-side
            $table->date('estimated_delivery_date');
            $table->date('valid_until');
            $table->string('status', 30)->default('pending')->index(); // 'pending', 'accepted', 'declined', 'withdrawn', 'expired'
            $table->text('quality_notes')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->timestamps();
        });

        // 9. Persisted Purchase Orders
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->foreignId('quotation_id')->nullable()->constrained('quotations')->nullOnDelete();
            $table->foreignId('rfq_id')->nullable()->constrained('rfqs')->nullOnDelete();
            $table->foreignId('produce_reservation_id')->nullable()->constrained('produce_reservations')->nullOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('seller_type', 30); // 'farmer', 'supplier', 'fpo'
            $table->foreignId('supplier_organization_id')->nullable()->constrained('supplier_organizations')->nullOnDelete();
            $table->foreignId('fpo_organization_id')->nullable()->constrained('fpo_organizations')->nullOnDelete();
            $table->string('item_type', 30)->default('produce'); // 'produce', 'supplier_product', 'collective_produce'
            $table->foreignId('produce_lot_id')->nullable()->constrained('produce_lots')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('fpo_collective_listing_id')->nullable()->constrained('fpo_collective_listings')->nullOnDelete();
            $table->string('item_description');
            $table->decimal('quantity', 12, 2);
            $table->string('unit', 30)->default('kg');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('additional_charges', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->text('delivery_address');
            $table->string('delivery_district');
            $table->date('estimated_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->string('status', 40)->default('pending_confirmation')->index(); // 'pending_confirmation', 'confirmed', 'processing', 'ready_for_dispatch', 'dispatched', 'delivered', 'cancelled'
            $table->string('payment_status', 30)->default('unpaid')->index(); // 'unpaid', 'payment_pending', 'escrow_held', 'paid_offline', 'refunded'
            $table->string('payment_terms')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 10. Purchase Order Status Audit Logs
        Schema::create('purchase_order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->string('from_status', 40);
            $table->string('to_status', 40);
            $table->foreignId('actor_id')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_status_logs');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('rfq_invitations');
        Schema::dropIfExists('rfqs');
        Schema::dropIfExists('fpo_member_aggregations');
        Schema::dropIfExists('fpo_collective_listings');
        Schema::dropIfExists('procurement_requirements');

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['supplier_organization_id']);
            $table->dropColumn([
                'supplier_organization_id',
                'unit',
                'unit_price',
                'min_order_quantity',
                'stock_quantity',
                'is_available',
                'specifications',
                'brand_name',
            ]);
        });

        Schema::table('produce_lots', function (Blueprint $table) {
            $table->dropColumn('aggregated_quantity_kg');
        });
    }
};
