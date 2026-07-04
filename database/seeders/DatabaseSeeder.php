<?php

namespace Database\Seeders;

use App\Models\User;
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

        User::factory()->create([
            'name' => 'Shama Admin',
            'username' => 'admin',
            'email' => 'admin@shamasms.com',
            'sms_balance' => 1000,
            'sms_unit_price' => 35,
            'is_admin' => true,
        ]);
    }
}
