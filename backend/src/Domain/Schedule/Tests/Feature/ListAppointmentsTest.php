<?php

declare(strict_types=1);

namespace Domain\Schedule\Tests\Feature;

use Domain\Catalog\Models\Professional;
use Domain\Catalog\Models\Service;
use Domain\Identity\Enums\UserRole;
use Domain\Identity\Models\User;
use Domain\Schedule\Enums\AppointmentStatus;
use Domain\Schedule\Models\Appointment;
use Domain\Tenant\Models\Barbershop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

final class ListAppointmentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_sees_only_their_shop_on_the_requested_day(): void
    {
        $graph = $this->graph('North Shop', 'north-shop', 'Diego', 'Haircut');
        $other = $this->graph('South Shop', 'south-shop', 'Rui', 'Beard');
        $this->appointment($graph, 'Bea', '2026-10-12 11:00:00', '2026-10-12 11:30:00');
        $first = $this->appointment($graph, 'Ana', '2026-10-12 10:00:00', '2026-10-12 10:30:00');
        $this->appointment($graph, 'Later', '2026-10-13 09:00:00', '2026-10-13 09:30:00');
        $this->appointment($other, 'Bruno', '2026-10-12 09:00:00', '2026-10-12 09:30:00');

        Passport::actingAs($graph['owner']);

        $response = $this->getJson('/api/appointments?date=2026-10-12');

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJsonPath('0.id', $first->id);
        $response->assertJsonPath('0.client_name', 'Ana');
        $response->assertJsonPath('0.service_name', 'Haircut');
        $response->assertJsonPath('0.professional_name', 'Diego');
        $response->assertJsonPath('0.price', 45.5);
        $response->assertJsonPath('0.status', 'pending');
        $response->assertJsonPath('0.starts_at', '2026-10-12 10:00:00');
        $response->assertJsonPath('0.ends_at', '2026-10-12 10:30:00');
        $response->assertJsonPath('1.client_name', 'Bea');
        $response->assertJsonMissing(['client_name' => 'Bruno']);
        $response->assertJsonMissing(['client_name' => 'Later']);
    }

    public function test_owner_without_a_date_sees_every_day_of_their_shop(): void
    {
        $graph = $this->graph('North Shop', 'north-shop', 'Diego', 'Haircut');
        $other = $this->graph('South Shop', 'south-shop', 'Rui', 'Beard');
        $this->appointment($graph, 'Ana', '2026-10-12 10:00:00', '2026-10-12 10:30:00');
        $this->appointment($graph, 'Later', '2026-10-13 09:00:00', '2026-10-13 09:30:00');
        $this->appointment($other, 'Bruno', '2026-10-12 09:00:00', '2026-10-12 09:30:00');

        Passport::actingAs($graph['owner']);

        $response = $this->getJson('/api/appointments');

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJsonMissing(['client_name' => 'Bruno']);
    }

    public function test_guest_cannot_list_appointments(): void
    {
        $response = $this->getJson('/api/appointments');

        $response->assertUnauthorized();
    }

    public function test_invalid_date_is_rejected(): void
    {
        $graph = $this->graph('North Shop', 'north-shop', 'Diego', 'Haircut');

        Passport::actingAs($graph['owner']);

        $response = $this->getJson('/api/appointments?date=12-10-2026');

        $response->assertUnprocessable();
    }

    /**
     * @param  array{barbershop_id: int, professional_id: int, service_id: int}  $graph
     */
    private function appointment(array $graph, string $clientName, string $startsAt, string $endsAt): Appointment
    {
        return Appointment::query()->create([
            'barbershop_id' => $graph['barbershop_id'],
            'professional_id' => $graph['professional_id'],
            'service_id' => $graph['service_id'],
            'client_name' => $clientName,
            'client_email' => 'client@example.com',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'price' => 45.5,
            'status' => AppointmentStatus::Pending,
        ]);
    }

    /**
     * @return array{owner: User, barbershop_id: int, professional_id: int, service_id: int}
     */
    private function graph(string $shopName, string $slug, string $professionalName, string $serviceName): array
    {
        $shop = Barbershop::query()->create([
            'name' => $shopName,
            'slug' => $slug,
        ]);
        $owner = User::factory()->create([
            'barbershop_id' => $shop->id,
            'role' => UserRole::Owner,
        ]);
        $barber = User::factory()->create([
            'barbershop_id' => $shop->id,
            'role' => UserRole::Barber,
        ]);
        $professional = Professional::query()->create([
            'barbershop_id' => $shop->id,
            'user_id' => $barber->id,
            'name' => $professionalName,
            'active' => true,
        ]);
        $service = Service::query()->create([
            'barbershop_id' => $shop->id,
            'name' => $serviceName,
            'price' => 45.5,
            'duration_in_minutes' => 30,
            'active' => true,
        ]);

        return [
            'owner' => $owner,
            'barbershop_id' => (int) $shop->id,
            'professional_id' => (int) $professional->id,
            'service_id' => (int) $service->id,
        ];
    }
}
