<?php

declare(strict_types=1);

namespace Domain\Schedule\Actions;

use Domain\Schedule\Data\ListAppointmentsData;
use Domain\Schedule\Data\ListedAppointmentData;
use Domain\Schedule\Models\Appointment;
use Spatie\LaravelData\DataCollection;

final class ListAppointments
{
    public function handle(ListAppointmentsData $data): DataCollection
    {
        $query = Appointment::query()
            ->with(['service', 'professional', 'appointmentServices'])
            ->orderBy('starts_at');

        if (! empty($data->date)) {
            $query->whereDate('starts_at', $data->date);
        }

        $appointments = $query->get()->map(fn (Appointment $appointment): ListedAppointmentData => new ListedAppointmentData(
            id: (int) $appointment->id,
            starts_at: $appointment->starts_at->format('Y-m-d H:i:s'),
            ends_at: $appointment->ends_at->format('Y-m-d H:i:s'),
            client_name: $appointment->client_name,
            service_name: $this->serviceName($appointment),
            professional_name: empty($appointment->professional) ? '' : $appointment->professional->name,
            price: (float) $appointment->price,
            status: $appointment->status,
        ));

        return new DataCollection(ListedAppointmentData::class, $appointments);
    }

    private function serviceName(Appointment $appointment): string
    {
        $items = $appointment->appointmentServices;

        if ($items->isEmpty()) {
            return empty($appointment->service) ? '' : $appointment->service->name;
        }

        return $items->pluck('name')->implode(' + ');
    }
}
