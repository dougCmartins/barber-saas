<?php

declare(strict_types=1);

namespace Domain\Visagism\Actions;

use Domain\Identity\Models\User;
use Domain\Visagism\Data\AnalyzeFaceData;
use Domain\Visagism\Data\VisagismResultData;
use Domain\Visagism\Exceptions\VisagismLimitExceededException;
use Domain\Visagism\Models\VisagismAnalysis;
use Domain\Visagism\Support\VisagismAnalysisScope;
use Domain\Visagism\Support\VisagismResultBuilder;

final class AnalyzeFaceProfile
{
    public function handle(AnalyzeFaceData $data): VisagismResultData
    {
        /** @var User $user */
        $user = auth()->user();
        $barbershop = VisagismAnalysisScope::resolveBarbershop($data->barbershop_slug);
        $email = (string) $user->email;

        $activeAi = VisagismAnalysisScope::forClientToday((int) $barbershop->id, $email)
            ->where('consultation_type', 'ai')
            ->first();

        if ($activeAi !== null) {
            throw new VisagismLimitExceededException;
        }

        VisagismAnalysisScope::softDeleteOpposite((int) $barbershop->id, $email, 'ai');

        sleep(2);

        $hairType = $data->hair_texture ?? 'wavy';
        $normalizedHair = in_array($hairType, ['curly', 'coily'], true) ? $hairType : 'wavy';
        $result = VisagismResultBuilder::build('square', $normalizedHair);

        VisagismAnalysis::query()->create([
            'barbershop_id' => $barbershop->id,
            'client_email' => $email,
            'consultation_type' => 'ai',
            'face_shape' => $result->face_shape,
            'hair_type' => $result->hair_type,
            'results_json' => $result->toArray(),
            'is_manual_override' => false,
        ]);

        return $result;
    }
}
