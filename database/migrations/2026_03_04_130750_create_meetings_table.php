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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pupil_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meeting_type_id')->constrained()->cascadeOnDelete();
            $table->date('date')->nullable();
            $table->string('title');
            $table->text('participants')->nullable();
            $table->text('discussion')->nullable();
            $table->text('recommendations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
