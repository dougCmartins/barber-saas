<?php

declare(strict_types=1);

namespace Domain\Tenant\Models;

use Domain\Identity\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Barbershop extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'active',
        'address',
        'city',
        'closing_time',
        'rating',
        'total_reviews',
        'banner_url',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'rating' => 'float',
            'total_reviews' => 'integer',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
