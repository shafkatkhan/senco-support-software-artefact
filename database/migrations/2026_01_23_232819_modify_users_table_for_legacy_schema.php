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
            $table->renameColumn('name', 'first_name');
            $table->string('last_name')->after('first_name');

            // Add new columns
            $table->unsignedBigInteger('user_group_id')->after('id');
            $table->string('username')->unique()->after('last_name');
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
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            
            $table->dropColumn('last_name');
            $table->renameColumn('first_name', 'name');

            $table->dropColumn([
                'user_group_id', 
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
