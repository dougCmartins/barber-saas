<?php

declare(strict_types=1);

namespace Domain\Tenant\Tests\Feature;

use Domain\Identity\Enums\UserRole;
use Domain\Identity\Models\User;
use Domain\Tenant\Models\Barbershop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_only_sees_users_from_their_barbershop(): void
    {
        $shop = Barbershop::query()->create([
            'name' => 'North Shop',
            'slug' => 'north-shop',
        ]);
        $otherShop = Barbershop::query()->create([
            'name' => 'South Shop',
            'slug' => 'south-shop',
        ]);
        $owner = User::factory()->create([
            'barbershop_id' => $shop->id,
            'role' => UserRole::Owner,
        ]);
        User::factory()->create([
            'barbershop_id' => $otherShop->id,
            'role' => UserRole::Barber,
        ]);

        $this->actingAs($owner);

        $users = User::query()->get();

        $this->assertCount(1, $users);
        $this->assertTrue($users->first()->is($owner));
        $this->assertTrue($owner->barbershop->is($shop));
    }

    public function test_super_admin_sees_users_from_every_barbershop(): void
    {
        $shop = Barbershop::query()->create([
            'name' => 'North Shop',
            'slug' => 'north-shop',
        ]);
        $otherShop = Barbershop::query()->create([
            'name' => 'South Shop',
            'slug' => 'south-shop',
        ]);
        $admin = User::factory()->create([
            'role' => UserRole::SuperAdmin,
        ]);
        User::factory()->create([
            'barbershop_id' => $shop->id,
            'role' => UserRole::Owner,
        ]);
        User::factory()->create([
            'barbershop_id' => $otherShop->id,
            'role' => UserRole::Barber,
        ]);

        $this->actingAs($admin);

        $this->assertCount(3, User::query()->get());
    }

    public function test_guest_query_is_not_limited_to_a_barbershop(): void
    {
        $shop = Barbershop::query()->create([
            'name' => 'North Shop',
            'slug' => 'north-shop',
        ]);
        $otherShop = Barbershop::query()->create([
            'name' => 'South Shop',
            'slug' => 'south-shop',
        ]);
        User::factory()->create([
            'barbershop_id' => $shop->id,
            'role' => UserRole::Owner,
        ]);
        User::factory()->create([
            'barbershop_id' => $otherShop->id,
            'role' => UserRole::Client,
        ]);

        $this->assertCount(2, User::query()->get());
    }
}
