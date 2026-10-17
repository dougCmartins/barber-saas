<?php

declare(strict_types=1);

namespace Domain\Tenant\Tests\Feature;

use Database\Seeders\NavalhaSeeder;
use Domain\Catalog\Models\Professional;
use Domain\Catalog\Models\Service;
use Domain\Schedule\Models\Appointment;
use Domain\Tenant\Models\Barbershop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class NavalhaSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seed_restores_the_shop_catalog_without_appointments(): void
    {
        $this->seed(NavalhaSeeder::class);

        $shop = Barbershop::query()->where('slug', 'navalha')->first();

        $this->assertNotNull($shop);
        $this->assertSame('Navalha', $shop->name);

        $specialties = Professional::query()
            ->where('barbershop_id', $shop->id)
            ->pluck('specialty')
            ->sort()
            ->values()
            ->all();

        $this->assertSame(['Barba & clássico', 'Fade & textura'], $specialties);

        $services = Service::query()
            ->where('barbershop_id', $shop->id)
            ->orderBy('name')
            ->get();

        $this->assertSame(
            [
                ['Barboterapia', 35, '55.00'],
                ['Corte assinatura', 45, '65.00'],
                ['Degradê premium', 50, '75.00'],
                ['Ritual completo', 80, '110.00'],
            ],
            $services
                ->map(fn (Service $service): array => [
                    $service->name,
                    $service->duration_in_minutes,
                    $service->price,
                ])
                ->all(),
        );

        $this->assertSame(0, Appointment::query()->count());
    }
}
