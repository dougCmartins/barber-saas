<?php

declare(strict_types=1);

namespace Domain\Visagism\Actions;

use Domain\Identity\Models\User;
use Domain\Visagism\Data\RestoreVisagismAnalysisData;
use Domain\Visagism\Data\VisagismActiveRecordData;
use Domain\Visagism\Exceptions\VisagismAnalysisNotFoundException;
use Domain\Visagism\Models\VisagismAnalysis;
use Domain\Visagism\Support\VisagismAnalysisMapper;
use Domain\Visagism\Support\VisagismAnalysisScope;

final class RestoreVisagismAnalysis
{
    public function handle(RestoreVisagismAnalysisData $data): VisagismActiveRecordData
    {
        /** @var User $user */
        $user = auth()->user();
        $barbershop = VisagismAnalysisScope::resolveBarbershop($data->barbershop_slug);
        $email = (string) $user->email;

        $trashed = VisagismAnalysis::withTrashed()
            ->where('barbershop_id', $barbershop->id)
            ->where('client_email', $email)
            ->where('consultation_type', $data->consultation_type)
            ->whereDate('created_at', now()->toDateString())
            ->onlyTrashed()
            ->latest('id')
            ->first();

        if ($trashed === null) {
            throw new VisagismAnalysisNotFoundException;
        }

        VisagismAnalysisScope::softDeleteOpposite((int) $barbershop->id, $email, $data->consultation_type);
        $trashed->restore();

        return VisagismAnalysisMapper::toActiveRecord($trashed->refresh());
    }
}
