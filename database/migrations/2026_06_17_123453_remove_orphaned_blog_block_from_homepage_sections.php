<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $keys = ['homepage_sections', 'homepage_sections_draft'];

        foreach ($keys as $key) {
            $sections = Setting::getJson($key);

            if (is_array($sections)) {
                // Filter out the legacy 'blog' blocks
                $filteredSections = array_values(array_filter($sections, function ($section) {
                    return isset($section['type']) && $section['type'] !== 'blog';
                }));

                // Save back if there was a modification
                if (count($sections) !== count($filteredSections)) {
                    Setting::setJson($key, $filteredSections);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration necessary for data cleanup
    }
};
