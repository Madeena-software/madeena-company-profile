<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('FILAMENT_ADMIN_EMAIL', 'admin@madeena.local')],
            [
                'name' => 'Madeena Super Admin',
                'password' => env('FILAMENT_ADMIN_PASSWORD', 'root'),
                'email_verified_at' => now(),
            ]
        );
    }
}
