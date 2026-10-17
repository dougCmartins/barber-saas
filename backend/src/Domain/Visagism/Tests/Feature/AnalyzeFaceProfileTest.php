<?php

declare(strict_types=1);

namespace Domain\Visagism\Tests\Feature;

use Domain\Identity\Enums\UserRole;
use Domain\Identity\Models\User;
use Domain\Tenant\Models\Barbershop;
use Domain\Visagism\Models\VisagismAnalysis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

final class AnalyzeFaceProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_analyze_returns_mock_diagnosis_and_persists_ai_row(): void
    {
        $graph = $this->graph();
        Passport::actingAs($graph['client']);

        $response = $this->postJson('/api/barbershops/north-shop/visagism/analyze', [
            'image' => 'data:image/jpeg;base64,/9j/mock',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('face_shape', 'square');
        $response->assertJsonPath('face_geometry_summary', 'Mandíbula marcada e testa larga.');
        $response->assertJsonPath('hair_type', 'wavy');
        $response->assertJsonPath('suggestions.0.match_percentage', 98);
        $response->assertJsonPath('upsell_products.0.name', 'Pomada Modeladora');
        $response->assertJsonMissingPath('code');

        $this->assertDatabaseHas('visagism_analyses', [
            'client_email' => 'ana@example.com',
            'consultation_type' => 'ai',
            'face_shape' => 'square',
        ]);
    }

    public function test_second_ai_analyze_same_day_is_rate_limited(): void
    {
        $graph = $this->graph();
        Passport::actingAs($graph['client']);

        $this->postJson('/api/barbershops/north-shop/visagism/analyze')->assertCreated();
        $second = $this->postJson('/api/barbershops/north-shop/visagism/analyze');

        $second->assertStatus(429);
        $second->assertJsonPath('code', 'VISAGISM_LIMIT_EXCEEDED');
    }

    public function test_analyze_soft_deletes_active_manual(): void
    {
        $graph = $this->graph();
        Passport::actingAs($graph['client']);

        $manual = VisagismAnalysis::query()->create([
            'barbershop_id' => $graph['barbershop_id'],
            'client_email' => 'ana@example.com',
            'consultation_type' => 'manual',
            'reference_photos' => ['data:image/jpeg;base64,a'],
            'desired_notes' => 'Fade',
            'disliked_notes' => 'Bowl cut',
        ]);

        $this->postJson('/api/barbershops/north-shop/visagism/analyze')->assertCreated();

        $this->assertSoftDeleted($manual);
        $this->assertDatabaseHas('visagism_analyses', [
            'client_email' => 'ana@example.com',
            'consultation_type' => 'ai',
            'deleted_at' => null,
        ]);
    }

    public function test_coily_texture_returns_hydration_products(): void
    {
        $graph = $this->graph();
        Passport::actingAs($graph['client']);

        $response = $this->postJson('/api/barbershops/north-shop/visagism/analyze', [
            'hair_texture' => 'coily',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('hair_type', 'coily');
        $response->assertJsonPath('upsell_products.0.name', 'Leave-in Definição');
    }

    public function test_guest_cannot_analyze(): void
    {
        $this->graph();

        $this->postJson('/api/barbershops/north-shop/visagism/analyze')->assertUnauthorized();
    }

    public function test_invalid_hair_texture_is_rejected(): void
    {
        $graph = $this->graph();
        Passport::actingAs($graph['client']);

        $response = $this->postJson('/api/barbershops/north-shop/visagism/analyze', [
            'hair_texture' => 'bald',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonPath('code', 'VALIDATION_FAILED');
    }

    /**
     * @return array{client: User, barbershop_id: int}
     */
    private function graph(): array
    {
        $shop = Barbershop::query()->create([
            'name' => 'North Shop',
            'slug' => 'north-shop',
        ]);
        $client = User::factory()->create([
            'barbershop_id' => null,
            'role' => UserRole::Client,
            'email' => 'ana@example.com',
            'name' => 'Ana Client',
        ]);

        return [
            'client' => $client,
            'barbershop_id' => (int) $shop->id,
        ];
    }
}
