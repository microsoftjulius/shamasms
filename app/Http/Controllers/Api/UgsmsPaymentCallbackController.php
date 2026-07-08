<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SmsCreditTransaction;
use Illuminate\Http\Request;

class UgsmsPaymentCallbackController extends Controller
{
    public function __invoke(Request $request)
    {
        $payload = $request->all();
        $reference = (string) (
            $request->input('reference')
            ?? $request->input('payment_reference')
            ?? $request->input('payment_id')
            ?? $request->input('id')
            ?? $request->input('data.reference')
            ?? $request->input('data.payment_reference')
            ?? $request->input('data.payment_id')
            ?? $request->input('data.id')
            ?? ''
        );
        $status = (string) (
            $request->input('status')
            ?? $request->input('data.status')
            ?? 'pending'
        );
        $amount = (int) (
            $request->input('amount')
            ?? $request->input('data.amount')
            ?? 0
        );

        $transaction = SmsCreditTransaction::query()
            ->where('provider', 'ugsms')
            ->where('type', 'ugsms_deposit')
            ->when($reference !== '', fn ($query) => $query->where('provider_reference', $reference))
            ->when($reference === '' && $amount > 0, fn ($query) => $query->where('amount', $amount)->whereIn('status', ['pending', 'processing']))
            ->latest()
            ->first();

        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found.'], 404);
        }

        $metadata = $transaction->metadata ?? [];
        $metadata['callback'] = $payload;
        $metadata['callback_received_at'] = now()->toIso8601String();

        $transaction->update([
            'status' => $this->normalizeStatus($status),
            'metadata' => $metadata,
        ]);

        return response()->json(['message' => 'Callback accepted.']);
    }

    private function normalizeStatus(string $status): string
    {
        return match (strtolower($status)) {
            'success', 'successful', 'succeeded', 'paid', 'completed' => 'success',
            'failed', 'fail', 'declined', 'cancelled', 'canceled', 'rejected' => 'failed',
            default => strtolower($status) ?: 'pending',
        };
    }
}
