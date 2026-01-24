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
        Schema::table('users', function (Blueprint $table) {
            // Rename id -> user_id
            $table->renameColumn('id', 'user_id');

            // Add new columns
            $table->integer('group_id')->after('user_id');
            $table->string('username')->unique()->after('name');
            $table->string('mobile')->after('username');
            $table->string('position')->nullable()->after('mobile');
            $table->integer('added_by')->nullable()->after('position');
            $table->date('joined_date')->nullable()->after('added_by');
            $table->date('expiry_date')->nullable()->after('joined_date');

            // Drop unneeded default columns
            $table->dropColumn(['email', 'email_verified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('user_id', 'id');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            
            $table->dropColumn([
                'group_id', 
                'username', 
                'mobile', 
                'position', 
                'added_by', 
                'joined_date', 
                'expiry_date'
            ]);
        });
    }
};
