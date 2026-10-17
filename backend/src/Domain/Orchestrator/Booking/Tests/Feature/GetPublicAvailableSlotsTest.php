<?php

declare(strict_types=1);

namespace Domain\Orchestrator\Booking\Tests\Feature;

use Domain\Catalog\Models\Professional;
use Domain\Catalog\Models\Service;
use Domain\Identity\Enums\UserRole;
use Domain\Identity\Models\User;
use Domain\Tenant\Models\Barbershop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetPublicAvailableSlotsTest extends TestCase
{
    use RefreshDatabase;

    public function test_summed_duration_drops_a_slot_that_fits_one_service_only(): void
    {
        $graph = $this->graph();
        $beard = Service::query()->create([
            'barbershop_id' => $graph['barbershop_id'],
            'name' => 'Barba',
            'price' => 25,
            'duration_in_minutes' => 30,
            'active' => true,
        ]);

        $single = $this->getJson($this->slotsUrl($graph['professional_id'], [$graph['service_id']]));
        $combined = $this->getJson($this->slotsUrl($graph['professional_id'], [$graph['service_id'], $beard->id]));

        $single->assertOk();
        $combined->assertOk();
        $this->assertContains('2026-10-12 17:30:00', $this->startsAt($single->json()));
        $this->assertContains('2026-10-12 17:00:00', $this->startsAt($combined->json()));
        $this->assertNotContains('2026-10-12 17:30:00', $this->startsAt($combined->json()));
    }

    public function test_unknown_service_returns_not_found_envelope(): void
    {
        $graph = $this->graph();

        $response = $this->getJson($this->slotsUrl($graph['professional_id'], [9999]));

        $response->assertNotFound();
        $response->assertJsonPath('code', 'BOOKABLE_OFFER_NOT_FOUND');
        $response->assertJsonPath('status_code', 404);
    }

    public function test_empty_service_list_is_rejected(): void
    {
        $graph = $this->graph();

        $response = $this->getJson('/api/barbershops/north-shop/professionals/'.$graph['professional_id'].'/slots?date=2026-10-12');

        $response->assertUnprocessable();
        $response->assertJsonPath('code', 'VALIDATION_FAILED');
    }

    /**
     * @param  list<int>  $serviceIds
     */
    private function slotsUrl(int $professionalId, array $serviceIds): string
    {
        return '/api/barbershops/north-shop/professionals/'.$professionalId.'/slots?'.http_build_query([
            'date' => '2026-10-12',
            'service_ids' => $serviceIds,
        ]);
    }

    /**
     * @param  array<int, array{starts_at?: string}>  $slots
     * @return list<string>
     */
    private function startsAt(array $slots): array
    {
        return array_map(
            fn (array $slot): string => $slot['starts_at'] ?? '',
            $slots,
        );
    }

    /**
     * @return array{barbershop_id: int, professional_id: int, service_id: int}
     */
    private function graph(): array
    {
        $shop = Barbershop::query()->create([
            'name' => 'North Shop',
            'slug' => 'north-shop',
        ]);
        $user = User::factory()->create([
            'barbershop_id' => $shop->id,
            'role' => UserRole::Barber,
        ]);
        $professional = Professional::query()->create([
            'barbershop_id' => $shop->id,
            'user_id' => $user->id,
            'name' => 'Diego',
            'active' => true,
        ]);
        $service = Service::query()->create([
            'barbershop_id' => $shop->id,
            'name' => 'Corte',
            'price' => 40,
            'duration_in_minutes' => 30,
            'active' => true,
        ]);

        return [
            'barbershop_id' => (int) $shop->id,
            'professional_id' => (int) $professional->id,
            'service_id' => (int) $service->id,
        ];
    }
}
