<section class="grid gap-6 lg:grid-cols-[420px_1fr]">
    <form wire:submit="transfer" class="panel" data-tour="me2u-form">
        <h1 class="page-title">Me 2 U</h1>
        <p class="page-subtitle">Share SMS credits with another ShamaSMS user by username.</p>
        <div class="mt-6 grid gap-4">
            <label class="label">Username <span class="req">*</span><input wire:model="username" class="field"></label>
            <label class="label">Credits <span class="req">*</span><input wire:model="credits" type="number" min="1" class="field"></label>
            <label class="label">Note<input wire:model="note" class="field"></label>
        </div>
        @foreach($errors->all() as $error)
            <p class="mt-2 text-sm font-semibold text-red-600">{{ $error }}</p>
        @endforeach
        <button data-tour="me2u-button" class="mt-6 rounded-lg bg-sky-500 px-6 py-3 font-black text-white hover:bg-sky-600">Share credits</button>
    </form>
    <div class="panel" data-tour="me2u-history">
        <h2 class="text-xl font-black">Transfer history</h2>
        <div class="mt-5 overflow-x-auto">
            <table class="table">
                <thead><tr><th>From</th><th>To</th><th>Credits</th><th>Date</th></tr></thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        <tr><td>{{ $transfer->sender->username }}</td><td>{{ $transfer->recipient->username }}</td><td>{{ $transfer->credits }}</td><td>{{ $transfer->created_at->format('d M Y H:i') }}</td></tr>
                    @empty
                        <tr><td colspan="4">No transfers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
