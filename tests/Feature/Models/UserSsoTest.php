<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSsoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the User model can be created with nullable password and has sso_id.
     */
    public function test_user_can_be_created_with_sso_id_and_nullable_password(): void
    {
        $user = User::create([
            'sso_id' => '12345-abcde',
            'name' => 'John SSO',
            'email' => 'john.sso@example.com',
            'password' => null,
            'role' => 'user',
            'is_admin' => false,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'john.sso@example.com',
            'sso_id' => '12345-abcde',
            'password' => null,
            'role' => 'user',
            'is_admin' => 0,
        ]);
    }
}
