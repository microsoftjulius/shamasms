<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_tiers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('min_amount');
            $table->unsignedInteger('sms_unit_price');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['is_active', 'min_amount']);
        });

        Schema::create('adverts', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adverts');
        Schema::dropIfExists('price_tiers');
    }
};
