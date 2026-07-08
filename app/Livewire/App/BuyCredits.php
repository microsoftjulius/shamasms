<?php

namespace App\Livewire\App;

use App\Models\PriceTier;
use App\Models\SmsCreditTransaction;
use App\Services\IotecPaymentService;
use App\Services\UgandaPhoneNumber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class BuyCredits extends Component
{
    use WithPagination;

    public int $amount = 10000;
    public string $phone = '';

    public function buy(IotecPaymentService $iotec, UgandaPhoneNumber $phoneNumber): void
    {
        $data = $this->validate([
            'amount' => ['required', 'integer', 'min:5000'],
            'phone' => ['required', 'string', 'max:30'],
        ]);

        $normalizedPhone = $phoneNumber->normalize($data['phone']);

        if (! $normalizedPhone) {
            $this->addError('phone', 'Enter a valid Ugandan mobile money number, for example 0700000000.');
            return;
        }

        $user = Auth::user();
        $tier = $this->matchingTier($data['amount']);
        $unitPrice = max(1, (int) ($tier?->sms_unit_price ?: $user->sms_unit_price ?: 35));
        $externalId = 'shamasms-'.Str::uuid();
        $result = $iotec->collect($data['amount'], $normalizedPhone['phone'], $externalId);
        $credits = intdiv($data['amount'], $unitPrice);

        if ($tier && (int) $user->sms_unit_price !== $unitPrice) {
            $user->update(['sms_unit_price' => $unitPrice]);
        }

        SmsCreditTransaction::query()->create([
            'user_id' => $user->id,
            'type' => 'purchase',
            'amount' => $data['amount'],
            'credits' => $credits,
            'phone' => $normalizedPhone['phone'],
            'provider' => 'iotec',
            'provider_reference' => $result['reference'] ?? null,
            'status' => $result['ok'] ? ($result['status'] ?? 'pending') : 'failed',
            'metadata' => [
                ...$result,
                'input_phone' => $data['phone'],
                'external_id' => $externalId,
                'unit_price' => $unitPrice,
                'price_tier_id' => $tier?->id,
                'price_tier_name' => $tier?->name,
            ],
        ]);

        session()->flash('status', ($result['ok'] ?? false)
            ? 'Payment request sent. Your credits will update after payment confirmation.'
            : 'Payment request failed. Check the recent purchases table for the provider message.');
    }

    public function render()
    {
        return view('livewire.app.buy-credits', [
            'transactions' => SmsCreditTransaction::query()->where('user_id', Auth::id())->latest()->paginate(10),
            'unitPrice' => max(1, (int) (Auth::user()->sms_unit_price ?: 35)),
            'tiers' => PriceTier::query()->where('is_active', true)->orderBy('min_messages')->get(),
        ])->layout('layouts.app');
    }

    private function matchingTier(int $amount): ?PriceTier
    {
        $fallback = null;

        foreach (PriceTier::query()->where('is_active', true)->orderByDesc('min_messages')->get() as $tier) {
            $unitPrice = max(1, (int) $tier->sms_unit_price);
            $credits = intdiv($amount, $unitPrice);
            $minMessages = (int) ($tier->min_messages ?: $tier->min_amount ?: 1);
            $maxMessages = $tier->max_messages ? (int) $tier->max_messages : null;

            if ($credits >= $minMessages && ($maxMessages === null || $credits <= $maxMessages)) {
                return $tier;
            }

            if ($credits >= $minMessages && $fallback === null) {
                $fallback = $tier;
            }
        }

        return $fallback;
    }
}
