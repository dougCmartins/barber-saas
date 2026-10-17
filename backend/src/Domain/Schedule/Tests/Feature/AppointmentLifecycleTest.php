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

final class AppointmentLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_cancels_and_completes_an_open_appointment(): void
    {
        $graph = $this->graph('North Shop', 'north-shop', 'Diego', 'Haircut');
        $pending = $this->appointment($graph, AppointmentStatus::Pending);
        $confirmed = $this->appointment($graph, AppointmentStatus::Confirmed);

        Passport::actingAs($graph['owner']);

        $canceled = $this->patchJson('/api/appointments/'.$pending->id.'/cancel');

        $canceled->assertOk();
        $canceled->assertJsonPath('id', $pending->id);
        $canceled->assertJsonPath('client_name', 'Ana');
        $canceled->assertJsonPath('service_name', 'Haircut');
        $canceled->assertJsonPath('professional_name', 'Diego');
        $canceled->assertJsonPath('price', 45.5);
        $canceled->assertJsonPath('status', 'canceled');
        $this->assertSame(AppointmentStatus::Canceled, $pending->refresh()->status);

        $completed = $this->patchJson('/api/appointments/'.$confirmed->id.'/complete');

        $completed->assertOk();
        $completed->assertJsonPath('status', 'completed');
        $this->assertSame(AppointmentStatus::Completed, $confirmed->refresh()->status);
    }

    public function test_another_shop_and_a_missing_id_share_the_not_found_envelope(): void
    {
        $graph = $this->graph('North Shop', 'north-shop', 'Diego', 'Haircut');
        $other = $this->graph('South Shop', 'south-shop', 'Rui', 'Beard');
        $foreign = $this->appointment($other, AppointmentStatus::Pending);

        Passport::actingAs($graph['owner']);

        $hidden = $this->patchJson('/api/appointments/'.$foreign->id.'/cancel');
        $missing = $this->patchJson('/api/appointments/999999/complete');

        $hidden->assertNotFound();
        $missing->assertNotFound();
        $hidden->assertJsonPath('data', null);
        $missing->assertJsonPath('data', null);
        $hidden->assertJsonPath('code', 'APPOINTMENT_NOT_FOUND');
        $missing->assertJsonPath('code', 'APPOINTMENT_NOT_FOUND');
        $hidden->assertJsonPath('status_code', 404);
        $missing->assertJsonPath('status_code', 404);
        $this->assertSame(AppointmentStatus::Pending, $foreign->refresh()->status);
    }

    public function test_guest_cannot_change_an_appointment(): void
    {
        $graph = $this->graph('North Shop', 'north-shop', 'Diego', 'Haircut');
        $appointment = $this->appointment($graph, AppointmentStatus::Pending);

        $response = $this->patchJson('/api/appointments/'.$appointment->id.'/cancel');

        $response->assertUnauthorized();
    }

    public function test_completing_a_canceled_appointment_is_rejected(): void
    {
        $graph = $this->graph('North Shop', 'north-shop', 'Diego', 'Haircut');
        $appointment = $this->appointment($graph, AppointmentStatus::Canceled);

        Passport::actingAs($graph['owner']);

        $response = $this->patchJson('/api/appointments/'.$appointment->id.'/complete');

        $response->assertUnprocessable();
        $response->assertJsonPath('code', 'APPOINTMENT_CLOSED');
        $response->assertJsonPath('status_code', 422);
        $this->assertSame(AppointmentStatus::Canceled, $appointment->refresh()->status);
    }

    /**
     * @param  array{barbershop_id: int, professional_id: int, service_id: int}  $graph
     */
    private function appointment(array $graph, AppointmentStatus $status): Appointment
    {
        return Appointment::query()->create([
            'barbershop_id' => $graph['barbershop_id'],
            'professional_id' => $graph['professional_id'],
            'service_id' => $graph['service_id'],
            'client_name' => 'Ana',
            'client_email' => 'ana@example.com',
            'starts_at' => '2026-10-12 10:00:00',
            'ends_at' => '2026-10-12 10:30:00',
            'price' => 45.5,
            'status' => $status,
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
