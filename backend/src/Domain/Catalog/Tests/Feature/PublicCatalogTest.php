<?php

declare(strict_types=1);

namespace Domain\Catalog\Tests\Feature;

use Domain\Catalog\Actions\ListPublicProfessionals;
use Domain\Catalog\Actions\ListPublicServices;
use Domain\Catalog\Data\PublicCatalogRequestData;
use Domain\Catalog\Exceptions\BarbershopNotFoundException;
use Domain\Catalog\Models\Professional;
use Domain\Catalog\Models\Service;
use Domain\Identity\Enums\UserRole;
use Domain\Identity\Models\User;
use Domain\Tenant\Models\Barbershop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PublicCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_services_list_only_active_items_of_the_requested_shop(): void
    {
        $shop = Barbershop::query()->create([
            'name' => 'North Shop',
            'slug' => 'north-shop',
        ]);
        $otherShop = Barbershop::query()->create([
            'name' => 'South Shop',
            'slug' => 'south-shop',
        ]);
        Service::query()->create([
            'barbershop_id' => $shop->id,
            'name' => 'Haircut',
            'price' => 40,
            'duration_in_minutes' => 30,
            'active' => true,
        ]);
        Service::query()->create([
            'barbershop_id' => $shop->id,
            'name' => 'Hidden',
            'price' => 10,
            'duration_in_minutes' => 15,
            'active' => false,
        ]);
        Service::query()->create([
            'barbershop_id' => $otherShop->id,
            'name' => 'Beard',
            'price' => 25,
            'duration_in_minutes' => 20,
            'active' => true,
        ]);

        $services = (new ListPublicServices)->handle(new PublicCatalogRequestData(
            barbershop_slug: 'north-shop',
        ));

        $this->assertCount(1, $services);
        $this->assertSame('Haircut', $services->toCollection()->first()->name);
    }

    public function test_public_services_ignore_the_authenticated_tenant_scope(): void
    {
        $shop = Barbershop::query()->create([
            'name' => 'North Shop',
            'slug' => 'north-shop',
        ]);
        $otherShop = Barbershop::query()->create([
            'name' => 'South Shop',
            'slug' => 'south-shop',
        ]);
        $owner = User::factory()->create([
            'barbershop_id' => $otherShop->id,
            'role' => UserRole::Owner,
        ]);
        Service::query()->create([
            'barbershop_id' => $shop->id,
            'name' => 'Haircut',
            'price' => 40,
            'duration_in_minutes' => 30,
        ]);

        $this->actingAs($owner);

        $services = (new ListPublicServices)->handle(new PublicCatalogRequestData(
            barbershop_slug: 'north-shop',
        ));

        $this->assertCount(1, $services);
        $this->assertSame('Haircut', $services->toCollection()->first()->name);
    }

    public function test_public_professionals_list_only_active_items_of_the_requested_shop(): void
    {
        $shop = Barbershop::query()->create([
            'name' => 'North Shop',
            'slug' => 'north-shop',
        ]);
        $otherShop = Barbershop::query()->create([
            'name' => 'South Shop',
            'slug' => 'south-shop',
        ]);
        $barber = User::factory()->create([
            'barbershop_id' => $shop->id,
            'role' => UserRole::Barber,
        ]);
        $other = User::factory()->create([
            'barbershop_id' => $otherShop->id,
            'role' => UserRole::Barber,
        ]);
        Professional::query()->create([
            'barbershop_id' => $shop->id,
            'user_id' => $barber->id,
            'name' => 'Ada',
            'avatar_url' => 'https://example.com/ada.jpg',
            'active' => true,
        ]);
        Professional::query()->create([
            'barbershop_id' => $shop->id,
            'user_id' => $barber->id,
            'name' => 'Hidden',
            'active' => false,
        ]);
        Professional::query()->create([
            'barbershop_id' => $otherShop->id,
            'user_id' => $other->id,
            'name' => 'Grace',
            'active' => true,
        ]);

        $professionals = (new ListPublicProfessionals)->handle(new PublicCatalogRequestData(
            barbershop_slug: 'north-shop',
        ));

        $this->assertCount(1, $professionals);
        $this->assertSame('Ada', $professionals->toCollection()->first()->name);
        $this->assertSame('https://example.com/ada.jpg', $professionals->toCollection()->first()->avatar_url);
    }

    public function test_public_service_route_does_not_require_authentication(): void
    {
        $shop = Barbershop::query()->create([
            'name' => 'North Shop',
            'slug' => 'north-shop',
        ]);
        Service::query()->create([
            'barbershop_id' => $shop->id,
            'name' => 'Haircut',
            'price' => 40,
            'duration_in_minutes' => 30,
        ]);

        $response = $this->getJson('/api/barbershops/north-shop/services');

        $response->assertOk();
        $response->assertJsonPath('0.name', 'Haircut');
    }

    public function test_unknown_barbershop_slug_returns_not_found(): void
    {
        $this->expectException(BarbershopNotFoundException::class);

        (new ListPublicServices)->handle(new PublicCatalogRequestData(
            barbershop_slug: 'missing-shop',
        ));
    }

    public function test_unknown_barbershop_route_returns_not_found_envelope(): void
    {
        $response = $this->getJson('/api/barbershops/missing-shop/services');

        $response->assertNotFound();
        $response->assertJsonPath('code', 'BARBERSHOP_NOT_FOUND');
        $response->assertJsonPath('status_code', 404);
    }
}
