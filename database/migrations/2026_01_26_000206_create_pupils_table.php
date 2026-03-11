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
        Schema::create('pupils', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob');
            $table->string('gender');
            $table->unsignedBigInteger('primary_family_member_id')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('locality')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->nullable();
            $table->foreignId('onboarded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('joined_date')->nullable();
            $table->string('initial_tutor_group')->nullable();
            $table->boolean('smoking_history')->default(false);
            $table->boolean('drug_abuse_history')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pupils');
    }
};
