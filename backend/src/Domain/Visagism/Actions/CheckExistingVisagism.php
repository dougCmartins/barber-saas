<?php

declare(strict_types=1);

namespace Domain\Visagism\Actions;

use Domain\Identity\Models\User;
use Domain\Visagism\Data\BarbershopSlugData;
use Domain\Visagism\Data\VisagismExistingStateData;
use Domain\Visagism\Data\VisagismTrashedSummaryData;
use Domain\Visagism\Models\VisagismAnalysis;
use Domain\Visagism\Support\VisagismAnalysisMapper;
use Domain\Visagism\Support\VisagismAnalysisScope;

final class CheckExistingVisagism
{
    public function handle(BarbershopSlugData $data): VisagismExistingStateData
    {
        /** @var User $user */
        $user = auth()->user();
        $barbershop = VisagismAnalysisScope::resolveBarbershop($data->barbershop_slug);
        $email = (string) $user->email;

        $active = VisagismAnalysisScope::forClientToday((int) $barbershop->id, $email)->first();

        $trashedAi = VisagismAnalysis::withTrashed()
            ->where('barbershop_id', $barbershop->id)
            ->where('client_email', $email)
            ->where('consultation_type', 'ai')
            ->whereDate('created_at', now()->toDateString())
            ->onlyTrashed()
            ->first();

        $trashedManual = VisagismAnalysis::withTrashed()
            ->where('barbershop_id', $barbershop->id)
            ->where('client_email', $email)
            ->where('consultation_type', 'manual')
            ->whereDate('created_at', now()->toDateString())
            ->onlyTrashed()
            ->first();

        return new VisagismExistingStateData(
            active: $active !== null ? VisagismAnalysisMapper::toActiveRecord($active) : null,
            trashed_ai: $trashedAi !== null
                ? new VisagismTrashedSummaryData(id: (int) $trashedAi->id, consultation_type: 'ai')
                : null,
            trashed_manual: $trashedManual !== null
                ? new VisagismTrashedSummaryData(id: (int) $trashedManual->id, consultation_type: 'manual')
                : null,
        );
    }
}
