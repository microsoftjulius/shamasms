<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SmsCreditTransaction;
use App\Services\SmsCreditTransactionStatusService;
use Illuminate\Http\Request;

class IotecCallbackController extends Controller
{
    public function __invoke(Request $request, SmsCreditTransactionStatusService $statusService)
    {
        $payload = $request->all();
        $externalId = (string) ($request->input('externalId') ?? '');
        $providerReference = (string) ($request->input('id') ?? $request->input('requestId') ?? $request->input('transactionId') ?? '');
        $status = (string) ($request->input('status') ?? $request->input('statusCode') ?? $request->input('requestStatus') ?? 'pending');

        $transaction = SmsCreditTransaction::query()
            ->where('provider', 'iotec')
            ->when($externalId !== '', fn ($query) => $query->where('metadata->external_id', $externalId))
            ->when($externalId === '' && $providerReference !== '', fn ($query) => $query->where('provider_reference', $providerReference))
            ->latest()
            ->first();

        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found.'], 404);
        }

        $statusService->mark($transaction, $status, [
            'callback' => $payload,
            'callback_received_at' => now()->toIso8601String(),
        ], $providerReference);

        return response()->json(['message' => 'Callback accepted.']);
    }
}
