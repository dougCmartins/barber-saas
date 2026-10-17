<?php

declare(strict_types=1);

namespace Domain\Orchestrator\Booking\Controllers;

use Domain\Orchestrator\Booking\Actions\BookPublicAppointment;
use Domain\Orchestrator\Booking\Data\BookPublicAppointmentData;
use Domain\Schedule\Data\AppointmentData;

final class BookPublicAppointmentController
{
    public function store(string $slug, BookPublicAppointmentData $data, BookPublicAppointment $action): AppointmentData
    {
        return $action->handle(BookPublicAppointmentData::from(
            BookPublicAppointmentData::validate([
                'slug' => $slug,
                'professional_id' => $data->professional_id,
                'service_ids' => $data->service_ids,
                'client_name' => $data->client_name,
                'client_email' => $data->client_email,
                'starts_at' => $data->starts_at,
                'notes' => $data->notes,
            ])
        ));
    }
}
