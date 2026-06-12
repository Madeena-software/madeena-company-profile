<?php

namespace Tests\Unit;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_set_and_get_json()
    {
        $data = ['email' => 'test@example.com', 'phone' => '12345'];

        Setting::setJson('contact_info', $data);

        $retrieved = Setting::getJson('contact_info');

        $this->assertEquals($data, $retrieved);
        $this->assertIsArray($retrieved);
    }

    public function test_get_json_returns_default_if_not_found()
    {
        $retrieved = Setting::getJson('non_existent', ['default' => 'value']);

        $this->assertEquals(['default' => 'value'], $retrieved);
    }
}
