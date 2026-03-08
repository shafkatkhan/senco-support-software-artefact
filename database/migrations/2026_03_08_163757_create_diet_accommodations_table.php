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
        Schema::create('diet_accommodations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('accommodation_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['Recommended', 'Approved']);
            $table->text('details')->nullable();
            $table->timestamps();
            
            $table->unique(['diet_id', 'accommodation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diet_accommodations');
    }
};
