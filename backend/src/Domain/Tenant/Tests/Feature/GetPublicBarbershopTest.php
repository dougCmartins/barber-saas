<?php

declare(strict_types=1);

namespace Domain\Tenant\Tests\Feature;

use Domain\Identity\Enums\UserRole;
use Domain\Identity\Models\User;
use Domain\Tenant\Data\PublicBarbershopRequestData;
use Domain\Tenant\Models\Barbershop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class GetPublicBarbershopTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_route_returns_the_active_shop_profile_without_authentication(): void
    {
        Barbershop::query()->create([
            'name' => 'North Shop',
            'slug' => 'north-shop',
            'address' => 'Rua Harmonia, 42',
            'city' => 'Sao Paulo',
            'closing_time' => '20:00',
            'rating' => 4.8,
            'total_reviews' => 12,
            'banner_url' => 'https://example.com/banner.jpg',
        ]);

        $response = $this->getJson('/api/barbershops/north-shop');

        $response->assertOk();
        $response->assertJsonPath('name', 'North Shop');
        $response->assertJsonPath('slug', 'north-shop');
        $response->assertJsonPath('active', true);
        $response->assertJsonPath('address', 'Rua Harmonia, 42');
        $response->assertJsonPath('city', 'Sao Paulo');
        $response->assertJsonPath('closing_time', '20:00');
        $response->assertJsonPath('rating', 4.8);
        $response->assertJsonPath('total_reviews', 12);
        $response->assertJsonPath('banner_url', 'https://example.com/banner.jpg');
    }

    public function test_inactive_shop_returns_not_found_envelope(): void
    {
        Barbershop::query()->create([
            'name' => 'Closed Shop',
            'slug' => 'closed-shop',
            'active' => false,
        ]);

        $response = $this->getJson('/api/barbershops/closed-shop');

        $response->assertNotFound();
        $response->assertJsonPath('code', 'BARBERSHOP_NOT_FOUND');
        $response->assertJsonPath('status_code', 404);
        $response->assertJsonPath('message', 'Barbershop not found.');
    }

    public function test_unknown_slug_returns_not_found_envelope(): void
    {
        $response = $this->getJson('/api/barbershops/missing-shop');

        $response->assertNotFound();
        $response->assertJsonPath('code', 'BARBERSHOP_NOT_FOUND');
        $response->assertJsonPath('status_code', 404);
    }

    public function test_owner_of_another_shop_can_read_the_public_profile(): void
    {
        Barbershop::query()->create([
            'name' => 'North Shop',
            'slug' => 'north-shop',
            'city' => 'Sao Paulo',
        ]);
        $otherShop = Barbershop::query()->create([
            'name' => 'South Shop',
            'slug' => 'south-shop',
        ]);
        $owner = User::factory()->create([
            'barbershop_id' => $otherShop->id,
            'role' => UserRole::Owner,
        ]);

        $this->actingAs($owner);

        $response = $this->getJson('/api/barbershops/north-shop');

        $response->assertOk();
        $response->assertJsonPath('slug', 'north-shop');
        $response->assertJsonPath('city', 'Sao Paulo');
    }

    public function test_empty_slug_fails_validation(): void
    {
        $this->expectException(ValidationException::class);

        PublicBarbershopRequestData::validate(['slug' => '']);
    }

    public function test_public_route_allows_the_frontend_origin(): void
    {
        Barbershop::query()->create([
            'name' => 'North Shop',
            'slug' => 'north-shop',
        ]);

        foreach (['http://localhost:5173', 'http://127.0.0.1:5173'] as $origin) {
            $response = $this->withHeader('Origin', $origin)
                ->getJson('/api/barbershops/north-shop');

            $response->assertOk();
            $response->assertHeader('Access-Control-Allow-Origin', $origin);
        }
    }
}
