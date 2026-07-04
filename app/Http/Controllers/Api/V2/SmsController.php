<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Api\V1\SmsController as V1SmsController;
use App\Models\ApiKey;
use App\Services\MessagePersonalizer;
use App\Services\UgsmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SmsController extends V1SmsController
{
    public function balance(Request $request)
    {
        $apiKey = $this->apiKeyFromRequest($request);

        if (! $apiKey) {
            return response()->json(['message' => 'Invalid API key.'], 401);
        }

        $apiKey->update(['last_used_at' => now()]);

        return response()->json([
            'balance' => $apiKey->user->sms_balance,
            'mode' => $apiKey->mode,
            'unit' => 'sms_credits',
            'sms_unit_price' => $apiKey->user->sms_unit_price,
            'currency' => 'UGX',
        ]);
    }

    public function send(Request $request, MessagePersonalizer $personalizer, UgsmsService $ugsms)
    {
        $apiKey = $this->apiKeyFromRequest($request);

        if (! $apiKey) {
            return response()->json(['message' => 'Invalid API key.'], 401);
        }

        $data = $request->validate([
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
            'api_key' => ['nullable', 'string'],
            'personalized' => ['boolean'],
            'sandbox' => ['boolean'],
        ]);

        $apiKey->update(['last_used_at' => now()]);
        $data = $this->normalizePayload($data);
        $data['sandbox'] = ($apiKey->mode === 'sandbox') || ($data['sandbox'] ?? false);

        return $this->dispatchMessage($apiKey->user, $data, $personalizer, $ugsms);
    }

    private function apiKeyFromRequest(Request $request): ?ApiKey
    {
        $plainKey = (string) ($request->bearerToken() ?: $request->header('X-API-Key') ?: $request->input('api_key'));

        if ($plainKey === '') {
            return null;
        }

        return ApiKey::query()
            ->where('is_active', true)
            ->get()
            ->first(fn (ApiKey $key) => Hash::check($plainKey, $key->key_hash));
    }
}
