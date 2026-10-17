<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barbershops', function (Blueprint $table) {
            $table->string('address')->nullable()->after('active');
            $table->string('city')->nullable()->after('address');
            $table->string('closing_time')->nullable()->after('city');
            $table->decimal('rating', 2, 1)->default(5.0)->after('closing_time');
            $table->integer('total_reviews')->default(0)->after('rating');
            $table->string('banner_url')->nullable()->after('total_reviews');
        });
    }

    public function down(): void
    {
        Schema::table('barbershops', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'city',
                'closing_time',
                'rating',
                'total_reviews',
                'banner_url',
            ]);
        });
    }
};
