<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserGroup;
use App\Models\TestForm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // create groups
        $adminGroup = UserGroup::create([
            'name' => 'Admin',
            'description' => 'Full access to all system features.'
        ]);
        $standardGroup = UserGroup::create([
            'name' => 'Standard',
            'description' => 'Regular access to standard features.'
        ]);
        $readOnlyGroup = UserGroup::create([
            'name' => 'Read-Only',
            'description' => 'Can view data but cannot make changes.'
        ]);

        // create admin user
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'user_group_id' => $adminGroup->id,
            'mobile' => '07777777777',
            'position' => 'Head Teacher',
            'joined_date' => now(),
        ]);

        // create 10 standard users
        User::factory(10)->create([
            'user_group_id' => $standardGroup->id,
        ]);

        // create 10 read-only users
        User::factory(10)->create([
            'user_group_id' => $readOnlyGroup->id,
        ]);

        // create test rows
        TestForm::factory(20)->create();
    }
}
