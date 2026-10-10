<?php

declare(strict_types=1);

namespace Domain\Catalog\Controllers;

use Domain\Catalog\Actions\CreateService;
use Domain\Catalog\Actions\ListServices;
use Domain\Catalog\Data\CreateServiceData;
use Domain\Catalog\Data\ServiceData;
use Spatie\LaravelData\DataCollection;

final class ServiceController
{
    public function index(ListServices $action): DataCollection
    {
        return $action->handle();
    }

    public function store(CreateServiceData $data, CreateService $action): ServiceData
    {
        return $action->handle($data);
    }
}
