<?php

declare(strict_types=1);

namespace Domain\Catalog\Models;

use Domain\Tenant\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

final class Professional extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'barbershop_id',
        'user_id',
        'name',
        'specialty',
        'avatar_url',
        'experience_years',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'experience_years' => 'integer',
            'active' => 'boolean',
        ];
    }
}
