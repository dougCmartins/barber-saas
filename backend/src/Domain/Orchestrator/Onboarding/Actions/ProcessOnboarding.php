<?php

declare(strict_types=1);

namespace Domain\Orchestrator\Onboarding\Actions;

use Domain\Identity\Actions\CreateUser;
use Domain\Identity\Data\CreateUserData;
use Domain\Identity\Enums\UserRole;
use Domain\Orchestrator\Onboarding\Data\OnboardingRequestData;
use Domain\Orchestrator\Onboarding\Data\OnboardingResultData;
use Domain\Tenant\Actions\CreateBarbershop;
use Domain\Tenant\Data\CreateBarbershopData;
use Illuminate\Support\Facades\DB;

final class ProcessOnboarding
{
    public function __construct(
        private readonly CreateBarbershop $createBarbershop,
        private readonly CreateUser $createUser,
    ) {
    }

    public function handle(OnboardingRequestData $data): OnboardingResultData
    {
        return DB::transaction(function () use ($data): OnboardingResultData {
            $shop = $this->createBarbershop->handle(CreateBarbershopData::from([
                'name' => $data->shop_name,
                'slug' => $data->shop_slug,
            ]));

            $owner = $this->createUser->handle(CreateUserData::from([
                'name' => $data->owner_name,
                'email' => $data->owner_email,
                'password' => $data->owner_password,
                'role' => UserRole::Owner,
                'barbershop_id' => $shop->id,
            ]));

            return new OnboardingResultData(shop: $shop, owner: $owner);
        });
    }
}
