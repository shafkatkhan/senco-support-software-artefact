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
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('after_school_job')->nullable();
            
            $table->boolean('has_special_needs')->default(false);
            $table->text('special_needs_details')->nullable();
            
            $table->boolean('attended_special_school')->default(false);
            $table->text('special_school_details')->nullable();
            
            $table->text('parental_description')->nullable();
            
            $table->boolean('social_services_involvement')->default(false);
            $table->boolean('probation_officer_required')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pupils', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'email',
                'after_school_job',
                'has_special_needs',
                'special_needs_details',
                'attended_special_school',
                'special_school_details',
                'parental_description',
                'social_services_involvement',
                'probation_officer_required',
            ]);
        });
    }
};
