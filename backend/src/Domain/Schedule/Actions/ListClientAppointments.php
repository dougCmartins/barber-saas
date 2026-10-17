<?php

declare(strict_types=1);

namespace Domain\Schedule\Actions;

use Domain\Identity\Models\User;
use Domain\Schedule\Data\ClientAppointmentData;
use Domain\Schedule\Data\ListClientAppointmentsData;
use Domain\Schedule\Models\Appointment;
use Spatie\LaravelData\DataCollection;

final class ListClientAppointments
{
    public function __construct(
        private readonly PresentClientAppointment $present,
    ) {}

    public function handle(ListClientAppointmentsData $data): DataCollection
    {
        /** @var User $user */
        $user = auth()->user();

        $appointments = Appointment::query()
            ->withoutGlobalScope('tenant')
            ->with([
                'service' => fn ($query) => $query->withoutGlobalScope('tenant'),
                'professional' => fn ($query) => $query->withoutGlobalScope('tenant'),
                'appointmentServices',
            ])
            ->where('client_email', $user->email)
            ->byPeriod($data->from, $data->to)
            ->byStatus($data->statuses ?? [])
            ->byProfessional($data->professional_id)
            ->orderByDesc('starts_at')
            ->get()
            ->map(fn (Appointment $appointment): ClientAppointmentData => $this->present->handle($appointment));

        return new DataCollection(ClientAppointmentData::class, $appointments);
    }
}
