<section class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="page-title">Reports</h1>
            <p class="page-subtitle">Track successful credit sales and estimated profit using the current UGSMS cost of UGX {{ number_format($providerCost) }} per SMS.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button wire:click="previousYear" type="button" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-black text-slate-700 hover:bg-sky-50">Previous</button>
            <div class="rounded-lg border border-sky-100 bg-sky-50 px-4 py-2 text-sm font-black text-sky-900">{{ $year }}</div>
            <button wire:click="nextYear" type="button" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-black text-slate-700 hover:bg-sky-50">Next</button>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        @foreach([['Today', $today], ['This month', $thisMonth], ['This year', $thisYear]] as [$label, $stats])
            <div class="panel min-w-0">
                <p class="text-xs font-black uppercase tracking-widest text-slate-500">{{ $label }}</p>
                <p class="mt-3 text-3xl font-black text-slate-950">UGX {{ number_format($stats['sales']) }}</p>
                <div class="mt-4 grid grid-cols-3 gap-2 text-xs font-bold text-slate-600">
                    <div>
                        <span class="block text-slate-400">Profit</span>
                        <strong class="{{ $stats['profit'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">UGX {{ number_format($stats['profit']) }}</strong>
                    </div>
                    <div>
                        <span class="block text-slate-400">Credits</span>
                        <strong class="text-sky-700">{{ number_format($stats['credits']) }}</strong>
                    </div>
                    <div>
                        <span class="block text-slate-400">Sales</span>
                        <strong class="text-slate-800">{{ number_format($stats['transactions']) }}</strong>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="panel min-w-0">
            <h2 class="text-xl font-black">Monthly sales</h2>
            <p class="page-subtitle">Months with the highest successful credit sales in {{ $year }}.</p>
            <div class="mt-5 space-y-3">
                @foreach($monthlyRows as $row)
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3 text-sm">
                            <span class="font-black text-slate-700">{{ $row['label'] }}</span>
                            <span class="font-bold text-slate-500">UGX {{ number_format($row['sales']) }}</span>
                        </div>
                        <div class="h-3 overflow-hidden rounded-lg bg-slate-100">
                            <div class="h-full rounded-lg bg-sky-500" style="width: {{ min(100, round(($row['sales'] / $maxMonthlySales) * 100, 2)) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="panel min-w-0">
            <h2 class="text-xl font-black">Monthly profit</h2>
            <p class="page-subtitle">Estimated profit is sales minus credits sold at the current UGSMS cost.</p>
            <div class="mt-5 space-y-3">
                @foreach($monthlyRows as $row)
                    @php($positiveProfit = max(0, $row['profit']))
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3 text-sm">
                            <span class="font-black text-slate-700">{{ $row['label'] }}</span>
                            <span class="font-bold {{ $row['profit'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">UGX {{ number_format($row['profit']) }}</span>
                        </div>
                        <div class="h-3 overflow-hidden rounded-lg bg-slate-100">
                            <div class="h-full rounded-lg bg-emerald-500" style="width: {{ min(100, round(($positiveProfit / $maxMonthlyProfit) * 100, 2)) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="panel min-w-0">
            <h2 class="text-xl font-black">Monthly report</h2>
            <div class="mt-5 overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Month</th><th>Sales</th><th>Cost</th><th>Profit</th><th>Credits</th><th>Count</th></tr></thead>
                    <tbody>
                        @foreach($monthlyRows as $row)
                            <tr>
                                <td class="font-black">{{ $row['label'] }}</td>
                                <td>UGX {{ number_format($row['sales']) }}</td>
                                <td>UGX {{ number_format($row['cost']) }}</td>
                                <td class="{{ $row['profit'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">UGX {{ number_format($row['profit']) }}</td>
                                <td>{{ number_format($row['credits']) }}</td>
                                <td>{{ number_format($row['transactions']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel min-w-0">
            <h2 class="text-xl font-black">Yearly report</h2>
            <div class="mt-5 overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Year</th><th>Sales</th><th>Cost</th><th>Profit</th><th>Credits</th><th>Count</th></tr></thead>
                    <tbody>
                        @foreach($yearlyRows as $row)
                            <tr>
                                <td class="font-black">{{ $row['label'] }}</td>
                                <td>UGX {{ number_format($row['sales']) }}</td>
                                <td>UGX {{ number_format($row['cost']) }}</td>
                                <td class="{{ $row['profit'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">UGX {{ number_format($row['profit']) }}</td>
                                <td>{{ number_format($row['credits']) }}</td>
                                <td>{{ number_format($row['transactions']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="panel min-w-0">
        <h2 class="text-xl font-black">Daily report</h2>
        <p class="page-subtitle">Last 31 days of successful credit sales.</p>
        <div class="mt-5 overflow-x-auto">
            <table class="table">
                <thead><tr><th>Day</th><th>Sales</th><th>Cost</th><th>Profit</th><th>Credits</th><th>Count</th></tr></thead>
                <tbody>
                    @foreach($dailyRows as $row)
                        <tr>
                            <td class="font-black">{{ $row['label'] }}</td>
                            <td>UGX {{ number_format($row['sales']) }}</td>
                            <td>UGX {{ number_format($row['cost']) }}</td>
                            <td class="{{ $row['profit'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">UGX {{ number_format($row['profit']) }}</td>
                            <td>{{ number_format($row['credits']) }}</td>
                            <td>{{ number_format($row['transactions']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
