<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services');
            $table->string('name');
            $table->unsignedInteger('duration_in_minutes');
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('position');
            $table->timestamps();

            $table->index(['appointment_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_services');
    }
};
