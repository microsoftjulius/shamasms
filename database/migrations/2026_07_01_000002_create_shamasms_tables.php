<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_groups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('sender_id')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'name']);
        });

        Schema::create('contacts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_group_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('phone');
            $table->string('var1')->nullable();
            $table->string('var2')->nullable();
            $table->string('var3')->nullable();
            $table->string('var4')->nullable();
            $table->string('var5')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'phone']);
        });

        Schema::create('sms_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('sender_id', 32);
            $table->text('body');
            $table->string('mode')->default('standard');
            $table->string('status')->default('draft');
            $table->unsignedSmallInteger('segments')->default(1);
            $table->unsignedInteger('recipient_count')->default(0);
            $table->timestamp('scheduled_at')->nullable();
            $table->unsignedSmallInteger('schedule_repeat_count')->nullable();
            $table->string('schedule_repeat_unit')->nullable();
            $table->time('schedule_time')->nullable();
            $table->string('external_reference')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });

        Schema::create('sms_recipients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sms_message_id')->constrained()->cascadeOnDelete();
            $table->string('phone');
            $table->string('name')->nullable();
            $table->string('var1')->nullable();
            $table->string('var2')->nullable();
            $table->string('var3')->nullable();
            $table->string('var4')->nullable();
            $table->string('var5')->nullable();
            $table->text('rendered_body')->nullable();
            $table->string('status')->default('pending');
            $table->string('provider_reference')->nullable();
            $table->timestamps();
        });

        Schema::create('sms_credit_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->unsignedInteger('amount')->default(0);
            $table->unsignedInteger('credits')->default(0);
            $table->string('phone')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_reference')->nullable();
            $table->string('status')->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('api_keys', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('key_hash', 128);
            $table->string('plain_text_key')->nullable();
            $table->string('mode')->default('sandbox');
            $table->timestamp('last_used_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['key_hash', 'is_active']);
        });

        Schema::create('integration_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('provider');
            $table->string('label');
            $table->string('base_url')->nullable();
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->string('username')->nullable();
            $table->text('password')->nullable();
            $table->boolean('is_sandbox')->default(true);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['provider', 'is_active']);
        });

        Schema::create('me2u_transfers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('from_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('to_user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('credits');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('me2u_transfers');
        Schema::dropIfExists('integration_settings');
        Schema::dropIfExists('api_keys');
        Schema::dropIfExists('sms_credit_transactions');
        Schema::dropIfExists('sms_recipients');
        Schema::dropIfExists('sms_messages');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('contact_groups');
    }
};
