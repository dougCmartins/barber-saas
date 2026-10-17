<?php

declare(strict_types=1);

namespace Domain\Visagism\Actions;

use Domain\Identity\Models\User;
use Domain\Visagism\Data\ForceDeleteVisagismAnalysisData;
use Domain\Visagism\Exceptions\VisagismAnalysisNotFoundException;
use Domain\Visagism\Models\VisagismAnalysis;
use Domain\Visagism\Support\VisagismAnalysisScope;

final class ForceDeleteVisagismAnalysis
{
    public function handle(ForceDeleteVisagismAnalysisData $data): void
    {
        /** @var User $user */
        $user = auth()->user();
        $barbershop = VisagismAnalysisScope::resolveBarbershop($data->barbershop_slug);
        $email = (string) $user->email;

        $analysis = VisagismAnalysis::withTrashed()
            ->where('barbershop_id', $barbershop->id)
            ->where('client_email', $email)
            ->where('consultation_type', $data->consultation_type)
            ->whereDate('created_at', now()->toDateString())
            ->latest('id')
            ->first();

        if ($analysis === null) {
            throw new VisagismAnalysisNotFoundException;
        }

        $analysis->forceDelete();
    }
}
