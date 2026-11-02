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
        // 1. Add status and rejection_reason to users table if not exists
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'status')) {
                $table->string('status', 20)->default('active')->index()->after('role');
            }
            if (! Schema::hasColumn('users', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('status');
            }
        });

        // 2. Farmer Profiles Table
        Schema::create('farmer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('farm_name')->nullable();
            $table->string('village')->nullable();
            $table->string('taluka')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->default('Gujarat');
            $table->string('pincode', 10)->nullable();
            $table->decimal('total_acreage', 8, 2)->default(0);
            $table->decimal('cultivated_acreage', 8, 2)->default(0);
            $table->string('soil_type')->nullable();
            $table->string('irrigation_type')->nullable(); // borewell, canal, drip, sprinkler, rainfed
            $table->json('crops_cultivated')->nullable(); // e.g. ["cotton", "banana", "potato"]
            $table->date('expected_harvest_date')->nullable();
            $table->decimal('estimated_harvest_quantity', 10, 2)->nullable();
            $table->string('estimated_harvest_unit', 20)->default('Quintal');
            $table->string('kisan_credit_card')->nullable();
            $table->string('survey_number')->nullable();
            $table->string('verification_status', 20)->default('verified')->index(); // pending, verified, rejected
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        // 3. Supplier Organizations Table
        Schema::create('supplier_organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('slug')->unique();
            $table->string('category', 50)->default('seeds'); // seeds, fertilizer, crop_protection, equipment, multi_category
            $table->text('description')->nullable();
            $table->string('gst_number', 30)->nullable();
            $table->string('license_number', 50)->nullable();
            $table->string('authorized_representative_name');
            $table->string('contact_email');
            $table->string('contact_phone', 20);
            $table->string('operating_address');
            $table->string('district', 100);
            $table->string('state', 100)->default('Gujarat');
            $table->string('pincode', 10)->nullable();
            $table->string('document_path')->nullable();
            $table->string('verification_status', 20)->default('pending')->index(); // pending, approved, rejected, suspended
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        // 4. Supplier Organization Members Pivot Table
        Schema::create('supplier_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_organization_id')->constrained('supplier_organizations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role_in_org', 30)->default('owner'); // owner, manager, sales_rep
            $table->boolean('is_primary_contact')->default(false);
            $table->timestamps();

            $table->unique(['supplier_organization_id', 'user_id']);
        });

        // 5. Buyer Profiles Table
        Schema::create('buyer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('company_name')->nullable();
            $table->string('buyer_type', 40)->default('individual'); // individual, processor, exporter, wholesaler, retail_chain
            $table->string('gst_number', 30)->nullable();
            $table->string('pan_number', 20)->nullable();
            $table->string('trade_license', 50)->nullable();
            $table->json('procurement_interests')->nullable();
            $table->string('annual_procurement_budget', 50)->nullable();
            $table->string('operating_address')->nullable();
            $table->string('district', 100)->nullable();
            $table->string('state', 100)->default('Gujarat');
            $table->string('pincode', 10)->nullable();
            $table->string('verification_status', 20)->default('verified')->index(); // pending, verified, rejected, suspended
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 6. FPO / Cooperative Organizations Table
        Schema::create('fpo_organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('slug')->unique();
            $table->string('organization_type', 60)->default('Farmer Producer Company (FPC)');
            $table->string('registration_number', 50); // CIN or Society Registration Number
            $table->unsignedSmallInteger('incorporation_year')->nullable();
            $table->text('description')->nullable();
            $table->string('registered_address');
            $table->string('village', 100)->nullable();
            $table->string('taluka', 100)->nullable();
            $table->string('district', 100);
            $table->string('state', 100)->default('Gujarat');
            $table->string('pincode', 10)->nullable();
            $table->string('authorized_representative_name');
            $table->string('contact_email');
            $table->string('contact_phone', 20);
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('ifsc_code', 20)->nullable();
            $table->string('document_path')->nullable();
            $table->string('verification_status', 20)->default('pending')->index(); // pending, approved, rejected, suspended
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        // 7. FPO Authorized Representatives / Staff Table
        Schema::create('fpo_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fpo_organization_id')->constrained('fpo_organizations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role_in_fpo', 30)->default('admin'); // admin, board_director, ceo, staff
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['fpo_organization_id', 'user_id']);
        });

        // 8. FPO Farmer Memberships Table (Links Farmers to FPOs without duplicating profile)
        Schema::create('fpo_farmer_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fpo_organization_id')->constrained('fpo_organizations')->cascadeOnDelete();
            $table->foreignId('farmer_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('member_code', 30)->nullable();
            $table->unsignedInteger('share_units')->default(1);
            $table->string('status', 20)->default('active')->index(); // active, pending, inactive
            $table->date('joined_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['fpo_organization_id', 'farmer_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fpo_farmer_memberships');
        Schema::dropIfExists('fpo_members');
        Schema::dropIfExists('fpo_organizations');
        Schema::dropIfExists('buyer_profiles');
        Schema::dropIfExists('supplier_members');
        Schema::dropIfExists('supplier_organizations');
        Schema::dropIfExists('farmer_profiles');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
