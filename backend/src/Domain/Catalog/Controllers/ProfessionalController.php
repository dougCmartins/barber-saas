<?php

declare(strict_types=1);

namespace Domain\Catalog\Controllers;

use Domain\Catalog\Actions\CreateProfessional;
use Domain\Catalog\Actions\ListProfessionals;
use Domain\Catalog\Data\CreateProfessionalData;
use Domain\Catalog\Data\ProfessionalData;
use Spatie\LaravelData\DataCollection;

final class ProfessionalController
{
    public function index(ListProfessionals $action): DataCollection
    {
        return $action->handle();
    }

    public function store(CreateProfessionalData $data, CreateProfessional $action): ProfessionalData
    {
        return $action->handle($data);
    }
}
