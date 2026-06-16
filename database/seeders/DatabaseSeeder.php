<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            PageSeeder::class,
            ProductSeeder::class,
            PostSeeder::class,
            SettingSeeder::class,
            EventSeeder::class,
            GuestMessageSeeder::class,
        ]);
    }
}
