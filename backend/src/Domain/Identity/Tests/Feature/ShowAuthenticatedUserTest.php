<?php

declare(strict_types=1);

namespace Domain\Identity\Tests\Feature;

use Domain\Identity\Enums\UserRole;
use Domain\Identity\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

final class ShowAuthenticatedUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_me_returns_the_authenticated_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Ana Client',
            'email' => 'ana@example.com',
            'role' => UserRole::Client,
            'barbershop_id' => null,
        ]);

        Passport::actingAs($user);

        $response = $this->getJson('/api/me');

        $response->assertOk();
        $response->assertJsonPath('id', $user->id);
        $response->assertJsonPath('name', 'Ana Client');
        $response->assertJsonPath('email', 'ana@example.com');
        $response->assertJsonPath('role', 'client');
        $response->assertJsonPath('barbershop_id', null);
        $response->assertJsonMissingPath('code');
    }

    public function test_guest_cannot_read_me(): void
    {
        $this->getJson('/api/me')->assertUnauthorized();
    }
}
