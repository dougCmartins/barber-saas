<?php

declare(strict_types=1);

namespace Domain\Orchestrator\Booking\Tests\Feature;

use Domain\Catalog\Models\Professional;
use Domain\Catalog\Models\Service;
use Domain\Identity\Enums\UserRole;
use Domain\Identity\Models\User;
use Domain\Schedule\Actions\ListAppointments;
use Domain\Schedule\Data\ListAppointmentsData;
use Domain\Schedule\Models\Appointment;
use Domain\Schedule\Models\AppointmentService;
use Domain\Tenant\Models\Barbershop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class BookPublicAppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_booking_stores_the_service_duration_and_price(): void
    {
        $graph = $this->graph();

        $response = $this->postJson('/api/barbershops/north-shop/appointments', [
            'professional_id' => $graph['professional_id'],
            'service_ids' => [$graph['service_id']],
            'client_name' => 'Ana',
            'client_email' => 'ana@example.com',
            'starts_at' => '2026-10-10 10:00:00',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('client_name', 'Ana');
        $response->assertJsonPath('starts_at', '2026-10-10 10:00:00');
        $response->assertJsonPath('ends_at', '2026-10-10 10:30:00');
        $response->assertJsonPath('price', 45.5);
        $response->assertJsonPath('status', 'pending');
        $response->assertJsonPath('barbershop_id', $graph['barbershop_id']);
        $this->assertSame(1, Appointment::query()->count());
        $this->assertSame(1, AppointmentService::query()->count());
    }

    public function test_several_services_become_one_appointment_with_summed_duration_and_price(): void
    {
        $graph = $this->graph();
        $beard = Service::query()->create([
            'barbershop_id' => $graph['barbershop_id'],
            'name' => 'Barba',
            'price' => 25,
            'duration_in_minutes' => 20,
            'active' => true,
        ]);
        Service::query()->whereKey($graph['service_id'])->update(['name' => 'Corte', 'price' => 40]);

        $response = $this->postJson('/api/barbershops/north-shop/appointments', [
            'professional_id' => $graph['professional_id'],
            'service_ids' => [$graph['service_id'], $beard->id, $graph['service_id']],
            'client_name' => 'Ana',
            'client_email' => 'ana@example.com',
            'starts_at' => '2026-10-10 10:00:00',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('service_id', $graph['service_id']);
        $response->assertJsonPath('ends_at', '2026-10-10 10:50:00');
        $response->assertJsonPath('price', 65);
        $this->assertSame(1, Appointment::query()->count());
        $this->assertSame(2, AppointmentService::query()->count());
        $this->assertDatabaseHas('appointment_services', [
            'name' => 'Corte',
            'position' => 0,
        ]);
        $this->assertDatabaseHas('appointment_services', [
            'name' => 'Barba',
            'position' => 1,
        ]);

        $listed = (new ListAppointments)->handle(new ListAppointmentsData);

        $this->assertSame('Corte + Barba', $listed->toArray()[0]['service_name']);
    }

    public function test_empty_service_list_is_rejected(): void
    {
        $graph = $this->graph();

        $response = $this->postJson('/api/barbershops/north-shop/appointments', [
            'professional_id' => $graph['professional_id'],
            'service_ids' => [],
            'client_name' => 'Ana',
            'client_email' => 'ana@example.com',
            'starts_at' => '2026-10-10 10:00:00',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonPath('code', 'VALIDATION_FAILED');
        $this->assertSame(0, Appointment::query()->count());
    }

    public function test_unknown_slug_returns_not_found_envelope(): void
    {
        $response = $this->postJson('/api/barbershops/missing-shop/appointments', [
            'professional_id' => 1,
            'service_ids' => [1],
            'client_name' => 'Ana',
            'client_email' => 'ana@example.com',
            'starts_at' => '2026-10-10 10:00:00',
        ]);

        $response->assertNotFound();
        $response->assertJsonPath('data', null);
        $response->assertJsonPath('code', 'BARBERSHOP_NOT_FOUND');
        $response->assertJsonPath('status_code', 404);
        $response->assertJsonPath('message', 'Barbershop not found.');
    }

    public function test_service_from_another_shop_does_not_create_an_appointment(): void
    {
        $graph = $this->graph();
        $otherShop = Barbershop::query()->create([
            'name' => 'South Shop',
            'slug' => 'south-shop',
        ]);
        $otherService = Service::query()->create([
            'barbershop_id' => $otherShop->id,
            'name' => 'Beard',
            'price' => 25,
            'duration_in_minutes' => 20,
            'active' => true,
        ]);

        $response = $this->postJson('/api/barbershops/north-shop/appointments', [
            'professional_id' => $graph['professional_id'],
            'service_ids' => [$otherService->id],
            'client_name' => 'Ana',
            'client_email' => 'ana@example.com',
            'starts_at' => '2026-10-10 10:00:00',
        ]);

        $response->assertNotFound();
        $response->assertJsonPath('code', 'BOOKABLE_OFFER_NOT_FOUND');
        $response->assertJsonPath('status_code', 404);
        $this->assertSame(0, Appointment::query()->count());
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
