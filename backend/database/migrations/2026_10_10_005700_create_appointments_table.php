<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barbershop_id')->constrained('barbershops');
            $table->foreignId('professional_id')->constrained('professionals');
            $table->foreignId('service_id')->constrained('services');
            $table->string('client_name');
            $table->string('client_email');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->decimal('price', 10, 2);
            $table->string('status');
            $table->timestamps();

            $table->index(['professional_id', 'starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
