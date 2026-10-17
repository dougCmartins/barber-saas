<?php

declare(strict_types=1);

namespace Domain\Schedule\Actions;

use Domain\Schedule\Data\AvailableSlotData;
use Domain\Schedule\Data\GetAvailableSlotsData;
use Domain\Schedule\Enums\AppointmentStatus;
use Domain\Schedule\Models\Appointment;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\DataCollection;

final class GetAvailableSlots
{
    public function handle(GetAvailableSlotsData $data): DataCollection
    {
        $open = Carbon::parse($data->date.' '.config('schedule.opens_at'));
        $close = Carbon::parse($data->date.' '.config('schedule.closes_at'));
        $step = (int) config('schedule.step_in_minutes');

        $appointments = Appointment::query()
            ->withoutGlobalScope('tenant')
            ->where('professional_id', $data->professional_id)
            ->where('status', '!=', AppointmentStatus::Canceled)
            ->where('starts_at', '<', $close)
            ->where('ends_at', '>', $open)
            ->get();

        $slots = [];
        $cursor = $open->copy();

        while ($cursor->lt($close)) {
            $endsAt = $cursor->copy()->addMinutes($data->duration_in_minutes);

            if ($endsAt->gt($close)) {
                break;
            }

            $collides = $appointments->contains(
                fn (Appointment $existing): bool => $existing->starts_at->lt($endsAt) && $existing->ends_at->gt($cursor),
            );

            if (! $collides) {
                $slots[] = new AvailableSlotData(
                    starts_at: $cursor->format('Y-m-d H:i:s'),
                );
            }

            $cursor->addMinutes($step);
        }

        return new DataCollection(AvailableSlotData::class, $slots);
    }
}
