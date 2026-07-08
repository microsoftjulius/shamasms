<?php

namespace App\Livewire\Admin;

use App\Models\SmsCreditTransaction;
use App\Services\SmsCreditTransactionStatusService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Payments extends Component
{
    use WithPagination;

    public ?string $paymentMessage = null;

    public function mount(): void
    {
        abort_unless(Auth::user()?->is_admin, 403);
    }

    public function markPaymentSuccessful(int $transactionId, SmsCreditTransactionStatusService $statusService): void
    {
        $this->markPayment($transactionId, 'success', $statusService);
    }

    public function markPaymentFailed(int $transactionId, SmsCreditTransactionStatusService $statusService): void
    {
        $this->markPayment($transactionId, 'failed', $statusService);
    }

    public function render()
    {
        abort_unless(Auth::user()?->is_admin, 403);

        return view('livewire.admin.payments', [
            'payments' => SmsCreditTransaction::query()
                ->with('user:id,name,username,email,sms_balance')
                ->latest()
                ->paginate(10),
        ])->layout('layouts.app');
    }

    private function markPayment(int $transactionId, string $status, SmsCreditTransactionStatusService $statusService): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $transaction = SmsCreditTransaction::query()->with('user')->findOrFail($transactionId);

        $statusService->mark($transaction, $status, [
            'admin_override' => [
                'status' => $status,
                'admin_id' => Auth::id(),
                'admin_name' => Auth::user()?->name,
                'updated_at' => now()->toIso8601String(),
            ],
        ]);

        $this->paymentMessage = "Payment for {$transaction->user?->name} has been marked {$status}.";
    }
}
