<?php

declare(strict_types=1);

namespace Domain\Visagism\Actions;

use Domain\Identity\Models\User;
use Domain\Visagism\Data\ManualConsultationData;
use Domain\Visagism\Data\SaveManualConsultationData;
use Domain\Visagism\Models\VisagismAnalysis;
use Domain\Visagism\Support\VisagismAnalysisScope;

final class SaveManualConsultation
{
    public function handle(SaveManualConsultationData $data): ManualConsultationData
    {
        /** @var User $user */
        $user = auth()->user();
        $barbershop = VisagismAnalysisScope::resolveBarbershop($data->barbershop_slug);
        $email = (string) $user->email;

        VisagismAnalysisScope::softDeleteOpposite((int) $barbershop->id, $email, 'manual');

        $photos = array_values(array_slice($data->reference_photos, 0, 2));

        $existing = VisagismAnalysisScope::forClientToday((int) $barbershop->id, $email)
            ->where('consultation_type', 'manual')
            ->first();

        if ($existing !== null) {
            $existing->update([
                'reference_photos' => $photos,
                'desired_notes' => $data->desired_notes,
                'disliked_notes' => $data->disliked_notes,
            ]);
            $analysis = $existing->refresh();
        } else {
            $analysis = VisagismAnalysis::query()->create([
                'barbershop_id' => $barbershop->id,
                'client_email' => $email,
                'consultation_type' => 'manual',
                'reference_photos' => $photos,
                'desired_notes' => $data->desired_notes,
                'disliked_notes' => $data->disliked_notes,
                'is_manual_override' => false,
            ]);
        }

        return new ManualConsultationData(
            id: (int) $analysis->id,
            consultation_type: 'manual',
            reference_photos: array_values($analysis->reference_photos ?? []),
            desired_notes: (string) $analysis->desired_notes,
            disliked_notes: (string) $analysis->disliked_notes,
        );
    }
}
