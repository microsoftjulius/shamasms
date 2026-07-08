<?php

namespace App\Livewire\App;

use App\Models\PriceTier;
use App\Models\SmsCreditTransaction;
use App\Services\IotecPaymentService;
use App\Services\UgandaPhoneNumber;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BuyCredits extends Component
{
    public int $amount = 10000;
    public string $phone = '';

    public function buy(IotecPaymentService $iotec, UgandaPhoneNumber $phoneNumber): void
    {
        $data = $this->validate([
            'amount' => ['required', 'integer', 'min:500'],
            'phone' => ['required', 'string', 'max:30'],
        ]);

        $normalizedPhone = $phoneNumber->normalize($data['phone']);

        if (! $normalizedPhone) {
            $this->addError('phone', 'Enter a valid Ugandan mobile money number, for example 0700000000.');
            return;
        }

        $user = Auth::user();
        $tier = PriceTier::query()
            ->where('is_active', true)
            ->where('min_amount', '<=', $data['amount'])
            ->orderByDesc('min_amount')
            ->first();
        $unitPrice = max(1, (int) ($tier?->sms_unit_price ?: $user->sms_unit_price ?: 35));
        $result = $iotec->collect($data['amount'], $normalizedPhone['phone']);
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
                'unit_price' => $unitPrice,
                'price_tier_id' => $tier?->id,
                'price_tier_name' => $tier?->name,
            ],
        ]);

        if (($result['sandbox'] ?? false) === true) {
            $user->increment('sms_balance', $credits);
        }

        session()->flash('status', 'Payment request sent. Sandbox payments credit instantly.');
    }

    public function render()
    {
        return view('livewire.app.buy-credits', [
            'transactions' => SmsCreditTransaction::query()->where('user_id', Auth::id())->latest()->limit(8)->get(),
            'unitPrice' => max(1, (int) (Auth::user()->sms_unit_price ?: 35)),
            'tiers' => PriceTier::query()->where('is_active', true)->orderBy('min_amount')->get(),
        ])->layout('layouts.app');
    }
}
