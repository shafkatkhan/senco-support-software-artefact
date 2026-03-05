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
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pupil_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('dosage')->nullable();
            $table->string('frequency');
            $table->string('time_of_day')->nullable();
            $table->string('administration_method')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('storage_instructions')->nullable();
            $table->boolean('self_administer')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
