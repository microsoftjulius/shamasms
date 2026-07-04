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
}
