<?php

declare(strict_types=1);

namespace Domain\Identity\Tests\Unit;

use Domain\Identity\Enums\UserRole;
use Domain\Identity\Models\User;
use Domain\Identity\Policies\BarbershopPolicy;
use Domain\Tenant\Models\Barbershop;
use Tests\TestCase;

final class BarbershopPolicyTest extends TestCase
{
    private BarbershopPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new BarbershopPolicy;
    }

    public function test_super_admin_and_owner_can_update_the_shop(): void
    {
        $shop = $this->shop(10);
        $owner = $this->user(UserRole::Owner, 10);
        $admin = $this->user(UserRole::SuperAdmin, null);

        $this->assertTrue($this->policy->update($admin, $shop));
        $this->assertTrue($this->policy->update($owner, $shop));
        $this->assertTrue($this->policy->delete($owner, $shop));
    }

    public function test_barber_and_receptionist_cannot_update_the_shop(): void
    {
        $shop = $this->shop(10);
        $barber = $this->user(UserRole::Barber, 10);
        $receptionist = $this->user(UserRole::Receptionist, 10);

        $this->assertFalse($this->policy->update($barber, $shop));
        $this->assertFalse($this->policy->update($receptionist, $shop));
        $this->assertFalse($this->policy->delete($barber, $shop));
        $this->assertTrue($this->policy->view($barber, $shop));
        $this->assertTrue($this->policy->view($receptionist, $shop));
    }

    public function test_owner_cannot_update_another_shop(): void
    {
        $shop = $this->shop(10);
        $owner = $this->user(UserRole::Owner, 20);

        $this->assertFalse($this->policy->update($owner, $shop));
        $this->assertFalse($this->policy->view($owner, $shop));
    }

    private function shop(int $id): Barbershop
    {
        $shop = new Barbershop([
            'name' => 'North Shop',
            'slug' => 'north-shop',
        ]);
        $shop->id = $id;

        return $shop;
    }

    private function user(UserRole $role, ?int $barbershopId): User
    {
        $user = new User([
            'name' => 'Ada',
            'email' => 'ada@example.com',
            'role' => $role,
            'barbershop_id' => $barbershopId,
        ]);

        return $user;
    }
}
