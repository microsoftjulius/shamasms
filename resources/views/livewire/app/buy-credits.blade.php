<section class="grid gap-6 lg:grid-cols-[420px_1fr]">
    <form wire:submit="buy" class="panel" data-tour="buy-form">
        <h1 class="page-title">Buy</h1>
        <p class="page-subtitle">Enter amount and phone number to call the Iotec payment API.</p>
        <div data-tour="buy-rate" class="mt-5 rounded-lg border border-sky-100 bg-sky-50 px-4 py-3 text-sm font-bold text-sky-900">
            Your rate is UGX {{ number_format($unitPrice) }} per SMS credit.
        </div>
        <div class="mt-6 grid gap-4">
            <label class="label">Amount UGX <span class="req">*</span><input wire:model="amount" type="number" min="5000" class="field" placeholder="5000"></label>
            <label class="label">Mobile money number <span class="req">*</span><input wire:model="phone" class="field" placeholder="0700000000"></label>
        </div>
        @if($tiers->isNotEmpty())
            <div class="mt-5 rounded-lg border border-slate-200 bg-slate-50 p-4">
                <h2 class="text-sm font-black text-slate-800">Price tiers</h2>
                <div class="mt-3 grid gap-2 text-sm text-slate-700">
                    @foreach($tiers as $tier)
                        <div class="flex items-center justify-between gap-3 rounded-lg bg-white px-3 py-2">
                            <span class="font-bold">
                                {{ $tier->name }}
                                {{ number_format($tier->min_messages ?: $tier->min_amount) }}@if($tier->max_messages)-{{ number_format($tier->max_messages) }}@else+@endif SMS
                            </span>
                            <span class="font-black text-sky-800">UGX {{ number_format($tier->sms_unit_price) }}/SMS</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        @foreach($errors->all() as $error)
            <p class="mt-2 text-sm font-semibold text-red-600">{{ $error }}</p>
        @endforeach
        <button data-tour="buy-button" class="mt-6 rounded-lg bg-sky-500 px-6 py-3 font-black text-white hover:bg-sky-600">Buy credits</button>
    </form>
    <div class="panel" data-tour="buy-history">
        <h2 class="text-xl font-black">Recent purchases</h2>
        <div class="mt-5 overflow-x-auto">
            <table class="table">
                <thead><tr><th>Amount</th><th>Credits</th><th>Phone</th><th>Status</th><th>Reference</th><th>Message</th></tr></thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ number_format($transaction->amount) }}</td>
                            <td>{{ number_format($transaction->credits) }}</td>
                            <td>{{ $transaction->phone ?: '—' }}</td>
                            <td><span class="status-pill">{{ $transaction->status }}</span></td>
                            <td>{{ $transaction->provider_reference ?: '—' }}</td>
                            <td class="max-w-xs text-xs leading-5">{{ data_get($transaction->metadata, 'message') ?: data_get($transaction->metadata, 'payload.message') ?: data_get($transaction->metadata, 'payload.error') ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No purchases yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5">
            {{ $transactions->links() }}
        </div>
    </div>
</section>
