<?php

declare(strict_types=1);

namespace Domain\Visagism\Support;

use Domain\Visagism\Data\ManualConsultationData;
use Domain\Visagism\Data\VisagismActiveRecordData;
use Domain\Visagism\Data\VisagismResultData;
use Domain\Visagism\Models\VisagismAnalysis;

final class VisagismAnalysisMapper
{
    public static function toActiveRecord(VisagismAnalysis $analysis): VisagismActiveRecordData
    {
        if ($analysis->consultation_type === 'manual') {
            return new VisagismActiveRecordData(
                id: (int) $analysis->id,
                consultation_type: 'manual',
                manual: new ManualConsultationData(
                    id: (int) $analysis->id,
                    consultation_type: 'manual',
                    reference_photos: array_values($analysis->reference_photos ?? []),
                    desired_notes: (string) $analysis->desired_notes,
                    disliked_notes: (string) $analysis->disliked_notes,
                ),
            );
        }

        /** @var array<string, mixed> $payload */
        $payload = $analysis->results_json ?? [];

        return new VisagismActiveRecordData(
            id: (int) $analysis->id,
            consultation_type: 'ai',
            result: VisagismResultData::from($payload),
        );
    }
}
