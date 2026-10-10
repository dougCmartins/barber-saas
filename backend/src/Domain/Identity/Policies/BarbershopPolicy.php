<?php

declare(strict_types=1);

namespace Domain\Identity\Policies;

use Domain\Identity\Enums\UserRole;
use Domain\Identity\Models\User;
use Domain\Tenant\Models\Barbershop;

final class BarbershopPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function view(User $user, Barbershop $barbershop): bool
    {
        if ($user->role === UserRole::SuperAdmin) {
            return true;
        }

        if (! $this->isStaff($user)) {
            return false;
        }

        return $this->belongsToShop($user, $barbershop);
    }

    public function create(User $user): bool
    {
        return $this->managesShops($user);
    }

    public function update(User $user, Barbershop $barbershop): bool
    {
        return $this->managesShop($user, $barbershop);
    }

    public function delete(User $user, Barbershop $barbershop): bool
    {
        return $this->managesShop($user, $barbershop);
    }

    private function managesShop(User $user, Barbershop $barbershop): bool
    {
        if ($user->role === UserRole::SuperAdmin) {
            return true;
        }

        if ($user->role !== UserRole::Owner) {
            return false;
        }

        return $this->belongsToShop($user, $barbershop);
    }

    private function managesShops(User $user): bool
    {
        return $user->role === UserRole::SuperAdmin
            || $user->role === UserRole::Owner;
    }

    private function isStaff(User $user): bool
    {
        return in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::Owner,
            UserRole::Barber,
            UserRole::Receptionist,
        ], true);
    }

    private function belongsToShop(User $user, Barbershop $barbershop): bool
    {
        return (int) $user->barbershop_id === (int) $barbershop->id;
    }
}
