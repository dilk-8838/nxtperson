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
        // 1. FPO Gate Entries
        Schema::create('fpo_gate_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_number', 40)->unique();
            $table->foreignId('fpo_organization_id')->constrained('fpo_organizations')->cascadeOnDelete();
            $table->timestamp('entry_date_time');
            $table->string('gate_point', 50)->default('Main Gate 1');
            $table->string('entry_type', 30)->default('vehicle_delivery'); // 'vehicle_delivery', 'walk_in', 'transfer', 'return'
            $table->string('vehicle_number', 30)->nullable();
            $table->string('driver_name', 100)->nullable();
            $table->string('driver_phone', 20)->nullable();
            $table->string('delivering_party_type', 30)->default('farmer'); // 'farmer', 'supplier', 'transporter', 'other'
            $table->foreignId('farmer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('delivering_party_name', 150);
            $table->string('delivering_party_contact', 50)->nullable();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->foreignId('source_produce_lot_id')->nullable()->constrained('produce_lots')->nullOnDelete();
            $table->foreignId('fpo_collective_listing_id')->nullable()->constrained('fpo_collective_listings')->nullOnDelete();
            $table->string('crop_name', 100);
            $table->string('variety', 100)->nullable();
            $table->decimal('declared_quantity', 12, 2);
            $table->string('declared_unit', 20)->default('kg');
            $table->text('initial_condition')->nullable();
            $table->string('status', 40)->default('registered')->index();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. FPO Gate Entry Produce Line Items (Multi-item delivery support)
        Schema::create('fpo_gate_entry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fpo_gate_entry_id')->constrained('fpo_gate_entries')->cascadeOnDelete();
            $table->string('item_number', 50);
            $table->foreignId('produce_lot_id')->nullable()->constrained('produce_lots')->nullOnDelete();
            $table->string('crop_name', 100);
            $table->string('variety', 100)->nullable();
            $table->decimal('declared_quantity', 12, 2);
            $table->string('declared_unit', 20)->default('kg');
            $table->decimal('measured_net_quantity', 12, 2)->nullable();
            $table->decimal('accepted_quantity', 12, 2)->nullable();
            $table->decimal('rejected_quantity', 12, 2)->nullable();
            $table->decimal('quarantined_quantity', 12, 2)->nullable();
            $table->string('assigned_grade', 50)->nullable();
            $table->string('status', 40)->default('pending')->index();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // 3. Weightment and Weighbridge Records
        Schema::create('fpo_weightments', function (Blueprint $table) {
            $table->id();
            $table->string('weightment_number', 50)->unique();
            $table->foreignId('fpo_gate_entry_id')->constrained('fpo_gate_entries')->cascadeOnDelete();
            $table->foreignId('fpo_gate_entry_item_id')->nullable()->constrained('fpo_gate_entry_items')->nullOnDelete();
            $table->unsignedInteger('sequence_number')->default(1);
            $table->decimal('gross_weight', 12, 2);
            $table->decimal('tare_weight', 12, 2);
            $table->decimal('net_weight', 12, 2);
            $table->string('unit', 20)->default('kg');
            $table->string('weighbridge_reference', 100)->nullable();
            $table->string('operator_name', 100);
            $table->string('measurement_method', 40)->default('manual'); // 'manual', 'automated_weighbridge'
            $table->boolean('is_manual_entry')->default(true);
            $table->boolean('discrepancy_flag')->default(false);
            $table->decimal('discrepancy_percentage', 6, 2)->default(0);
            $table->text('discrepancy_reason')->nullable();
            $table->boolean('discrepancy_resolved')->default(false);
            $table->foreignId('discrepancy_resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('discrepancy_resolution_notes')->nullable();
            $table->boolean('is_accepted')->default(true);
            $table->foreignId('reweighment_of_id')->nullable()->constrained('fpo_weightments')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('measured_at');
            $table->timestamps();
        });

        // 4. Sample Collection
        Schema::create('fpo_samples', function (Blueprint $table) {
            $table->id();
            $table->string('sample_number', 50)->unique();
            $table->foreignId('fpo_gate_entry_id')->constrained('fpo_gate_entries')->cascadeOnDelete();
            $table->foreignId('fpo_gate_entry_item_id')->nullable()->constrained('fpo_gate_entry_items')->nullOnDelete();
            $table->string('crop_name', 100);
            $table->string('variety', 100)->nullable();
            $table->timestamp('sample_date_time');
            $table->foreignId('collected_by')->constrained('users')->cascadeOnDelete();
            $table->string('collector_name', 100);
            $table->string('sampling_method', 60)->default('Manual Composite Sampling');
            $table->decimal('sample_quantity', 8, 2);
            $table->string('sample_unit', 20)->default('kg');
            $table->string('container_seal_number', 50)->nullable();
            $table->text('condition_observations')->nullable();
            $table->string('destination', 100)->default('On-site Quality Testing Lab');
            $table->string('status', 30)->default('collected')->index();
            $table->string('attachment_path', 255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Quality Inspection and Laboratory Tests
        Schema::create('fpo_quality_tests', function (Blueprint $table) {
            $table->id();
            $table->string('test_number', 50)->unique();
            $table->foreignId('fpo_sample_id')->constrained('fpo_samples')->cascadeOnDelete();
            $table->foreignId('fpo_gate_entry_item_id')->nullable()->constrained('fpo_gate_entry_items')->nullOnDelete();
            $table->string('test_type', 50); // 'moisture', 'foreign_matter', 'grain_size', 'damage_spoilage', 'visual_condition'
            $table->string('parameter_name', 100);
            $table->decimal('measured_value', 8, 2);
            $table->string('unit', 30)->default('%');
            $table->decimal('min_acceptable_value', 8, 2)->nullable();
            $table->decimal('max_acceptable_value', 8, 2)->nullable();
            $table->string('outcome', 20)->default('pass'); // 'pass', 'fail', 'conditional'
            $table->foreignId('tested_by')->constrained('users')->cascadeOnDelete();
            $table->string('tester_name', 100);
            $table->timestamp('tested_at');
            $table->text('notes')->nullable();
            $table->boolean('is_superseded')->default(false);
            $table->foreignId('superseded_by_id')->nullable()->constrained('fpo_quality_tests')->nullOnDelete();
            $table->timestamps();
        });

        // 6. Quality Approvals and Sign-off
        Schema::create('fpo_quality_approvals', function (Blueprint $table) {
            $table->id();
            $table->string('approval_number', 50)->unique();
            $table->foreignId('fpo_gate_entry_id')->constrained('fpo_gate_entries')->cascadeOnDelete();
            $table->foreignId('fpo_gate_entry_item_id')->nullable()->constrained('fpo_gate_entry_items')->nullOnDelete();
            $table->foreignId('approver_id')->constrained('users')->cascadeOnDelete();
            $table->string('approver_name', 100);
            $table->string('approver_role', 50);
            $table->string('decision', 30)->index(); // 'approved', 'rejected', 'partially_accepted', 'quarantined'
            $table->timestamp('decision_at');
            $table->decimal('accepted_quantity', 12, 2)->default(0);
            $table->decimal('rejected_quantity', 12, 2)->default(0);
            $table->decimal('quarantined_quantity', 12, 2)->default(0);
            $table->string('assigned_grade', 50)->nullable();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('signature_attestation')->nullable();
            $table->string('signature_image_path', 255)->nullable();
            $table->timestamps();
        });

        // 7. Inventory Posting and Stock Reconciliation
        Schema::create('fpo_inventory_postings', function (Blueprint $table) {
            $table->id();
            $table->string('posting_number', 50)->unique();
            $table->foreignId('fpo_organization_id')->constrained('fpo_organizations')->cascadeOnDelete();
            $table->foreignId('fpo_gate_entry_id')->constrained('fpo_gate_entries')->cascadeOnDelete();
            $table->foreignId('fpo_gate_entry_item_id')->nullable()->constrained('fpo_gate_entry_items')->nullOnDelete();
            $table->foreignId('produce_lot_id')->nullable()->constrained('produce_lots')->nullOnDelete();
            $table->foreignId('fpo_collective_listing_id')->nullable()->constrained('fpo_collective_listings')->nullOnDelete();
            $table->decimal('posted_quantity_kg', 12, 2);
            $table->decimal('source_stock_before_kg', 12, 2)->default(0);
            $table->decimal('source_stock_after_kg', 12, 2)->default(0);
            $table->string('status', 30)->default('posted')->index(); // 'posted', 'reversed'
            $table->foreignId('posted_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('posted_at');
            $table->text('reversal_reason')->nullable();
            $table->foreignId('reversed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reversed_at')->nullable();
            $table->string('idempotency_key', 80)->unique();
            $table->timestamps();
        });

        // 8. Gate Exit and Dispatch
        Schema::create('fpo_gate_exits', function (Blueprint $table) {
            $table->id();
            $table->string('exit_number', 50)->unique();
            $table->foreignId('fpo_organization_id')->constrained('fpo_organizations')->cascadeOnDelete();
            $table->foreignId('fpo_gate_entry_id')->nullable()->constrained('fpo_gate_entries')->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->string('movement_type', 40)->default('order_dispatch'); // 'order_dispatch', 'transfer', 'return_rejected', 'empty_exit'
            $table->string('vehicle_number', 30)->nullable();
            $table->string('driver_name', 100)->nullable();
            $table->string('driver_phone', 20)->nullable();
            $table->string('destination', 200)->nullable();
            $table->decimal('dispatched_quantity', 12, 2)->default(0);
            $table->string('unit', 20)->default('kg');
            $table->timestamp('exit_date_time');
            $table->foreignId('authorized_by')->constrained('users')->cascadeOnDelete();
            $table->string('authorizer_name', 100);
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->string('status', 30)->default('authorized')->index(); // 'authorized', 'exited', 'cancelled'
            $table->timestamp('actual_exit_at')->nullable();
            $table->timestamp('handover_confirmed_at')->nullable();
            $table->foreignId('handover_confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('handover_notes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 9. Operational Exceptions
        Schema::create('fpo_operational_exceptions', function (Blueprint $table) {
            $table->id();
            $table->string('exception_number', 50)->unique();
            $table->foreignId('fpo_organization_id')->constrained('fpo_organizations')->cascadeOnDelete();
            $table->foreignId('fpo_gate_entry_id')->nullable()->constrained('fpo_gate_entries')->nullOnDelete();
            $table->foreignId('fpo_gate_entry_item_id')->nullable()->constrained('fpo_gate_entry_items')->nullOnDelete();
            $table->string('exception_type', 50)->index(); // 'weight_discrepancy', 'quality_failure', 'missing_sample', 'stock_mismatch', 'quantity_mismatch', 'unauthorized_exit'
            $table->string('severity', 20)->default('medium'); // 'low', 'medium', 'critical'
            $table->string('title', 150);
            $table->text('description');
            $table->string('status', 30)->default('open')->index(); // 'open', 'under_review', 'resolved', 'closed', 'reopened'
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution_action')->nullable();
            $table->text('resolution_reason')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        // 10. Audit Logs (Tamper-evident, append-only operational history)
        Schema::create('fpo_operation_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fpo_organization_id')->constrained('fpo_organizations')->cascadeOnDelete();
            $table->foreignId('fpo_gate_entry_id')->nullable()->constrained('fpo_gate_entries')->nullOnDelete();
            $table->string('entity_type', 60);
            $table->unsignedBigInteger('entity_id');
            $table->string('action', 60);
            $table->string('from_state', 60)->nullable();
            $table->string('to_state', 60)->nullable();
            $table->foreignId('actor_id')->constrained('users')->cascadeOnDelete();
            $table->string('actor_name', 100);
            $table->string('actor_role', 50);
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fpo_operation_audit_logs');
        Schema::dropIfExists('fpo_operational_exceptions');
        Schema::dropIfExists('fpo_gate_exits');
        Schema::dropIfExists('fpo_inventory_postings');
        Schema::dropIfExists('fpo_quality_approvals');
        Schema::dropIfExists('fpo_quality_tests');
        Schema::dropIfExists('fpo_samples');
        Schema::dropIfExists('fpo_weightments');
        Schema::dropIfExists('fpo_gate_entry_items');
        Schema::dropIfExists('fpo_gate_entries');
    }
};
