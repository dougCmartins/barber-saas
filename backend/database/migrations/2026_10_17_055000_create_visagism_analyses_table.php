<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visagism_analyses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('barbershop_id')->constrained('barbershops')->cascadeOnDelete();
            $table->string('client_email');
            $table->string('consultation_type');
            $table->string('face_shape')->nullable();
            $table->string('hair_type')->nullable();
            $table->json('results_json')->nullable();
            $table->json('reference_photos')->nullable();
            $table->text('desired_notes')->nullable();
            $table->text('disliked_notes')->nullable();
            $table->boolean('is_manual_override')->default(false);
            $table->text('feedback_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['barbershop_id', 'client_email', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visagism_analyses');
    }
};
