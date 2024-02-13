<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Admin::where('email', 'superadmin@example.net')->first();
        if (!$admin) {
            Admin::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@example.net',
                'password' => bcrypt('1234567890'),
            ]);
        }
    }
}
