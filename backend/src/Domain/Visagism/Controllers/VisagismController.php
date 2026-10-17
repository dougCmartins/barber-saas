<?php

declare(strict_types=1);

namespace Domain\Visagism\Controllers;

use Domain\Visagism\Actions\AnalyzeFaceProfile;
use Domain\Visagism\Actions\CheckExistingVisagism;
use Domain\Visagism\Actions\ForceDeleteVisagismAnalysis;
use Domain\Visagism\Actions\OverrideVisagismAnalysis;
use Domain\Visagism\Actions\RestoreVisagismAnalysis;
use Domain\Visagism\Actions\SaveManualConsultation;
use Domain\Visagism\Data\AnalyzeFaceData;
use Domain\Visagism\Data\BarbershopSlugData;
use Domain\Visagism\Data\ForceDeleteVisagismAnalysisData;
use Domain\Visagism\Data\ManualConsultationData;
use Domain\Visagism\Data\OverrideVisagismAnalysisData;
use Domain\Visagism\Data\RestoreVisagismAnalysisData;
use Domain\Visagism\Data\SaveManualConsultationData;
use Domain\Visagism\Data\VisagismActiveRecordData;
use Domain\Visagism\Data\VisagismExistingStateData;
use Domain\Visagism\Data\VisagismResultData;
use Illuminate\Http\Response;

final class VisagismController
{
    public function existing(
        string $barbershop_slug,
        CheckExistingVisagism $action,
    ): VisagismExistingStateData {
        return $action->handle(BarbershopSlugData::from(
            BarbershopSlugData::validate(['barbershop_slug' => $barbershop_slug])
        ));
    }

    public function analyze(
        string $barbershop_slug,
        AnalyzeFaceData $data,
        AnalyzeFaceProfile $action,
    ): VisagismResultData {
        return $action->handle(AnalyzeFaceData::from(
            AnalyzeFaceData::validate([
                'barbershop_slug' => $barbershop_slug,
                'image' => $data->image,
                'age' => $data->age,
                'hair_texture' => $data->hair_texture,
                'beard_density' => $data->beard_density,
            ])
        ));
    }

    public function override(
        string $barbershop_slug,
        OverrideVisagismAnalysisData $data,
        OverrideVisagismAnalysis $action,
    ): VisagismResultData {
        return $action->handle(OverrideVisagismAnalysisData::from(
            OverrideVisagismAnalysisData::validate([
                'barbershop_slug' => $barbershop_slug,
                'face_shape' => $data->face_shape,
                'hair_type' => $data->hair_type,
                'feedback_reason' => $data->feedback_reason,
            ])
        ));
    }

    public function manual(
        string $barbershop_slug,
        SaveManualConsultationData $data,
        SaveManualConsultation $action,
    ): ManualConsultationData {
        return $action->handle(SaveManualConsultationData::from(
            SaveManualConsultationData::validate([
                'barbershop_slug' => $barbershop_slug,
                'reference_photos' => $data->reference_photos,
                'desired_notes' => $data->desired_notes,
                'disliked_notes' => $data->disliked_notes,
            ])
        ));
    }

    public function restore(
        string $barbershop_slug,
        RestoreVisagismAnalysisData $data,
        RestoreVisagismAnalysis $action,
    ): VisagismActiveRecordData {
        return $action->handle(RestoreVisagismAnalysisData::from(
            RestoreVisagismAnalysisData::validate([
                'barbershop_slug' => $barbershop_slug,
                'consultation_type' => $data->consultation_type,
            ])
        ));
    }

    public function forceDelete(
        string $barbershop_slug,
        ForceDeleteVisagismAnalysisData $data,
        ForceDeleteVisagismAnalysis $action,
    ): Response {
        $action->handle(ForceDeleteVisagismAnalysisData::from(
            ForceDeleteVisagismAnalysisData::validate([
                'barbershop_slug' => $barbershop_slug,
                'consultation_type' => $data->consultation_type,
            ])
        ));

        return response()->noContent();
    }
}
