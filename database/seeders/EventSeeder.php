<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::updateOrCreate(
            ['slug' => 'inabuyer-2026'],
            [
                'name' => 'Inabuyer 2026',
                'description' => 'Inabuyer 2026 Event',
                'is_active' => true,
            ]
        );
    }
}
