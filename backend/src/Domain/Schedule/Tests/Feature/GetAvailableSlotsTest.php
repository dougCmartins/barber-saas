<?php

declare(strict_types=1);

namespace Domain\Schedule\Tests\Feature;

use Domain\Catalog\Models\Professional;
use Domain\Catalog\Models\Service;
use Domain\Identity\Enums\UserRole;
use Domain\Identity\Models\User;
use Domain\Schedule\Actions\GetAvailableSlots;
use Domain\Schedule\Data\AvailableSlotData;
use Domain\Schedule\Data\GetAvailableSlotsData;
use Domain\Schedule\Enums\AppointmentStatus;
use Domain\Schedule\Models\Appointment;
use Domain\Tenant\Models\Barbershop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetAvailableSlotsTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_day_returns_blocks_until_closing_time(): void
    {
        $graph = $this->graph();

        $starts = $this->starts($graph, 30);

        $this->assertSame('2026-10-12 09:00:00', $starts[0]);
        $this->assertSame('2026-10-12 17:30:00', $starts[array_key_last($starts)]);
        $this->assertCount(18, $starts);
    }

    public function test_block_that_passes_closing_time_is_discarded(): void
    {
        $graph = $this->graph();

        $starts = $this->starts($graph, 45);

        $this->assertContains('2026-10-12 17:00:00', $starts);
        $this->assertNotContains('2026-10-12 17:30:00', $starts);
        $this->assertCount(17, $starts);
    }

    public function test_overlapping_appointment_removes_the_slot(): void
    {
        $graph = $this->graph();
        $this->appointment($graph, '2026-10-12 10:00:00', '2026-10-12 11:00:00', AppointmentStatus::Pending);

        $starts = $this->starts($graph, 30);

        $this->assertNotContains('2026-10-12 10:00:00', $starts);
        $this->assertNotContains('2026-10-12 10:30:00', $starts);
        $this->assertContains('2026-10-12 09:30:00', $starts);
        $this->assertContains('2026-10-12 11:00:00', $starts);
    }

    public function test_back_to_back_appointment_keeps_the_next_slot(): void
    {
        $graph = $this->graph();
        $this->appointment($graph, '2026-10-12 10:00:00', '2026-10-12 10:30:00', AppointmentStatus::Confirmed);

        $starts = $this->starts($graph, 30);

        $this->assertNotContains('2026-10-12 10:00:00', $starts);
        $this->assertContains('2026-10-12 10:30:00', $starts);
    }

    public function test_canceled_appointment_does_not_block_the_slot(): void
    {
        $graph = $this->graph();
        $this->appointment($graph, '2026-10-12 10:00:00', '2026-10-12 11:00:00', AppointmentStatus::Canceled);

        $starts = $this->starts($graph, 30);

        $this->assertContains('2026-10-12 10:00:00', $starts);
        $this->assertContains('2026-10-12 10:30:00', $starts);
    }

    public function test_slots_stay_blocked_when_another_tenant_is_authenticated(): void
    {
        $graph = $this->graph();
        $this->appointment($graph, '2026-10-12 10:00:00', '2026-10-12 11:00:00', AppointmentStatus::Pending);
        $otherShop = Barbershop::query()->create([
            'name' => 'South Shop',
            'slug' => 'south-shop',
        ]);
        $owner = User::factory()->create([
            'barbershop_id' => $otherShop->id,
            'role' => UserRole::Owner,
        ]);
        $this->actingAs($owner);

        $starts = $this->starts($graph, 30);

        $this->assertNotContains('2026-10-12 10:00:00', $starts);
    }

    /**
     * @param  array{barbershop_id: int, professional_id: int, service_id: int}  $graph
     * @return list<string>
     */
    private function starts(array $graph, int $durationInMinutes): array
    {
        $slots = (new GetAvailableSlots)->handle(new GetAvailableSlotsData(
            professional_id: $graph['professional_id'],
            date: '2026-10-12',
            duration_in_minutes: $durationInMinutes,
        ));

        return $slots->toCollection()
            ->map(fn (AvailableSlotData $slot): string => $slot->starts_at)
            ->values()
            ->all();
    }

    /**
     * @param  array{barbershop_id: int, professional_id: int, service_id: int}  $graph
     */
    private function appointment(array $graph, string $startsAt, string $endsAt, AppointmentStatus $status): void
    {
        Appointment::query()->create([
            'barbershop_id' => $graph['barbershop_id'],
            'professional_id' => $graph['professional_id'],
            'service_id' => $graph['service_id'],
            'client_name' => 'Ana',
            'client_email' => 'ana@example.com',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'price' => 45.5,
            'status' => $status,
        ]);
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
