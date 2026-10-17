<?php

declare(strict_types=1);

namespace Domain\Visagism\Tests\Feature;

use Domain\Identity\Enums\UserRole;
use Domain\Identity\Models\User;
use Domain\Tenant\Models\Barbershop;
use Domain\Visagism\Models\VisagismAnalysis;
use Domain\Visagism\Support\VisagismResultBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

final class VisagismLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_returns_active_and_trashed_state(): void
    {
        $graph = $this->graph();
        Passport::actingAs($graph['client']);

        $ai = VisagismAnalysis::query()->create([
            'barbershop_id' => $graph['barbershop_id'],
            'client_email' => 'ana@example.com',
            'consultation_type' => 'ai',
            'face_shape' => 'square',
            'hair_type' => 'wavy',
            'results_json' => VisagismResultBuilder::build('square', 'wavy')->toArray(),
        ]);
        $ai->delete();

        $manual = VisagismAnalysis::query()->create([
            'barbershop_id' => $graph['barbershop_id'],
            'client_email' => 'ana@example.com',
            'consultation_type' => 'manual',
            'reference_photos' => ['data:image/jpeg;base64,a'],
            'desired_notes' => 'Fade curto',
            'disliked_notes' => 'Muito volume',
        ]);

        $response = $this->getJson('/api/barbershops/north-shop/visagism/existing');

        $response->assertOk();
        $response->assertJsonPath('active.id', $manual->id);
        $response->assertJsonPath('active.consultation_type', 'manual');
        $response->assertJsonPath('trashed_ai.id', $ai->id);
        $response->assertJsonPath('trashed_manual', null);
    }

    public function test_restore_reactivates_trashed_and_soft_deletes_opposite(): void
    {
        $graph = $this->graph();
        Passport::actingAs($graph['client']);

        $ai = VisagismAnalysis::query()->create([
            'barbershop_id' => $graph['barbershop_id'],
            'client_email' => 'ana@example.com',
            'consultation_type' => 'ai',
            'face_shape' => 'square',
            'hair_type' => 'wavy',
            'results_json' => VisagismResultBuilder::build('square', 'wavy')->toArray(),
        ]);
        $ai->delete();

        $manual = VisagismAnalysis::query()->create([
            'barbershop_id' => $graph['barbershop_id'],
            'client_email' => 'ana@example.com',
            'consultation_type' => 'manual',
            'reference_photos' => ['data:image/jpeg;base64,a'],
            'desired_notes' => 'Fade',
            'disliked_notes' => 'Bowl',
        ]);

        $response = $this->postJson('/api/barbershops/north-shop/visagism/restore', [
            'consultation_type' => 'ai',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('consultation_type', 'ai');
        $response->assertJsonPath('result.face_shape', 'square');

        $this->assertNull($ai->refresh()->deleted_at);
        $this->assertSoftDeleted($manual);
    }

    public function test_force_delete_removes_record_permanently(): void
    {
        $graph = $this->graph();
        Passport::actingAs($graph['client']);

        $ai = VisagismAnalysis::query()->create([
            'barbershop_id' => $graph['barbershop_id'],
            'client_email' => 'ana@example.com',
            'consultation_type' => 'ai',
            'face_shape' => 'square',
            'hair_type' => 'wavy',
            'results_json' => VisagismResultBuilder::build('square', 'wavy')->toArray(),
        ]);
        $ai->delete();

        $response = $this->postJson('/api/barbershops/north-shop/visagism/force-delete', [
            'consultation_type' => 'ai',
        ]);

        $response->assertNoContent();
        $this->assertDatabaseMissing('visagism_analyses', ['id' => $ai->id]);
    }

    public function test_override_requires_feedback_and_updates_ai(): void
    {
        $graph = $this->graph();
        Passport::actingAs($graph['client']);

        VisagismAnalysis::query()->create([
            'barbershop_id' => $graph['barbershop_id'],
            'client_email' => 'ana@example.com',
            'consultation_type' => 'ai',
            'face_shape' => 'square',
            'hair_type' => 'wavy',
            'results_json' => VisagismResultBuilder::build('square', 'wavy')->toArray(),
        ]);

        $missing = $this->postJson('/api/barbershops/north-shop/visagism/override', [
            'face_shape' => 'oval',
            'hair_type' => 'straight',
        ]);
        $missing->assertUnprocessable();

        $response = $this->postJson('/api/barbershops/north-shop/visagism/override', [
            'face_shape' => 'oval',
            'hair_type' => 'straight',
            'feedback_reason' => 'A IA errou o formato do meu rosto',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('face_shape', 'oval');
        $response->assertJsonPath('hair_type', 'straight');

        $this->assertDatabaseHas('visagism_analyses', [
            'client_email' => 'ana@example.com',
            'face_shape' => 'oval',
            'is_manual_override' => true,
            'feedback_reason' => 'A IA errou o formato do meu rosto',
        ]);
    }

    public function test_manual_soft_deletes_ai_and_persists_notes(): void
    {
        $graph = $this->graph();
        Passport::actingAs($graph['client']);

        $ai = VisagismAnalysis::query()->create([
            'barbershop_id' => $graph['barbershop_id'],
            'client_email' => 'ana@example.com',
            'consultation_type' => 'ai',
            'face_shape' => 'square',
            'hair_type' => 'wavy',
            'results_json' => VisagismResultBuilder::build('square', 'wavy')->toArray(),
        ]);

        $response = $this->postJson('/api/barbershops/north-shop/visagism/manual', [
            'reference_photos' => ['data:image/jpeg;base64,one', 'data:image/jpeg;base64,two'],
            'desired_notes' => 'Quero um fade médio',
            'disliked_notes' => 'Não gosto de fringe',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('consultation_type', 'manual');
        $response->assertJsonPath('desired_notes', 'Quero um fade médio');
        $response->assertJsonCount(2, 'reference_photos');

        $this->assertSoftDeleted($ai);
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
