<?php

namespace App\Livewire\Admin;

use App\Models\IntegrationSetting;
use App\Models\SmsCreditTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Reports extends Component
{
    public int $year;

    public function mount(): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $this->year = (int) now()->year;
    }

    public function previousYear(): void
    {
        $this->year--;
    }

    public function nextYear(): void
    {
        $this->year++;
    }

    public function render()
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $providerCost = $this->providerCostPerSms();
        $monthlyRows = $this->monthlyRows($providerCost);
        $dailyRows = $this->dailyRows($providerCost);
        $yearlyRows = $this->yearlyRows($providerCost);

        return view('livewire.admin.reports', [
            'providerCost' => $providerCost,
            'today' => $this->statsFor(now()->copy()->startOfDay(), now()->copy()->endOfDay(), $providerCost),
            'thisMonth' => $this->statsFor(now()->copy()->startOfMonth(), now()->copy()->endOfMonth(), $providerCost),
            'thisYear' => $this->statsFor(now()->copy()->startOfYear(), now()->copy()->endOfYear(), $providerCost),
            'monthlyRows' => $monthlyRows,
            'dailyRows' => $dailyRows,
            'yearlyRows' => $yearlyRows,
            'maxMonthlySales' => max(1, $monthlyRows->max('sales') ?? 0),
            'maxMonthlyProfit' => max(1, $monthlyRows->max('profit') ?? 0),
        ])->layout('layouts.app');
    }

    private function monthlyRows(int $providerCost)
    {
        return collect(range(1, 12))->map(function (int $month) use ($providerCost): array {
            $startsAt = Carbon::create($this->year, $month, 1)->startOfMonth();
            $endsAt = $startsAt->copy()->endOfMonth();

            return [
                'label' => $startsAt->format('M Y'),
                ...$this->statsFor($startsAt, $endsAt, $providerCost),
            ];
        });
    }

    private function dailyRows(int $providerCost)
    {
        return collect(range(0, 30))->map(function (int $offset) use ($providerCost): array {
            $date = now()->copy()->subDays($offset);

            return [
                'label' => $date->format('d M Y'),
                ...$this->statsFor($date->copy()->startOfDay(), $date->copy()->endOfDay(), $providerCost),
            ];
        });
    }

    private function yearlyRows(int $providerCost)
    {
        return collect(range($this->year - 4, $this->year))->reverse()->values()->map(function (int $year) use ($providerCost): array {
            $startsAt = Carbon::create($year, 1, 1)->startOfYear();
            $endsAt = $startsAt->copy()->endOfYear();

            return [
                'label' => (string) $year,
                ...$this->statsFor($startsAt, $endsAt, $providerCost),
            ];
        });
    }

    private function statsFor(Carbon $startsAt, Carbon $endsAt, int $providerCost): array
    {
        $transactions = SmsCreditTransaction::query()
            ->whereIn('type', ['purchase', 'admin_credit'])
            ->where('status', 'success')
            ->where('amount', '>', 0)
            ->whereBetween('created_at', [$startsAt, $endsAt])
            ->get(['amount', 'credits']);

        $sales = (int) $transactions->sum('amount');
        $credits = (int) $transactions->sum('credits');
        $cost = $credits * $providerCost;

        return [
            'sales' => $sales,
            'cost' => $cost,
            'profit' => $sales - $cost,
            'credits' => $credits,
            'transactions' => $transactions->count(),
        ];
    }

    private function providerCostPerSms(): int
    {
        $setting = IntegrationSetting::query()
            ->whereIn('provider', ['sms_gateway', 'ugsms'])
            ->where('is_active', true)
            ->latest()
            ->first();

        return max(1, (int) data_get($setting?->metadata, 'unit_price', config('services.ugsms.unit_price', 35)));
    }
}
