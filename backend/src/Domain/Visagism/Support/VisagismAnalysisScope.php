<?php

declare(strict_types=1);

namespace Domain\Visagism\Support;

use Domain\Tenant\Exceptions\BarbershopNotFoundException;
use Domain\Tenant\Models\Barbershop;
use Domain\Visagism\Models\VisagismAnalysis;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

final class VisagismAnalysisScope
{
    public static function resolveBarbershop(string $slug): Barbershop
    {
        $barbershop = Barbershop::query()->where('slug', $slug)->first();

        if ($barbershop === null) {
            throw new BarbershopNotFoundException;
        }

        return $barbershop;
    }

    /**
     * @return Builder<VisagismAnalysis>
     */
    public static function forClientToday(int $barbershopId, string $clientEmail): Builder
    {
        return VisagismAnalysis::query()
            ->where('barbershop_id', $barbershopId)
            ->where('client_email', $clientEmail)
            ->whereDate('created_at', Carbon::today());
    }

    public static function softDeleteOpposite(int $barbershopId, string $clientEmail, string $activeType): void
    {
        $opposite = $activeType === 'ai' ? 'manual' : 'ai';

        self::forClientToday($barbershopId, $clientEmail)
            ->where('consultation_type', $opposite)
            ->delete();
    }
}
