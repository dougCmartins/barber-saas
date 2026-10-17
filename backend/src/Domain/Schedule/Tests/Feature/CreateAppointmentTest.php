<?php

declare(strict_types=1);

namespace Domain\Schedule\Tests\Feature;

use Domain\Catalog\Models\Professional;
use Domain\Catalog\Models\Service;
use Domain\Identity\Enums\UserRole;
use Domain\Identity\Models\User;
use Domain\Schedule\Actions\CreateAppointment;
use Domain\Schedule\Data\AppointmentData;
use Domain\Schedule\Data\AppointmentServiceItemData;
use Domain\Schedule\Data\CreateAppointmentData;
use Domain\Schedule\Enums\AppointmentStatus;
use Domain\Schedule\Exceptions\SlotUnavailableException;
use Domain\Schedule\Models\Appointment;
use Domain\Tenant\Models\Barbershop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\LaravelData\DataCollection;
use Tests\TestCase;

final class CreateAppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_an_appointment_for_a_free_slot(): void
    {
        $graph = $this->graph();

        $appointment = $this->book($graph, '2026-10-10 10:00:00', '2026-10-10 10:30:00');

        $this->assertSame($graph['barbershop_id'], $appointment->barbershop_id);
        $this->assertSame('Ana', $appointment->client_name);
        $this->assertSame('2026-10-10 10:00:00', $appointment->starts_at);
        $this->assertSame('2026-10-10 10:30:00', $appointment->ends_at);
        $this->assertSame(45.5, $appointment->price);
        $this->assertSame(AppointmentStatus::Pending, $appointment->status);
        $this->assertDatabaseCount('appointments', 1);
    }

    public function test_overlapping_slot_is_rejected(): void
    {
        $graph = $this->graph();
        $this->book($graph, '2026-10-10 10:00:00', '2026-10-10 11:00:00');

        try {
            $this->book($graph, '2026-10-10 10:30:00', '2026-10-10 11:30:00');
            $this->fail('An overlapping slot must be rejected.');
        } catch (SlotUnavailableException $exception) {
            $this->assertSame('SLOT_UNAVAILABLE', $exception->getErrorCode());
            $this->assertSame(409, $exception->getHttpStatus());
        }

        $this->assertDatabaseCount('appointments', 1);
    }

    public function test_canceled_appointment_does_not_block_the_slot(): void
    {
        $graph = $this->graph();
        Appointment::query()->create([
            'barbershop_id' => $graph['barbershop_id'],
            'professional_id' => $graph['professional_id'],
            'service_id' => $graph['service_id'],
            'client_name' => 'Old client',
            'client_email' => 'old@example.com',
            'starts_at' => '2026-10-10 10:00:00',
            'ends_at' => '2026-10-10 11:00:00',
            'price' => 45.5,
            'status' => AppointmentStatus::Canceled,
        ]);

        $appointment = $this->book($graph, '2026-10-10 10:00:00', '2026-10-10 11:00:00');

        $this->assertSame(AppointmentStatus::Pending, $appointment->status);
        $this->assertDatabaseCount('appointments', 2);
    }

    public function test_back_to_back_slots_are_allowed(): void
    {
        $graph = $this->graph();
        $this->book($graph, '2026-10-10 10:00:00', '2026-10-10 11:00:00');

        $appointment = $this->book($graph, '2026-10-10 11:00:00', '2026-10-10 12:00:00');

        $this->assertSame('2026-10-10 11:00:00', $appointment->starts_at);
        $this->assertDatabaseCount('appointments', 2);
    }

    /**
     * @param  array{barbershop_id: int, professional_id: int, service_id: int}  $graph
     */
    private function book(array $graph, string $startsAt, string $endsAt): AppointmentData
    {
        return (new CreateAppointment)->handle(new CreateAppointmentData(
            barbershop_id: $graph['barbershop_id'],
            professional_id: $graph['professional_id'],
            service_id: $graph['service_id'],
            client_name: 'Ana',
            client_email: 'ana@example.com',
            starts_at: $startsAt,
            ends_at: $endsAt,
            price: 45.5,
            services: new DataCollection(AppointmentServiceItemData::class, [
                new AppointmentServiceItemData(
                    service_id: $graph['service_id'],
                    name: 'Haircut',
                    duration_in_minutes: 30,
                    price: 45.5,
                    position: 0,
                ),
            ]),
        ));
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
            'name' => 'Haircut',
            'price' => 45.5,
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
