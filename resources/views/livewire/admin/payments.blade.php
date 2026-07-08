<section class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="page-title">Payments</h1>
            <p class="page-subtitle">Review credit purchases and manually resolve payments that did not callback.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-black text-slate-700 hover:bg-sky-50 hover:text-sky-800" href="{{ route('admin.dashboard') }}">
                Admin dashboard
            </a>
            <a class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-black text-slate-700 hover:bg-sky-50 hover:text-sky-800" href="{{ route('admin.integrations') }}">
                Integrations
            </a>
        </div>
    </div>

    @if($paymentMessage)
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-black text-emerald-900">
            {{ $paymentMessage }}
        </div>
    @endif

    <div class="panel min-w-0">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Credits</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Reference</th>
                        <th>Message</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td class="whitespace-nowrap">{{ $payment->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <div class="font-black text-slate-900">{{ $payment->user?->name ?? 'Unknown user' }}</div>
                                <div class="text-xs text-slate-500">{{ $payment->user?->username ?? '—' }}</div>
                            </td>
                            <td>{{ number_format($payment->amount) }}</td>
                            <td>{{ number_format($payment->credits) }}</td>
                            <td class="whitespace-nowrap">{{ $payment->phone ?: '—' }}</td>
                            <td><span class="status-pill">{{ $payment->status }}</span></td>
                            <td class="max-w-52 break-words text-xs">{{ $payment->provider_reference ?: data_get($payment->metadata, 'external_id', '—') }}</td>
                            <td class="max-w-xs text-xs leading-5">{{ data_get($payment->metadata, 'message') ?: data_get($payment->metadata, 'callback.statusMessage') ?: data_get($payment->metadata, 'payload.message') ?: data_get($payment->metadata, 'payload.error') ?: '—' }}</td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    <button wire:click="markPaymentSuccessful({{ $payment->id }})" data-swal-confirm="Mark this payment successful and add credits if needed?" data-swal-title="Approve payment?" data-swal-icon="success" data-swal-confirm-text="Mark successful" data-swal-confirm-color="#10b981" type="button" class="rounded-lg bg-emerald-500 px-3 py-2 text-xs font-black text-white hover:bg-emerald-600">Success</button>
                                    <button wire:click="markPaymentFailed({{ $payment->id }})" data-swal-confirm="Mark this payment failed and reverse credits if it was already credited?" data-swal-title="Fail payment?" data-swal-icon="warning" data-swal-confirm-text="Mark failed" data-swal-confirm-color="#dc2626" type="button" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-black text-red-700 hover:bg-red-50">Failed</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9">No payments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5">
            {{ $payments->links() }}
        </div>
    </div>
</section>
