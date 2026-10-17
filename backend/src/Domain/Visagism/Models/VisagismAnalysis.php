<?php

declare(strict_types=1);

namespace Domain\Visagism\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class VisagismAnalysis extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'barbershop_id',
        'client_email',
        'consultation_type',
        'face_shape',
        'hair_type',
        'results_json',
        'reference_photos',
        'desired_notes',
        'disliked_notes',
        'is_manual_override',
        'feedback_reason',
    ];

    protected function casts(): array
    {
        return [
            'results_json' => 'array',
            'reference_photos' => 'array',
            'is_manual_override' => 'boolean',
        ];
    }
}
