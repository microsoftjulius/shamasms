<?php

namespace App\Livewire\Admin;

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
    public array $prices = [];

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

        [$startsAt, $endsAt] = $this->periodRange();

        return view('livewire.admin.dashboard', [
            'users' => $users,
            'topSenders' => $this->topSenders($startsAt, $endsAt),
            'startsAt' => $startsAt,
            'endsAt' => $endsAt,
            'totalUsers' => User::query()->count(),
            'totalBalance' => User::query()->sum('sms_balance'),
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
