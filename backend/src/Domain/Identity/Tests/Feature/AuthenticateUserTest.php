<?php

declare(strict_types=1);

namespace Domain\Identity\Tests\Feature;

use Domain\Identity\Actions\AuthenticateUser;
use Domain\Identity\Data\LoginRequestData;
use Domain\Identity\Data\TokenData;
use Domain\Identity\Enums\UserRole;
use Domain\Identity\Exceptions\InvalidCredentialsException;
use Domain\Identity\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Client;
use Tests\TestCase;

final class AuthenticateUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Client::factory()->asPersonalAccessTokenClient()->create();
    }

    public function test_handle_returns_a_token_for_valid_credentials(): void
    {
        User::factory()->create([
            'email' => 'owner@example.com',
            'password' => 'password',
            'role' => UserRole::Owner,
        ]);

        $token = (new AuthenticateUser)->handle(new LoginRequestData(
            email: 'owner@example.com',
            password: 'password',
        ));

        $this->assertInstanceOf(TokenData::class, $token);
        $this->assertNotSame('', $token->access_token);
        $this->assertSame('Bearer', $token->token_type);
        $this->assertGreaterThan(0, $token->expires_in);
    }

    public function test_handle_rejects_a_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'owner@example.com',
            'password' => 'password',
            'role' => UserRole::Owner,
        ]);

        $this->expectException(InvalidCredentialsException::class);
        $this->expectExceptionMessage('Invalid credentials.');

        (new AuthenticateUser)->handle(new LoginRequestData(
            email: 'owner@example.com',
            password: 'wrong-password',
        ));
    }

    public function test_handle_rejects_an_unknown_email(): void
    {
        $this->expectException(InvalidCredentialsException::class);

        (new AuthenticateUser)->handle(new LoginRequestData(
            email: 'missing@example.com',
            password: 'password',
        ));
    }
}
