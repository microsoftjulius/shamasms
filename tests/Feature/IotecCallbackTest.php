<?php

namespace Tests\Feature;

use App\Models\SmsCreditTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IotecCallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_iotec_success_callback_credits_matching_purchase_once(): void
    {
        $user = User::factory()->create(['sms_balance' => 0]);

        $transaction = SmsCreditTransaction::query()->create([
            'user_id' => $user->id,
            'type' => 'purchase',
            'amount' => 10000,
            'credits' => 285,
            'phone' => '256700000000',
            'provider' => 'iotec',
            'status' => 'SentToVendor',
            'metadata' => ['external_id' => 'shamasms-test-1'],
        ]);

        $payload = [
            'id' => 'iotec-request-1',
            'externalId' => 'shamasms-test-1',
            'status' => 'Success',
            'statusMessage' => 'Payment completed',
        ];

        $this->postJson('/api/iotec/callback', $payload)
            ->assertOk()
            ->assertJsonPath('message', 'Callback accepted.');

        $this->postJson('/api/iotec/callback', $payload)->assertOk();

        $transaction->refresh();

        $this->assertSame(285, $user->fresh()->sms_balance);
        $this->assertSame('success', $transaction->status);
        $this->assertSame('iotec-request-1', $transaction->provider_reference);
        $this->assertSame('Payment completed', data_get($transaction->metadata, 'callback.statusMessage'));
        $this->assertNotNull(data_get($transaction->metadata, 'credited_at'));
    }

    public function test_iotec_failed_callback_updates_status_without_crediting(): void
    {
        $user = User::factory()->create(['sms_balance' => 0]);

        $transaction = SmsCreditTransaction::query()->create([
            'user_id' => $user->id,
            'type' => 'purchase',
            'amount' => 10000,
            'credits' => 285,
            'phone' => '256700000000',
            'provider' => 'iotec',
            'provider_reference' => 'iotec-request-2',
            'status' => 'SentToVendor',
            'metadata' => [],
        ]);

        $this->postJson('/api/iotec/callback', [
            'id' => 'iotec-request-2',
            'status' => 'Failed',
            'statusMessage' => 'User declined',
        ])->assertOk();

        $this->assertSame(0, $user->fresh()->sms_balance);
        $this->assertSame('failed', $transaction->fresh()->status);
    }
}
