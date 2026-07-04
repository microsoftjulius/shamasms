<section class="grid gap-6 lg:grid-cols-[1fr_320px]">
    <form wire:submit="send" class="panel" data-tour="compose-form">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="page-title">Compose SMS</h1>
                <p class="page-subtitle">Send now, or schedule by days, weeks, and time.</p>
            </div>
            <span class="rounded-lg bg-sky-50 px-4 py-2 text-sm font-black text-sky-800">{{ $characters }} chars · {{ $segments }} segment{{ $segments === 1 ? '' : 's' }}</span>
        </div>

        <div class="mt-6 grid gap-4">
            <label class="label" data-tour="sender-id">Sender ID<input wire:model="sender_id" class="field" placeholder="e.g. SHAMA"></label>

            <div data-tour="recipient-mode">
                <p class="mb-2 text-sm font-black text-slate-800">Recipients</p>
                <div class="segmented">
                    <label><input wire:model.live="recipient_mode" type="radio" value="typed"> <span>Type</span></label>
                    <label><input wire:model.live="recipient_mode" type="radio" value="upload"> <span>Upload</span></label>
                    <label><input wire:model.live="recipient_mode" type="radio" value="group"> <span>Phonebook group</span></label>
                </div>
            </div>

            @if($recipient_mode === 'typed')
                <label class="label">Recipient rows <span class="req">*</span><textarea wire:model="typed_recipients" class="field min-h-36" placeholder="0700000000&#10;256750000000&#10;+256770000000"></textarea></label>
            @elseif($recipient_mode === 'upload')
                <label class="label">Upload CSV/XLS-style file <span class="req">*</span><input wire:model="recipient_file" type="file" class="field bg-white"></label>
            @else
                <label class="label">Group <span class="req">*</span><select wire:model="contact_group_id" class="field">
                    <option value="">Choose group</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }} ({{ $group->contacts_count }})</option>
                    @endforeach
                </select></label>
            @endif
            @if($invalid_recipients)
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                    Invalid recipient numbers ignored: {{ implode(', ', array_slice($invalid_recipients, 0, 8)) }}
                    @if(count($invalid_recipients) > 8)
                        , and {{ count($invalid_recipients) - 8 }} more
                    @endif
                </div>
            @endif

            <label data-tour="personalize-toggle" class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                <input wire:model.live="personalized" type="checkbox" class="rounded border-slate-300"> Personalized message
            </label>

            <label class="label" data-tour="message-body">Message <span class="req">*</span>
                <textarea
                    wire:model.live="body"
                    class="field min-h-28"
                    rows="4"
                    @if($personalized)
                        placeholder="Use &#64;&#64;name&#64;&#64;, &#64;&#64;var1&#64;&#64;, &#64;&#64;var2&#64;&#64;, &#64;&#64;var3&#64;&#64;, &#64;&#64;var4&#64;&#64;, &#64;&#64;var5&#64;&#64;"
                    @else
                        placeholder="Type your message"
                    @endif
                ></textarea>
            </label>
            <p class="text-sm font-bold text-slate-600">
                {{ $characters }} character{{ $characters === 1 ? '' : 's' }} · {{ $segments }} message{{ $segments === 1 ? '' : 's' }}
            </p>

            <div data-tour="delivery-mode">
                <p class="mb-2 text-sm font-black text-slate-800">Delivery</p>
                <div class="segmented">
                    <label><input wire:model.live="send_when" type="radio" value="now"> <span>Send now</span></label>
                    <label><input wire:model.live="send_when" type="radio" value="later"> <span>Send later</span></label>
                </div>
            </div>

            @if($send_when === 'later')
                <div class="grid gap-4 md:grid-cols-3">
                    <label class="label">Number <span class="req">*</span><input wire:model="repeat_count" type="number" min="1" class="field"></label>
                    <label class="label">Period <span class="req">*</span><select wire:model.live="repeat_unit" class="field"><option value="days">Days</option><option value="weeks">Weeks</option></select></label>
                    <label class="label">Time per day <span class="req">*</span><input wire:model="schedule_time" type="time" class="field"></label>
                </div>
                @if($repeat_unit === 'weeks')
                    <div>
                        <p class="mb-2 text-sm font-black text-slate-800">Days in the week <span class="req">*</span></p>
                        <div class="weekday-grid">
                            @foreach($weekdays as $value => $label)
                                <label>
                                    <input wire:model="schedule_weekdays" type="checkbox" value="{{ $value }}">
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('schedule_weekdays')
                            <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            @endif
        </div>

        @foreach($errors->all() as $error)
            <p class="mt-2 text-sm font-semibold text-red-600">{{ $error }}</p>
        @endforeach
        <button data-tour="send-button" class="mt-6 w-full rounded-lg bg-sky-500 px-6 py-3 font-black text-white hover:bg-sky-600 sm:w-auto">{{ $send_when === 'now' ? 'Send SMS' : 'Schedule SMS' }}</button>
    </form>

    <aside class="panel h-fit" data-tour="quick-guide">
        <h2 class="text-lg font-black">Quick guide</h2>
        <div class="mt-4 space-y-4 text-sm leading-6 text-slate-700">
            <p>Each SMS segment is 160 characters. Longer messages are counted as multiple segments.</p>
            <p>Recipient rows accept valid Ugandan mobile numbers only, such as 070..., 25670..., or +25670....</p>
            <p>Current balance: <strong class="text-sky-800">{{ number_format($balance) }}</strong> credits.</p>
        </div>
    </aside>
</section>
