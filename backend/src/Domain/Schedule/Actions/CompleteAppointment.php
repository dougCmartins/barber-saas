<?php

declare(strict_types=1);

namespace Domain\Schedule\Actions;

use Domain\Schedule\Data\AppointmentIdData;
use Domain\Schedule\Data\ListedAppointmentData;
use Domain\Schedule\Enums\AppointmentStatus;
use Domain\Schedule\Exceptions\AppointmentClosedException;
use Domain\Schedule\Exceptions\AppointmentNotFoundException;
use Domain\Schedule\Models\Appointment;

final class CompleteAppointment
{
    public function handle(AppointmentIdData $data): ListedAppointmentData
    {
        $appointment = Appointment::query()->with(['service', 'professional'])->find($data->id);

        if (! $appointment instanceof Appointment) {
            throw new AppointmentNotFoundException;
        }

        $this->assertOpen($appointment);

        $appointment->status = AppointmentStatus::Completed;
        $appointment->save();

        return $this->present($appointment);
    }

    private function assertOpen(Appointment $appointment): void
    {
        if ($appointment->status === AppointmentStatus::Canceled || $appointment->status === AppointmentStatus::Completed) {
            throw new AppointmentClosedException;
        }
    }

    private function present(Appointment $appointment): ListedAppointmentData
    {
        return new ListedAppointmentData(
            id: (int) $appointment->id,
            starts_at: $appointment->starts_at->format('Y-m-d H:i:s'),
            ends_at: $appointment->ends_at->format('Y-m-d H:i:s'),
            client_name: $appointment->client_name,
            service_name: empty($appointment->service) ? '' : $appointment->service->name,
            professional_name: empty($appointment->professional) ? '' : $appointment->professional->name,
            price: (float) $appointment->price,
            status: $appointment->status,
        );
    }
}
