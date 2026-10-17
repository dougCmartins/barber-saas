<?php

declare(strict_types=1);

namespace Domain\Schedule\Actions;

use Domain\Schedule\Data\AppointmentData;
use Domain\Schedule\Data\CreateAppointmentData;
use Domain\Schedule\Enums\AppointmentStatus;
use Domain\Schedule\Exceptions\SlotUnavailableException;
use Domain\Schedule\Models\Appointment;
use Illuminate\Support\Facades\DB;

final class CreateAppointment
{
    public function handle(CreateAppointmentData $data): AppointmentData
    {
        $appointment = DB::transaction(function () use ($data): Appointment {
            $taken = Appointment::query()
                ->withoutGlobalScope('tenant')
                ->where('professional_id', $data->professional_id)
                ->where('status', '!=', AppointmentStatus::Canceled)
                ->where('starts_at', '<', $data->ends_at)
                ->where('ends_at', '>', $data->starts_at)
                ->lockForUpdate()
                ->exists();

            if ($taken) {
                throw new SlotUnavailableException;
            }

            $appointment = Appointment::query()->create([
                'barbershop_id' => $data->barbershop_id,
                'professional_id' => $data->professional_id,
                'service_id' => $data->service_id,
                'client_name' => $data->client_name,
                'client_email' => $data->client_email,
                'starts_at' => $data->starts_at,
                'ends_at' => $data->ends_at,
                'price' => $data->price,
                'status' => AppointmentStatus::Pending,
                'notes' => $data->notes,
            ]);

            foreach ($data->services as $service) {
                $appointment->appointmentServices()->create([
                    'service_id' => $service->service_id,
                    'name' => $service->name,
                    'duration_in_minutes' => $service->duration_in_minutes,
                    'price' => $service->price,
                    'position' => $service->position,
                ]);
            }

            return $appointment;
        });

        return new AppointmentData(
            id: (int) $appointment->id,
            barbershop_id: (int) $appointment->barbershop_id,
            professional_id: (int) $appointment->professional_id,
            service_id: (int) $appointment->service_id,
            client_name: $appointment->client_name,
            client_email: $appointment->client_email,
            starts_at: $appointment->starts_at->format('Y-m-d H:i:s'),
            ends_at: $appointment->ends_at->format('Y-m-d H:i:s'),
            price: (float) $appointment->price,
            status: $appointment->status,
            notes: $appointment->notes,
        );
    }
}
