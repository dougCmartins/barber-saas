<?php

declare(strict_types=1);

namespace Domain\Tenant\Models\Concerns;

use Domain\Identity\Enums\UserRole;
use Domain\Tenant\Models\Barbershop;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public function barbershop(): BelongsTo
    {
        return $this->belongsTo(Barbershop::class);
    }

    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder): void {
            if (! auth()->hasUser()) {
                return;
            }

            $user = auth()->user();

            if ($user->role === UserRole::SuperAdmin) {
                return;
            }

            $builder->where(
                $builder->getModel()->getTable().'.barbershop_id',
                $user->barbershop_id,
            );
        });
    }
}
