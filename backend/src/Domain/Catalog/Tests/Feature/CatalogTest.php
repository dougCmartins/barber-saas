<?php

declare(strict_types=1);

namespace Domain\Catalog\Tests\Feature;

use Domain\Catalog\Actions\CreateProfessional;
use Domain\Catalog\Actions\CreateService;
use Domain\Catalog\Actions\ListProfessionals;
use Domain\Catalog\Actions\ListServices;
use Domain\Catalog\Data\CreateProfessionalData;
use Domain\Catalog\Data\CreateServiceData;
use Domain\Catalog\Models\Professional;
use Domain\Catalog\Models\Service;
use Domain\Identity\Enums\UserRole;
use Domain\Identity\Models\User;
use Domain\Tenant\Models\Barbershop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_service_stores_the_authenticated_barbershop(): void
    {
        $shop = Barbershop::query()->create([
            'name' => 'North Shop',
            'slug' => 'north-shop',
        ]);
        $owner = User::factory()->create([
            'barbershop_id' => $shop->id,
            'role' => UserRole::Owner,
        ]);

        $this->actingAs($owner);

        $service = (new CreateService)->handle(new CreateServiceData(
            name: 'Haircut',
            description: 'Classic cut',
            price: 45.5,
            duration_in_minutes: 30,
        ));

        $this->assertSame((int) $shop->id, $service->barbershop_id);
        $this->assertSame('Haircut', $service->name);
        $this->assertSame('Classic cut', $service->description);
        $this->assertSame(45.5, $service->price);
        $this->assertSame(30, $service->duration_in_minutes);
        $this->assertTrue($service->active);
    }

    public function test_owner_lists_only_services_from_their_barbershop(): void
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
            'barbershop_id' => $shop->id,
            'role' => UserRole::Owner,
        ]);
        Service::query()->create([
            'barbershop_id' => $shop->id,
            'name' => 'Haircut',
            'price' => 40,
            'duration_in_minutes' => 30,
        ]);
        Service::query()->create([
            'barbershop_id' => $otherShop->id,
            'name' => 'Beard',
            'price' => 25,
            'duration_in_minutes' => 20,
        ]);

        $this->actingAs($owner);

        $services = (new ListServices)->handle();

        $this->assertCount(1, $services);
        $this->assertSame('Haircut', $services->toCollection()->first()->name);
    }

    public function test_create_professional_stores_the_authenticated_barbershop(): void
    {
        $shop = Barbershop::query()->create([
            'name' => 'North Shop',
            'slug' => 'north-shop',
        ]);
        $owner = User::factory()->create([
            'barbershop_id' => $shop->id,
            'role' => UserRole::Owner,
        ]);

        $this->actingAs($owner);

        $professional = (new CreateProfessional)->handle(new CreateProfessionalData(
            user_id: (int) $owner->id,
            name: 'Ada',
            specialty: 'Fade',
            avatar_url: null,
            experience_years: 8,
        ));

        $this->assertSame((int) $shop->id, $professional->barbershop_id);
        $this->assertSame((int) $owner->id, $professional->user_id);
        $this->assertSame('Ada', $professional->name);
        $this->assertSame('Fade', $professional->specialty);
        $this->assertNull($professional->avatar_url);
        $this->assertSame(8, $professional->experience_years);
        $this->assertTrue($professional->active);
    }

    public function test_owner_lists_only_professionals_from_their_barbershop(): void
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
            'barbershop_id' => $shop->id,
            'role' => UserRole::Owner,
        ]);
        $other = User::factory()->create([
            'barbershop_id' => $otherShop->id,
            'role' => UserRole::Barber,
        ]);
        Professional::query()->create([
            'barbershop_id' => $shop->id,
            'user_id' => $owner->id,
            'name' => 'Ada',
        ]);
        Professional::query()->create([
            'barbershop_id' => $otherShop->id,
            'user_id' => $other->id,
            'name' => 'Grace',
        ]);

        $this->actingAs($owner);

        $professionals = (new ListProfessionals)->handle();

        $this->assertCount(1, $professionals);
        $this->assertSame('Ada', $professionals->toCollection()->first()->name);
    }
}
