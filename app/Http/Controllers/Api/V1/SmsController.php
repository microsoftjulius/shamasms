<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SmsMessage;
use App\Models\User;
use App\Services\MessagePersonalizer;
use App\Services\UgsmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SmsController extends Controller
{
    public function balance(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid login credentials.'], 401);
        }

        return response()->json([
            'balance' => $user->sms_balance,
            'unit' => 'sms_credits',
            'sms_unit_price' => $user->sms_unit_price,
            'currency' => 'UGX',
        ]);
    }

    public function send(Request $request, MessagePersonalizer $personalizer, UgsmsService $ugsms)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'sender_id' => ['nullable', 'string', 'max:32'],
            'message' => ['nullable', 'string', 'max:765', 'required_without:message_body'],
            'message_body' => ['nullable', 'string', 'max:765', 'required_without:message'],
            'recipients' => ['nullable', 'array', 'min:1', 'required_without_all:numbers,to,phone'],
            'recipients.*.phone' => ['required', 'string', 'max:30'],
            'recipients.*.name' => ['nullable', 'string', 'max:160'],
            'recipients.*.var1' => ['nullable', 'string', 'max:160'],
            'recipients.*.var2' => ['nullable', 'string', 'max:160'],
            'recipients.*.var3' => ['nullable', 'string', 'max:160'],
            'recipients.*.var4' => ['nullable', 'string', 'max:160'],
            'recipients.*.var5' => ['nullable', 'string', 'max:160'],
            'numbers' => ['nullable'],
            'to' => ['nullable'],
            'phone' => ['nullable', 'string', 'max:30'],
            'personalized' => ['boolean'],
            'sandbox' => ['boolean'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid login credentials.'], 401);
        }

        return $this->dispatchMessage($user, $this->normalizePayload($data), $personalizer, $ugsms);
    }

    protected function dispatchMessage(User $user, array $data, MessagePersonalizer $personalizer, UgsmsService $ugsms)
    {
        $segments = $personalizer->segments($data['message']);
        $cost = $segments * count($data['recipients']);

        if (! ($data['sandbox'] ?? false) && $user->sms_balance < $cost) {
            return response()->json(['message' => 'Insufficient SMS balance.'], 422);
        }

        $message = SmsMessage::query()->create([
            'user_id' => $user->id,
            'sender_id' => $data['sender_id'] ?? null,
            'body' => $data['message'],
            'mode' => ($data['personalized'] ?? false) ? 'personalized' : 'standard',
            'status' => ($data['sandbox'] ?? false) ? 'sandbox' : 'sending',
            'segments' => $segments,
            'recipient_count' => count($data['recipients']),
            'external_reference' => $requestId = 'api-'.str()->uuid(),
        ]);

        $sentCount = 0;
        $failedCount = 0;

        foreach ($data['recipients'] as $recipient) {
            $body = ($data['personalized'] ?? false) ? $personalizer->render($data['message'], $recipient) : $data['message'];
            $result = ($data['sandbox'] ?? false)
                ? ['ok' => true, 'reference' => 'sandbox-'.str()->uuid()]
                : $ugsms->send($data['sender_id'] ?? null, $recipient['phone'], $body);
            $sent = (bool) ($result['ok'] ?? false);
            $sent ? $sentCount++ : $failedCount++;

            $message->recipients()->create([
                ...$recipient,
                'rendered_body' => $body,
                'status' => $sent ? 'sent' : 'failed',
                'provider_reference' => $result['reference'] ?? null,
            ]);
        }

        if (! ($data['sandbox'] ?? false)) {
            $charged = $segments * $sentCount;

            if ($charged > 0) {
                $user->decrement('sms_balance', $charged);
            }

            $message->update(['status' => $sentCount === 0 ? 'failed' : ($failedCount > 0 ? 'partial' : 'sent')]);
        }

        return response()->json([
            'message' => 'Accepted',
            'reference' => $requestId,
            'segments' => $segments,
            'recipient_count' => count($data['recipients']),
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'credits_used' => ($data['sandbox'] ?? false) ? 0 : $segments * $sentCount,
            'balance' => $user->fresh()->sms_balance,
        ], 202);
    }

    protected function normalizePayload(array $data): array
    {
        $data['message'] = $data['message'] ?? $data['message_body'] ?? '';

        if (! empty($data['recipients'])) {
            return $data;
        }

        $numbers = $data['numbers'] ?? $data['to'] ?? $data['phone'] ?? [];

        if (is_string($numbers)) {
            $numbers = preg_split('/[\s,;]+/', $numbers, flags: PREG_SPLIT_NO_EMPTY);
        }

        $data['recipients'] = collect($numbers)
            ->map(function ($recipient) {
                if (is_array($recipient)) {
                    return [
                        'phone' => $recipient['phone'] ?? $recipient['number'] ?? $recipient['to'] ?? '',
                        'name' => $recipient['name'] ?? null,
                        'var1' => $recipient['var1'] ?? null,
                        'var2' => $recipient['var2'] ?? null,
                        'var3' => $recipient['var3'] ?? null,
                        'var4' => $recipient['var4'] ?? null,
                        'var5' => $recipient['var5'] ?? null,
                    ];
                }

                return ['phone' => (string) $recipient];
            })
            ->filter(fn (array $recipient) => filled($recipient['phone']))
            ->values()
            ->all();

        return $data;
    }
}
