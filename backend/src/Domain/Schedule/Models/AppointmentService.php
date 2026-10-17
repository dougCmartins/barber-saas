<?php

declare(strict_types=1);

namespace Domain\Schedule\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class AppointmentService extends Model
{
    protected $fillable = [
        'appointment_id',
        'service_id',
        'name',
        'duration_in_minutes',
        'price',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'duration_in_minutes' => 'integer',
            'price' => 'decimal:2',
            'position' => 'integer',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
