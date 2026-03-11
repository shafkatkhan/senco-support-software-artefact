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
        Schema::table('pupils', function (Blueprint $table) {
            $table->foreignId('social_services_professional_id')->nullable()->constrained('professionals')->nullOnDelete();
            $table->foreignId('probation_officer_professional_id')->nullable()->constrained('professionals')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pupils', function (Blueprint $table) {
            $table->dropForeign(['social_services_professional_id']);
            $table->dropForeign(['probation_officer_professional_id']);
            $table->dropColumn(['social_services_professional_id', 'probation_officer_professional_id']);
        });
    }
};
