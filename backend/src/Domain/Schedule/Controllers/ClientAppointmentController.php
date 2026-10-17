<?php

declare(strict_types=1);

namespace Domain\Schedule\Controllers;

use Domain\Schedule\Actions\CancelClientAppointment;
use Domain\Schedule\Actions\ListClientAppointments;
use Domain\Schedule\Data\AppointmentIdData;
use Domain\Schedule\Data\ClientAppointmentData;
use Domain\Schedule\Data\ListClientAppointmentsData;
use Spatie\LaravelData\DataCollection;

final class ClientAppointmentController
{
    public function index(ListClientAppointmentsData $data, ListClientAppointments $action): DataCollection
    {
        return $action->handle($data);
    }

    public function cancel(string $id, CancelClientAppointment $action): ClientAppointmentData
    {
        return $action->handle(AppointmentIdData::from(
            AppointmentIdData::validate(['id' => $id])
        ));
    }
}
