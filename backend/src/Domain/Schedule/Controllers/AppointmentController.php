<?php

declare(strict_types=1);

namespace Domain\Schedule\Controllers;

use Domain\Schedule\Actions\CancelAppointment;
use Domain\Schedule\Actions\CompleteAppointment;
use Domain\Schedule\Actions\ListAppointments;
use Domain\Schedule\Data\AppointmentIdData;
use Domain\Schedule\Data\ListAppointmentsData;
use Domain\Schedule\Data\ListedAppointmentData;
use Spatie\LaravelData\DataCollection;

final class AppointmentController
{
    public function index(ListAppointmentsData $data, ListAppointments $action): DataCollection
    {
        return $action->handle($data);
    }

    public function cancel(string $id, CancelAppointment $action): ListedAppointmentData
    {
        return $action->handle(AppointmentIdData::from(AppointmentIdData::validate(['id' => $id])));
    }

    public function complete(string $id, CompleteAppointment $action): ListedAppointmentData
    {
        return $action->handle(AppointmentIdData::from(AppointmentIdData::validate(['id' => $id])));
    }
}
