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
        Schema::table('family_members', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('locality')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('highest_education')->nullable();
            $table->string('financial_status')->nullable();
            $table->string('occupation')->nullable();
            $table->string('state_support')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('family_members', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'email',
                'address_line_1',
                'address_line_2',
                'locality',
                'postcode',
                'country',
                'marital_status',
                'highest_education',
                'financial_status',
                'occupation',
                'state_support',
            ]);
        });
    }
};
