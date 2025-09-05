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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('tin_vat_number')->nullable()->after('certification_proof');
            $table->string('business_license')->nullable()->after('tin_vat_number');
            $table->string('tax_certificate')->nullable()->after('business_license');
            $table->string('owner_id')->nullable()->after('tax_certificate');
            $table->string('registration_certificate')->nullable()->after('owner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'tin_vat_number',
                'business_license',
                'tax_certificate',
                'owner_id',
                'registration_certificate'
            ]);
        });
    }
};
