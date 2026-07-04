<?php

namespace App\Livewire\App;

use App\Models\SmsCreditTransaction;
use App\Services\IotecPaymentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BuyCredits extends Component
{
    public int $amount = 10000;
    public string $phone = '';

    public function buy(IotecPaymentService $iotec): void
    {
        $data = $this->validate([
            'amount' => ['required', 'integer', 'min:500'],
            'phone' => ['required', 'string', 'max:30'],
        ]);

        $user = Auth::user();
        $unitPrice = max(1, (int) ($user->sms_unit_price ?: 35));
        $result = $iotec->collect($data['amount'], $data['phone']);
        $credits = intdiv($data['amount'], $unitPrice);

        SmsCreditTransaction::query()->create([
            'user_id' => $user->id,
            'type' => 'purchase',
            'amount' => $data['amount'],
            'credits' => $credits,
            'phone' => $data['phone'],
            'provider' => 'iotec',
            'provider_reference' => $result['reference'] ?? null,
            'status' => $result['ok'] ? ($result['status'] ?? 'pending') : 'failed',
            'metadata' => $result,
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
        ])->layout('layouts.app');
    }
}
