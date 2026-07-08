<?php

namespace Tests\Feature;

use App\Models\IntegrationSetting;
use App\Models\User;
use App\Services\UgsmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UgsmsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_ugsms_service_uses_v2_api_key_header_and_message_body(): void
    {
        Http::fake([
            'https://ugsms.test/api/v2/sms/send' => Http::response([
                'success' => true,
                'data' => ['message_id' => 'msg-123'],
            ]),
        ]);

        IntegrationSetting::query()->create([
            'provider' => 'ugsms',
            'label' => 'UG SMS',
            'base_url' => 'https://ugsms.test/api/v2',
            'api_key' => 'test-key',
            'is_sandbox' => false,
            'is_active' => true,
        ]);

        $result = app(UgsmsService::class)->send('SHAMA', '256770000000', 'Hello');

        $this->assertTrue($result['ok']);
        $this->assertSame('msg-123', $result['reference']);

        Http::assertSent(fn ($request) => $request->hasHeader('X-API-Key', 'test-key')
            && $request->url() === 'https://ugsms.test/api/v2/sms/send'
            && $request['numbers'] === '256770000000'
            && $request['message_body'] === 'Hello'
            && $request['sender_id'] === 'SHAMA');
    }

    public function test_api_send_does_not_deduct_shamasms_credits_when_ugsms_fails(): void
    {
        Http::fake([
            'https://ugsms.test/api/v2/sms/send' => Http::response(['error' => 'Insufficient balance'], 400),
        ]);

        IntegrationSetting::query()->create([
            'provider' => 'ugsms',
            'label' => 'UG SMS',
            'base_url' => 'https://ugsms.test/api/v2',
            'api_key' => 'test-key',
            'is_sandbox' => false,
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'email' => 'sender@example.com',
            'password' => 'secret',
            'sms_balance' => 5,
        ]);

        $response = $this->postJson('/api/v1/sms/send', [
            'email' => 'sender@example.com',
            'password' => 'secret',
            'message' => 'Hello',
            'phone' => '256770000000',
        ]);

        $response->assertAccepted()
            ->assertJsonPath('sent_count', 0)
            ->assertJsonPath('failed_count', 1)
            ->assertJsonPath('credits_used', 0)
            ->assertJsonPath('balance', 5);

        $this->assertSame(5, $user->fresh()->sms_balance);
    }

    public function test_ugsms_balance_uses_configured_unit_price_for_credit_balance(): void
    {
        Http::fake([
            'https://ugsms.test/api/v2/account/balance' => Http::response([
                'balance' => 10500,
            ]),
        ]);

        IntegrationSetting::query()->create([
            'provider' => 'sms_gateway',
            'label' => 'UG SMS',
            'base_url' => 'https://ugsms.test/api/v2',
            'api_key' => 'test-key',
            'is_sandbox' => false,
            'is_active' => true,
            'metadata' => ['unit_price' => 35],
        ]);

        $result = app(UgsmsService::class)->balance();

        $this->assertTrue($result['ok']);
        $this->assertSame(10500, $result['balance']);
        $this->assertSame(35, $result['unit_price']);
        $this->assertSame(300, $result['credits']);
    }

    public function test_ugsms_deposit_posts_to_payments_endpoint(): void
    {
        Http::fake([
            'https://ugsms.test/api/v2/payments' => Http::response([
                'reference' => 'pay-123',
                'status' => 'pending',
                'message' => 'Payment prompt sent',
            ]),
        ]);

        IntegrationSetting::query()->create([
            'provider' => 'sms_gateway',
            'label' => 'UG SMS',
            'base_url' => 'https://ugsms.test/api/v2',
            'api_key' => 'test-key',
            'is_sandbox' => false,
            'is_active' => true,
            'metadata' => ['unit_price' => 35],
        ]);

        $result = app(UgsmsService::class)->requestDeposit(5000, '0702913454', 'https://example.com/api/ugsms/payment-callback');

        $this->assertTrue($result['ok']);
        $this->assertSame('pay-123', $result['reference']);

        Http::assertSent(fn ($request) => $request->hasHeader('X-API-Key', 'test-key')
            && $request->url() === 'https://ugsms.test/api/v2/payments'
            && $request['amount'] === 5000
            && $request['phone_number'] === '0702913454'
            && $request['callback_url'] === 'https://example.com/api/ugsms/payment-callback');
    }

    public function test_ugsms_payment_callback_updates_deposit_status(): void
    {
        $transaction = \App\Models\SmsCreditTransaction::query()->create([
            'type' => 'ugsms_deposit',
            'amount' => 5000,
            'credits' => 142,
            'phone' => '0702913454',
            'provider' => 'ugsms',
            'provider_reference' => 'pay-123',
            'status' => 'pending',
            'metadata' => [],
        ]);

        $this->postJson('/api/ugsms/payment-callback', [
            'reference' => 'pay-123',
            'status' => 'success',
            'message' => 'Payment received',
        ])->assertOk()
            ->assertJsonPath('message', 'Callback accepted.');

        $transaction->refresh();

        $this->assertSame('success', $transaction->status);
        $this->assertSame('Payment received', data_get($transaction->metadata, 'callback.message'));
    }
}
