<?php

namespace App\Services;

use App\Models\SmsCreditTransaction;
use Illuminate\Support\Facades\DB;

class SmsCreditTransactionStatusService
{
    public function mark(
        SmsCreditTransaction $transaction,
        string $status,
        array $metadata = [],
        ?string $providerReference = null,
    ): SmsCreditTransaction {
        return DB::transaction(function () use ($transaction, $status, $metadata, $providerReference): SmsCreditTransaction {
            $transaction->refresh();

            $currentMetadata = $transaction->metadata ?? [];
            $normalizedStatus = $this->normalize($status);

            foreach ($metadata as $key => $value) {
                $currentMetadata[$key] = $value;
            }

            if ($normalizedStatus === 'success' && blank(data_get($currentMetadata, 'credited_at'))) {
                $transaction->user?->increment('sms_balance', $transaction->credits);
                $currentMetadata['credited_at'] = now()->toIso8601String();
            }

            if ($normalizedStatus === 'failed' && filled(data_get($currentMetadata, 'credited_at'))) {
                $user = $transaction->user;

                if ($user) {
                    $user->update([
                        'sms_balance' => max(0, (int) $user->fresh()->sms_balance - (int) $transaction->credits),
                    ]);
                }

                $currentMetadata['credit_reversed_at'] = now()->toIso8601String();
                unset($currentMetadata['credited_at']);
            }

            $transaction->update([
                'status' => $normalizedStatus,
                'provider_reference' => $providerReference ?: $transaction->provider_reference,
                'metadata' => $currentMetadata,
            ]);

            return $transaction;
        });
    }

    public function normalize(string $status): string
    {
        return match (strtolower($status)) {
            'success', 'successful', 'succeeded' => 'success',
            'failed', 'fail', 'declined', 'cancelled', 'canceled', 'rejected', 'rolledback' => 'failed',
            'senttovendor', 'sent_to_vendor' => 'sent_to_vendor',
            'awaitingapproval' => 'awaiting_approval',
            default => strtolower($status) ?: 'pending',
        };
    }
}
