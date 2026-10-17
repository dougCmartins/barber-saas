<?php

declare(strict_types=1);

namespace Domain\Schedule\Actions;

use Domain\Schedule\Data\ClientAppointmentData;
use Domain\Schedule\Models\Appointment;

final class PresentClientAppointment
{
    public function handle(Appointment $appointment): ClientAppointmentData
    {
        $serviceNames = $appointment->appointmentServices
            ->pluck('name')
            ->filter()
            ->values()
            ->all();

        if (! $serviceNames) {
            $serviceNames = empty($appointment->service) ? [] : [$appointment->service->name];
        }

        return new ClientAppointmentData(
            id: (int) $appointment->id,
            starts_at: $appointment->starts_at->format('Y-m-d H:i:s'),
            ends_at: $appointment->ends_at->format('Y-m-d H:i:s'),
            professional_id: (int) $appointment->professional_id,
            professional_name: empty($appointment->professional) ? '' : $appointment->professional->name,
            professional_avatar_url: empty($appointment->professional) ? null : $appointment->professional->avatar_url,
            service_names: $serviceNames,
            price: (float) $appointment->price,
            status: $appointment->status,
            notes: $appointment->notes,
        );
    }
}
