<?php

namespace Tests\Feature;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_can_render_user_index()
    {
        $this->actingAs($this->admin)->get(UserResource::getUrl('index'))->assertSuccessful();
    }

    public function test_can_render_user_create()
    {
        $this->actingAs($this->admin)->get(UserResource::getUrl('create'))->assertSuccessful();
    }

    public function test_can_render_user_edit()
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)->get(UserResource::getUrl('edit', ['record' => $user]))->assertSuccessful();
    }
}
