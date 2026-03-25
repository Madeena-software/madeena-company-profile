<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => config('auth.filament_admin_email', 'admin@madeena.local')],
            [
                'name' => 'Madeena Super Admin',
                'password' => config('auth.filament_admin_password', 'root'),
                'email_verified_at' => now(),
                'is_admin' => true,
            ]
        );
    }
}
