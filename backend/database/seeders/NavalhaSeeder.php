<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Catalog\Models\Professional;
use Domain\Catalog\Models\Service;
use Domain\Identity\Enums\UserRole;
use Domain\Identity\Models\User;
use Domain\Tenant\Models\Barbershop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NavalhaSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $shop = Barbershop::query()->updateOrCreate(
                ['slug' => 'navalha'],
                ['name' => 'Navalha', 'active' => true],
            );

            foreach ($this->professionals() as $professional) {
                $user = User::query()->updateOrCreate(
                    ['email' => $professional['email']],
                    [
                        'barbershop_id' => $shop->id,
                        'role' => UserRole::Barber,
                        'name' => $professional['name'],
                        'password' => 'password',
                    ],
                );

                Professional::query()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'barbershop_id' => $shop->id,
                        'name' => $professional['name'],
                        'specialty' => $professional['specialty'],
                        'active' => true,
                    ],
                );
            }

            foreach ($this->services() as $service) {
                Service::query()->updateOrCreate(
                    [
                        'barbershop_id' => $shop->id,
                        'name' => $service['name'],
                    ],
                    [
                        'price' => $service['price'],
                        'duration_in_minutes' => $service['duration_in_minutes'],
                        'active' => true,
                    ],
                );
            }
        });
    }

    /**
     * @return list<array{name: string, specialty: string, email: string}>
     */
    private function professionals(): array
    {
        return [
            [
                'name' => 'Léo Martins',
                'specialty' => 'Fade & textura',
                'email' => 'leo.martins@navalha.test',
            ],
            [
                'name' => 'Rafa Santos',
                'specialty' => 'Barba & clássico',
                'email' => 'rafa.santos@navalha.test',
            ],
        ];
    }

    /**
     * @return list<array{name: string, duration_in_minutes: int, price: string}>
     */
    private function services(): array
    {
        return [
            ['name' => 'Corte assinatura', 'duration_in_minutes' => 45, 'price' => '65.00'],
            ['name' => 'Degradê premium', 'duration_in_minutes' => 50, 'price' => '75.00'],
            ['name' => 'Barboterapia', 'duration_in_minutes' => 35, 'price' => '55.00'],
            ['name' => 'Ritual completo', 'duration_in_minutes' => 80, 'price' => '110.00'],
        ];
    }
}
