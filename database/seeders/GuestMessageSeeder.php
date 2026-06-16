<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuestMessageSeeder extends Seeder
{
    public function run(): void
    {
        $event = Event::where('slug', 'inabuyer-2026')->first();
        if (!$event) {
            return;
        }

        // If the table is empty and we have a seed file, seed it!
        if (DB::table('guest_messages')->count() === 0 && file_exists(__DIR__ . '/guest_messages.sql')) {
            DB::unprepared("
                CREATE TEMPORARY TABLE `temp_inabuyer_messages` (
                    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL,
                    `organization` varchar(255) NOT NULL,
                    `position` varchar(255) DEFAULT NULL,
                    `phone` varchar(255) DEFAULT NULL,
                    `email` varchar(255) DEFAULT NULL,
                    `kesan_dan_pesan` text NOT NULL,
                    `is_visible` tinyint(1) NOT NULL DEFAULT '0',
                    `created_at` timestamp NULL DEFAULT NULL,
                    `updated_at` timestamp NULL DEFAULT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");

            $sql = file_get_contents(__DIR__ . '/guest_messages.sql');
            $sql = str_replace('INSERT INTO `inabuyer_messages`', 'INSERT INTO `temp_inabuyer_messages`', $sql);
            DB::unprepared($sql);

            DB::statement("
                INSERT INTO `guest_messages` 
                (`id`, `event_id`, `name`, `organization`, `position`, `phone`, `email`, `kesan_dan_pesan`, `is_visible`, `created_at`, `updated_at`) 
                SELECT `id`, ?, `name`, `organization`, `position`, `phone`, `email`, `kesan_dan_pesan`, `is_visible`, `created_at`, `updated_at` 
                FROM `temp_inabuyer_messages`
            ", [$event->id]);

            DB::unprepared("DROP TEMPORARY TABLE `temp_inabuyer_messages`;");
        }

        // Catch any records that might have been manually inserted without an event_id
        DB::table('guest_messages')->whereNull('event_id')->update(['event_id' => $event->id]);
    }
}
