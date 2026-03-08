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
        Schema::dropIfExists('pupil_accommodations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('pupil_accommodations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pupil_id')->constrained()->cascadeOnDelete();
            $table->foreignId('accommodation_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['pupil_id', 'accommodation_id']);
        });
    }
};
