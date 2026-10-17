<?php

declare(strict_types=1);

namespace Domain\Schedule\Models;

use Domain\Catalog\Models\Professional;
use Domain\Catalog\Models\Service;
use Domain\Schedule\Enums\AppointmentStatus;
use Domain\Tenant\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

final class Appointment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'barbershop_id',
        'professional_id',
        'service_id',
        'client_name',
        'client_email',
        'starts_at',
        'ends_at',
        'price',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'price' => 'decimal:2',
            'status' => AppointmentStatus::class,
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function appointmentServices(): HasMany
    {
        return $this->hasMany(AppointmentService::class)->orderBy('position');
    }

    /**
     * @param  Builder<Appointment>  $query
     * @return Builder<Appointment>
     */
    public function scopeByPeriod(Builder $query, ?string $from, ?string $to): Builder
    {
        if (! empty($from)) {
            $query->where('starts_at', '>=', Carbon::parse($from)->startOfDay());
        }

        if (! empty($to)) {
            $query->where('starts_at', '<=', Carbon::parse($to)->endOfDay());
        }

        return $query;
    }

    /**
     * @param  Builder<Appointment>  $query
     * @param  list<string>  $statuses
     * @return Builder<Appointment>
     */
    public function scopeByStatus(Builder $query, array $statuses): Builder
    {
        if (! $statuses) {
            return $query;
        }

        return $query->whereIn('status', $statuses);
    }

    /**
     * @param  Builder<Appointment>  $query
     * @return Builder<Appointment>
     */
    public function scopeByProfessional(Builder $query, ?int $professionalId): Builder
    {
        if (empty($professionalId)) {
            return $query;
        }

        return $query->where('professional_id', $professionalId);
    }
}
