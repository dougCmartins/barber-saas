<?php

declare(strict_types=1);

namespace Domain\Schedule\Actions;

use Domain\Identity\Models\User;
use Domain\Schedule\Data\AppointmentIdData;
use Domain\Schedule\Data\ClientAppointmentData;
use Domain\Schedule\Enums\AppointmentStatus;
use Domain\Schedule\Exceptions\AppointmentClosedException;
use Domain\Schedule\Exceptions\AppointmentNotFoundException;
use Domain\Schedule\Models\Appointment;

final class CancelClientAppointment
{
    public function __construct(
        private readonly PresentClientAppointment $present,
    ) {}

    public function handle(AppointmentIdData $data): ClientAppointmentData
    {
        /** @var User $user */
        $user = auth()->user();

        $appointment = Appointment::query()
            ->withoutGlobalScope('tenant')
            ->with([
                'service' => fn ($query) => $query->withoutGlobalScope('tenant'),
                'professional' => fn ($query) => $query->withoutGlobalScope('tenant'),
                'appointmentServices',
            ])
            ->whereKey($data->id)
            ->where('client_email', $user->email)
            ->first();

        if (! $appointment instanceof Appointment) {
            throw new AppointmentNotFoundException;
        }

        if (
            $appointment->status === AppointmentStatus::Canceled
            || $appointment->status === AppointmentStatus::Completed
        ) {
            throw new AppointmentClosedException;
        }

        $appointment->status = AppointmentStatus::Canceled;
        $appointment->save();

        return $this->present->handle($appointment);
    }
}
