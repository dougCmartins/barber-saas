<?php

declare(strict_types=1);

namespace Domain\Catalog\Models;

use Domain\Tenant\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

final class Service extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'barbershop_id',
        'name',
        'description',
        'price',
        'duration_in_minutes',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration_in_minutes' => 'integer',
            'active' => 'boolean',
        ];
    }
}
