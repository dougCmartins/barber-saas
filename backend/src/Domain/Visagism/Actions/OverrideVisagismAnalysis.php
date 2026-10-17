<?php

declare(strict_types=1);

namespace Domain\Visagism\Actions;

use Domain\Identity\Models\User;
use Domain\Visagism\Data\OverrideVisagismAnalysisData;
use Domain\Visagism\Data\VisagismResultData;
use Domain\Visagism\Exceptions\VisagismAnalysisNotFoundException;
use Domain\Visagism\Support\VisagismAnalysisScope;
use Domain\Visagism\Support\VisagismResultBuilder;

final class OverrideVisagismAnalysis
{
    public function handle(OverrideVisagismAnalysisData $data): VisagismResultData
    {
        /** @var User $user */
        $user = auth()->user();
        $barbershop = VisagismAnalysisScope::resolveBarbershop($data->barbershop_slug);
        $email = (string) $user->email;

        $analysis = VisagismAnalysisScope::forClientToday((int) $barbershop->id, $email)
            ->where('consultation_type', 'ai')
            ->first();

        if ($analysis === null) {
            throw new VisagismAnalysisNotFoundException;
        }

        $result = VisagismResultBuilder::build($data->face_shape, $data->hair_type);

        $analysis->update([
            'face_shape' => $result->face_shape,
            'hair_type' => $result->hair_type,
            'results_json' => $result->toArray(),
            'is_manual_override' => true,
            'feedback_reason' => $data->feedback_reason,
        ]);

        return $result;
    }
}
