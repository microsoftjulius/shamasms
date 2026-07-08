<?php

namespace App\Livewire\Admin;

use App\Models\Advert;
use App\Models\PriceTier;
use App\Models\SmsMessage;
use App\Models\User;
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
    public array $prices = [];
    public array $tierInputs = [];
    public string $adminUsername = '';
    public string $tierName = '';
    public int $tierMinAmount = 10000;
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

    public function createTier(): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $data = $this->validate([
            'tierName' => ['required', 'string', 'max:80'],
            'tierMinAmount' => ['required', 'integer', 'min:500'],
            'tierUnitPrice' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        $tier = PriceTier::query()->create([
            'name' => $data['tierName'],
            'min_amount' => $data['tierMinAmount'],
            'sms_unit_price' => $data['tierUnitPrice'],
            'is_active' => true,
        ]);

        $this->tierName = '';
        $this->tierMinAmount = 10000;
        $this->tierUnitPrice = 35;
        $this->tierInputs[$tier->id] = [
            'name' => $tier->name,
            'min_amount' => $tier->min_amount,
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
            "tierInputs.$tierId.min_amount" => ['required', 'integer', 'min:500'],
            "tierInputs.$tierId.sms_unit_price" => ['required', 'integer', 'min:1', 'max:10000'],
            "tierInputs.$tierId.is_active" => ['boolean'],
        ]);

        $tier = PriceTier::query()->findOrFail($tierId);
        $input = $this->tierInputs[$tierId];

        $tier->update([
            'name' => $input['name'],
            'min_amount' => (int) $input['min_amount'],
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
        }

        $tiers = PriceTier::query()->orderBy('min_amount')->get();
        foreach ($tiers as $tier) {
            $this->tierInputs[$tier->id] ??= [
                'name' => $tier->name,
                'min_amount' => $tier->min_amount,
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
        ])->layout('layouts.app');
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
