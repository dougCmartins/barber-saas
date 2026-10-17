<?php

declare(strict_types=1);

namespace Domain\Schedule\Tests\Feature;

use Domain\Catalog\Models\Professional;
use Domain\Catalog\Models\Service;
use Domain\Identity\Enums\UserRole;
use Domain\Identity\Models\User;
use Domain\Schedule\Enums\AppointmentStatus;
use Domain\Schedule\Models\Appointment;
use Domain\Schedule\Models\AppointmentService;
use Domain\Tenant\Models\Barbershop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

final class ClientAppointmentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_lists_only_own_appointments_with_filters(): void
    {
        $graph = $this->graph();
        $mine = $this->appointment($graph, 'ana@example.com', '2026-10-12 10:00:00', AppointmentStatus::Pending);
        $this->appointment($graph, 'other@example.com', '2026-10-12 11:00:00', AppointmentStatus::Pending);
        $this->appointment($graph, 'ana@example.com', '2026-10-20 10:00:00', AppointmentStatus::Completed);

        Passport::actingAs($graph['client']);

        $response = $this->getJson('/api/client/appointments?from=2026-10-01&to=2026-10-15&statuses[]=pending');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.id', $mine->id);
        $response->assertJsonPath('0.professional_name', 'Diego');
        $response->assertJsonPath('0.service_names.0', 'Haircut');
        $response->assertJsonPath('0.status', 'pending');
        $response->assertJsonMissingPath('code');
    }

    public function test_client_filters_by_professional(): void
    {
        $graph = $this->graph();
        $otherBarber = User::factory()->create([
            'barbershop_id' => $graph['barbershop_id'],
            'role' => UserRole::Barber,
        ]);
        $otherPro = Professional::query()->create([
            'barbershop_id' => $graph['barbershop_id'],
            'user_id' => $otherBarber->id,
            'name' => 'Rui',
            'active' => true,
        ]);
        $mine = $this->appointment($graph, 'ana@example.com', '2026-10-12 10:00:00', AppointmentStatus::Confirmed);
        Appointment::query()->create([
            'barbershop_id' => $graph['barbershop_id'],
            'professional_id' => $otherPro->id,
            'service_id' => $graph['service_id'],
            'client_name' => 'Ana',
            'client_email' => 'ana@example.com',
            'starts_at' => '2026-10-13 10:00:00',
            'ends_at' => '2026-10-13 10:30:00',
            'price' => 45.5,
            'status' => AppointmentStatus::Confirmed,
        ]);

        Passport::actingAs($graph['client']);

        $response = $this->getJson('/api/client/appointments?professional_id='.$graph['professional_id']);

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.id', $mine->id);
    }

    public function test_client_cancels_own_open_appointment(): void
    {
        $graph = $this->graph();
        $appointment = $this->appointment($graph, 'ana@example.com', '2026-10-12 10:00:00', AppointmentStatus::Pending);

        Passport::actingAs($graph['client']);

        $response = $this->patchJson('/api/client/appointments/'.$appointment->id.'/cancel');

        $response->assertOk();
        $response->assertJsonPath('status', 'canceled');
        $this->assertSame(AppointmentStatus::Canceled, $appointment->refresh()->status);
    }

    public function test_client_cannot_cancel_someone_elses_appointment(): void
    {
        $graph = $this->graph();
        $foreign = $this->appointment($graph, 'other@example.com', '2026-10-12 10:00:00', AppointmentStatus::Pending);

        Passport::actingAs($graph['client']);

        $response = $this->patchJson('/api/client/appointments/'.$foreign->id.'/cancel');

        $response->assertNotFound();
        $response->assertJsonPath('code', 'APPOINTMENT_NOT_FOUND');
        $this->assertSame(AppointmentStatus::Pending, $foreign->refresh()->status);
    }

    public function test_guest_cannot_list_or_cancel_client_appointments(): void
    {
        $graph = $this->graph();
        $appointment = $this->appointment($graph, 'ana@example.com', '2026-10-12 10:00:00', AppointmentStatus::Pending);

        $list = $this->getJson('/api/client/appointments');
        $cancel = $this->patchJson('/api/client/appointments/'.$appointment->id.'/cancel');

        $list->assertUnauthorized();
        $cancel->assertUnauthorized();
    }

    /**
     * @param  array{barbershop_id: int, professional_id: int, service_id: int}  $graph
     */
    private function appointment(
        array $graph,
        string $email,
        string $startsAt,
        AppointmentStatus $status,
    ): Appointment {
        $appointment = Appointment::query()->create([
            'barbershop_id' => $graph['barbershop_id'],
            'professional_id' => $graph['professional_id'],
            'service_id' => $graph['service_id'],
            'client_name' => 'Ana',
            'client_email' => $email,
            'starts_at' => $startsAt,
            'ends_at' => '2026-10-12 10:30:00',
            'price' => 45.5,
            'status' => $status,
            'notes' => 'Near the window',
        ]);

        AppointmentService::query()->create([
            'appointment_id' => $appointment->id,
            'service_id' => $graph['service_id'],
            'name' => 'Haircut',
            'duration_in_minutes' => 30,
            'price' => 45.5,
            'position' => 0,
        ]);

        return $appointment;
    }

    /**
     * @return array{client: User, barbershop_id: int, professional_id: int, service_id: int}
     */
    private function graph(): array
    {
        $shop = Barbershop::query()->create([
            'name' => 'North Shop',
            'slug' => 'north-shop',
        ]);
        $client = User::factory()->create([
            'barbershop_id' => null,
            'role' => UserRole::Client,
            'email' => 'ana@example.com',
            'name' => 'Ana Client',
        ]);
        $barber = User::factory()->create([
            'barbershop_id' => $shop->id,
            'role' => UserRole::Barber,
        ]);
        $professional = Professional::query()->create([
            'barbershop_id' => $shop->id,
            'user_id' => $barber->id,
            'name' => 'Diego',
            'avatar_url' => 'https://example.com/diego.jpg',
            'active' => true,
        ]);
        $service = Service::query()->create([
            'barbershop_id' => $shop->id,
            'name' => 'Haircut',
            'price' => 45.5,
            'duration_in_minutes' => 30,
            'active' => true,
        ]);

        return [
            'client' => $client,
            'barbershop_id' => (int) $shop->id,
            'professional_id' => (int) $professional->id,
            'service_id' => (int) $service->id,
        ];
    }
}
