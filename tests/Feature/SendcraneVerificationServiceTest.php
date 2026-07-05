<?php

namespace Tests\Feature;

use App\Services\SendcraneVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SendcraneVerificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_verification_email_using_sendcrane_notification_template(): void
    {
        config()->set('services.sendcrane.base_url', 'https://sendcrane.com/api/v1');
        config()->set('services.sendcrane.endpoint', '/email/send');
        config()->set('services.sendcrane.api_key', 'test-sendcrane-key');
        config()->set('services.sendcrane.sandbox', false);
        config()->set('services.sendcrane.verification_template_type', 'notification');

        Http::fake([
            'sendcrane.com/api/v1/email/send' => Http::response(['ok' => true], 200),
        ]);

        $sent = app(SendcraneVerificationService::class)->send(
            'user@example.com',
            'John Doe',
            'https://shamasms.com/email/verify/1/test-hash',
        );

        $this->assertTrue($sent);

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'https://sendcrane.com/api/v1/email/send'
                && $request->hasHeader('Authorization', 'Bearer test-sendcrane-key')
                && $request['to'] === 'user@example.com'
                && $request['template_type'] === 'notification'
                && $request['variables']['user_name'] === 'John Doe'
                && $request['variables']['verification_url'] === 'https://shamasms.com/email/verify/1/test-hash';
        });
    }
}
