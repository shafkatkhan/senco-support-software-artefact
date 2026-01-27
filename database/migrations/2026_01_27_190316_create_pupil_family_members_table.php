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
        Schema::create('pupil_family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pupil_id')->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob');
            $table->string('relation');
            $table->timestamps();
        });

        Schema::table('pupils', function (Blueprint $table) {
            $table->foreign('primary_family_member_id')->references('id')->on('pupil_family_members')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pupil_family_members');
    }
};
