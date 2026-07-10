<?php

namespace App\Livewire\Admin;

use App\Models\Advert;
use App\Models\PriceTier;
use App\Models\SmsMessage;
use App\Models\SmsCreditTransaction;
use App\Models\User;
use App\Services\SmsCreditTransactionStatusService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    public string $period = 'day';
    public string $search = '';
    public ?string $priceUpdateMessage = null;
    public ?string $adminMessage = null;
    public ?string $tierMessage = null;
    public ?string $advertMessage = null;
    public ?string $paymentMessage = null;
    public ?string $userActionMessage = null;
    public array $prices = [];
    public array $creditInputs = [];
    public array $tierInputs = [];
    public string $adminUsername = '';
    public string $tierName = '';
    public int $tierMinMessages = 1;
    public ?int $tierMaxMessages = 1000;
    public int $tierUnitPrice = 35;
    public string $advertTitle = '';
    public string $advertBody = '';
    public bool $advertActive = false;

    public function updatedSearch(): void
    {
        $this->resetPage('usersPage');
    }

    public function updatedPeriod(): void
    {
        $this->resetPage('topSendersPage');
    }

    public function mount(): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $this->prices = User::query()
            ->pluck('sms_unit_price', 'id')
            ->map(fn ($price) => (int) ($price ?: 35))
            ->all();

        $advert = Advert::query()->latest()->first();
        if ($advert) {
            $this->advertTitle = $advert->title;
            $this->advertBody = $advert->body;
            $this->advertActive = $advert->is_active;
        }
    }

    public function updatePrice(int $userId): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $this->validate([
            "prices.$userId" => ['required', 'integer', 'min:1', 'max:10000'],
        ], [
            "prices.$userId.required" => 'Enter a price for this user.',
            "prices.$userId.integer" => 'The price must be a whole number.',
            "prices.$userId.min" => 'The price must be at least UGX 1.',
            "prices.$userId.max" => 'The price is too high.',
        ]);

        $user = User::query()->findOrFail($userId);
        $price = (int) $this->prices[$userId];

        $user->update(['sms_unit_price' => $price]);

        $this->priceUpdateMessage = "{$user->name}'s SMS price has been updated to UGX ".number_format($price)." per SMS.";
        session()->flash('status', 'User pricing updated.');
    }

    public function promoteAdmin(): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $this->validate([
            'adminUsername' => ['required', 'string', 'exists:users,username'],
        ], [
            'adminUsername.exists' => 'No user was found with that username.',
        ]);

        $user = User::query()->where('username', $this->adminUsername)->firstOrFail();
        $user->update(['is_admin' => true]);

        $this->adminUsername = '';
        $this->adminMessage = "{$user->name} can now access the admin section.";
    }

    public function verifyUser(int $userId): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $user = User::query()->findOrFail($userId);

        if (! $user->hasVerifiedEmail()) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        $this->userActionMessage = "{$user->name}'s account is now verified.";
    }

    public function creditUser(int $userId): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $this->validate([
            "creditInputs.$userId" => ['required', 'integer', 'min:1', 'max:10000000'],
        ], [
            "creditInputs.$userId.required" => 'Enter credits to add.',
            "creditInputs.$userId.integer" => 'Credits must be a whole number.',
            "creditInputs.$userId.min" => 'Credits must be at least 1.',
            "creditInputs.$userId.max" => 'Credits are too high.',
        ]);

        $credits = (int) $this->creditInputs[$userId];
        $user = User::query()->findOrFail($userId);
        $user->increment('sms_balance', $credits);

        SmsCreditTransaction::query()->create([
            'user_id' => $user->id,
            'type' => 'admin_credit',
            'amount' => 0,
            'credits' => $credits,
            'provider' => 'manual',
            'provider_reference' => 'admin-'.now()->format('YmdHis').'-'.$user->id,
            'status' => 'success',
            'metadata' => [
                'admin_id' => Auth::id(),
                'admin_name' => Auth::user()?->name,
                'credited_at' => now()->toIso8601String(),
            ],
        ]);

        $this->creditInputs[$userId] = null;
        $this->userActionMessage = "{$user->name} has been credited with ".number_format($credits)." SMS credits.";
    }

    public function createTier(): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $data = $this->validate([
            'tierName' => ['required', 'string', 'max:80'],
            'tierMinMessages' => ['required', 'integer', 'min:1'],
            'tierMaxMessages' => ['nullable', 'integer', 'min:1'],
            'tierUnitPrice' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        if ($data['tierMaxMessages'] !== null && $data['tierMaxMessages'] < $data['tierMinMessages']) {
            $this->addError('tierMaxMessages', 'The end of the range must be greater than or equal to the start.');
            return;
        }

        $tier = PriceTier::query()->create([
            'name' => $data['tierName'],
            'min_amount' => $data['tierMinMessages'],
            'min_messages' => $data['tierMinMessages'],
            'max_messages' => $data['tierMaxMessages'],
            'sms_unit_price' => $data['tierUnitPrice'],
            'is_active' => true,
        ]);

        $this->tierName = '';
        $this->tierMinMessages = 1;
        $this->tierMaxMessages = 1000;
        $this->tierUnitPrice = 35;
        $this->tierInputs[$tier->id] = [
            'name' => $tier->name,
            'min_messages' => $tier->min_messages,
            'max_messages' => $tier->max_messages,
            'sms_unit_price' => $tier->sms_unit_price,
            'is_active' => $tier->is_active,
        ];
        $this->tierMessage = 'Price tier created.';
    }

    public function updateTier(int $tierId): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $this->validate([
            "tierInputs.$tierId.name" => ['required', 'string', 'max:80'],
            "tierInputs.$tierId.min_messages" => ['required', 'integer', 'min:1'],
            "tierInputs.$tierId.max_messages" => ['nullable', 'integer', 'min:1'],
            "tierInputs.$tierId.sms_unit_price" => ['required', 'integer', 'min:1', 'max:10000'],
            "tierInputs.$tierId.is_active" => ['boolean'],
        ]);

        $tier = PriceTier::query()->findOrFail($tierId);
        $input = $this->tierInputs[$tierId];
        $minMessages = (int) $input['min_messages'];
        $maxMessages = $input['max_messages'] === '' || $input['max_messages'] === null
            ? null
            : (int) $input['max_messages'];

        if ($maxMessages !== null && $maxMessages < $minMessages) {
            $this->addError("tierInputs.$tierId.max_messages", 'The end of the range must be greater than or equal to the start.');
            return;
        }

        $tier->update([
            'name' => $input['name'],
            'min_amount' => $minMessages,
            'min_messages' => $minMessages,
            'max_messages' => $maxMessages,
            'sms_unit_price' => (int) $input['sms_unit_price'],
            'is_active' => (bool) ($input['is_active'] ?? false),
        ]);

        $this->tierMessage = 'Price tier updated.';
    }

    public function saveAdvert(): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $data = $this->validate([
            'advertTitle' => ['required', 'string', 'max:120'],
            'advertBody' => ['required', 'string', 'max:1000'],
            'advertActive' => ['boolean'],
        ]);

        Advert::query()->update(['is_active' => false]);

        Advert::query()->create([
            'title' => $data['advertTitle'],
            'body' => $data['advertBody'],
            'is_active' => $data['advertActive'],
        ]);

        $this->advertMessage = $data['advertActive'] ? 'Advert is now active for logged-in users.' : 'Advert saved as inactive.';
    }

    public function activateAdvert(int $advertId): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $advert = Advert::query()->findOrFail($advertId);

        Advert::query()->update(['is_active' => false]);
        $advert->update(['is_active' => true]);

        $this->advertTitle = $advert->title;
        $this->advertBody = $advert->body;
        $this->advertActive = true;
        $this->advertMessage = "\"{$advert->title}\" is now active for logged-in users.";
    }

    public function stopAdvert(int $advertId): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $advert = Advert::query()->findOrFail($advertId);
        $advert->update(['is_active' => false]);

        $this->advertActive = false;
        $this->advertMessage = "\"{$advert->title}\" has been stopped.";
    }

    public function deleteAdvert(int $advertId): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $advert = Advert::query()->findOrFail($advertId);
        $title = $advert->title;
        $wasActive = $advert->is_active;

        $advert->delete();

        if ($wasActive) {
            $this->advertActive = false;
        }

        $this->advertMessage = "\"{$title}\" has been deleted.";
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

        $users = User::query()
            ->withCount(['smsMessages as sent_batches_count' => fn ($query) => $query->where('status', 'sent')])
            ->withSum(['smsMessages as sent_recipients_total' => fn ($query) => $query->where('status', 'sent')], 'recipient_count')
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query
                        ->where('name', 'like', "%{$this->search}%")
                        ->orWhere('username', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10, pageName: 'usersPage');

        foreach ($users as $user) {
            $this->prices[$user->id] ??= (int) ($user->sms_unit_price ?: 35);
            $this->creditInputs[$user->id] ??= null;
        }

        $tiers = PriceTier::query()->orderBy('min_messages')->get();
        foreach ($tiers as $tier) {
            $this->tierInputs[$tier->id] ??= [
                'name' => $tier->name,
                'min_messages' => $tier->min_messages ?: $tier->min_amount,
                'max_messages' => $tier->max_messages,
                'sms_unit_price' => $tier->sms_unit_price,
                'is_active' => $tier->is_active,
            ];
        }

        [$startsAt, $endsAt] = $this->periodRange();

        return view('livewire.admin.dashboard', [
            'users' => $users,
            'topSenders' => $this->topSenders($startsAt, $endsAt),
            'startsAt' => $startsAt,
            'endsAt' => $endsAt,
            'totalUsers' => User::query()->count(),
            'totalBalance' => User::query()->sum('sms_balance'),
            'adminCount' => User::query()->where('is_admin', true)->count(),
            'tiers' => $tiers,
            'adverts' => Advert::query()->latest()->limit(10)->get(),
            'payments' => SmsCreditTransaction::query()
                ->with('user:id,name,username,email,sms_balance')
                ->latest()
                ->paginate(10, pageName: 'paymentsPage'),
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

    private function periodRange(): array
    {
        $now = now();

        return match ($this->period) {
            'week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
        };
    }

    private function topSenders(Carbon $startsAt, Carbon $endsAt)
    {
        return SmsMessage::query()
            ->select([
                'user_id',
                DB::raw('COUNT(*) as batches_count'),
                DB::raw('COALESCE(SUM(recipient_count), 0) as recipients_count'),
                DB::raw('COALESCE(SUM(recipient_count * segments), 0) as credits_count'),
            ])
            ->with('user:id,name,username,email,sms_unit_price')
            ->where('status', 'sent')
            ->whereBetween('created_at', [$startsAt, $endsAt])
            ->groupBy('user_id')
            ->orderByDesc('recipients_count')
            ->paginate(10, pageName: 'topSendersPage');
    }
}
