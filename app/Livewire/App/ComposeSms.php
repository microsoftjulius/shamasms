<?php

namespace App\Livewire\App;

use App\Models\ContactGroup;
use App\Models\SmsMessage;
use App\Services\MessagePersonalizer;
use App\Services\UgandaPhoneNumber;
use App\Services\UgsmsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class ComposeSms extends Component
{
    use WithFileUploads;

    public string $sender_id = '';
    public string $recipient_mode = 'typed';
    public string $typed_recipients = '';
    public ?int $contact_group_id = null;
    public $recipient_file = null;
    public string $body = '';
    public bool $personalized = false;
    public string $send_when = 'now';
    public int $repeat_count = 1;
    public string $repeat_unit = 'days';
    public array $schedule_weekdays = [];
    public string $schedule_time = '09:00';
    public array $invalid_recipients = [];

    public array $weekdays = [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
    ];

    public function send(MessagePersonalizer $personalizer, UgsmsService $ugsms): void
    {
        $this->validate([
            'sender_id' => ['nullable', 'string', 'max:32'],
            'recipient_mode' => ['required', 'in:typed,upload,group'],
            'typed_recipients' => ['nullable', 'string'],
            'contact_group_id' => ['nullable', 'exists:contact_groups,id'],
            'recipient_file' => ['nullable', 'file', 'max:4096'],
            'body' => ['required', 'string', 'max:765'],
            'send_when' => ['required', 'in:now,later'],
            'repeat_count' => ['required', 'integer', 'min:1', 'max:52'],
            'repeat_unit' => ['required', 'in:days,weeks'],
            'schedule_weekdays' => ['array'],
            'schedule_weekdays.*' => ['in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'schedule_time' => ['required', 'date_format:H:i'],
        ]);

        if ($this->send_when === 'later' && $this->repeat_unit === 'weeks' && $this->schedule_weekdays === []) {
            $this->addError('schedule_weekdays', 'Choose at least one day of the week.');
            return;
        }

        $recipients = $this->recipients(app(UgandaPhoneNumber::class));
        if ($recipients === []) {
            $this->addError('typed_recipients', 'Add at least one valid Ugandan mobile number.');
            return;
        }

        $segments = $personalizer->segments($this->body);
        $cost = $segments * count($recipients) * $this->repeat_count;
        $user = Auth::user();

        if ($this->send_when === 'now' && $user->sms_balance < $cost) {
            $this->addError('body', 'Your balance is too low for this send.');
            return;
        }

        $message = SmsMessage::query()->create([
            'user_id' => $user->id,
            'sender_id' => $this->sender_id ?: null,
            'body' => $this->body,
            'mode' => $this->personalized ? 'personalized' : 'standard',
            'status' => $this->send_when === 'now' ? 'sending' : 'scheduled',
            'segments' => $segments,
            'recipient_count' => count($recipients),
            'scheduled_at' => $this->send_when === 'later' ? now()->setTimeFromTimeString($this->schedule_time) : null,
            'schedule_repeat_count' => $this->send_when === 'later' ? $this->repeat_count : null,
            'schedule_repeat_unit' => $this->send_when === 'later' ? $this->repeat_unit : null,
            'schedule_weekdays' => $this->send_when === 'later' && $this->repeat_unit === 'weeks'
                ? array_values($this->schedule_weekdays)
                : null,
            'schedule_time' => $this->send_when === 'later' ? $this->schedule_time : null,
        ]);

        $sentCount = 0;
        $failedCount = 0;

        foreach ($recipients as $recipient) {
            $rendered = $this->personalized ? $personalizer->render($this->body, $recipient) : $this->body;
            $row = $message->recipients()->create([...$recipient, 'rendered_body' => $rendered]);

            if ($this->send_when === 'now') {
                $result = $ugsms->send($this->sender_id ?: null, $recipient['phone'], $rendered);
                $sent = (bool) ($result['ok'] ?? false);
                $sent ? $sentCount++ : $failedCount++;
                $row->update([
                    'status' => $sent ? 'sent' : 'failed',
                    'provider_reference' => $result['reference'] ?? null,
                ]);
            }
        }

        if ($this->send_when === 'now') {
            $charged = $segments * $sentCount;

            if ($charged > 0) {
                $user->decrement('sms_balance', $charged);
            }

            $message->update(['status' => $sentCount === 0 ? 'failed' : ($failedCount > 0 ? 'partial' : 'sent')]);
        }

        $this->reset(['typed_recipients', 'recipient_file', 'body']);
        session()->flash('status', $this->send_when === 'now'
            ? ($failedCount > 0 ? "{$sentCount} sent, {$failedCount} failed." : 'Message sent successfully.')
            : 'Message scheduled.');
    }

    public function render()
    {
        return view('livewire.app.compose-sms', [
            'groups' => Auth::user()->contactGroups()->withCount('contacts')->orderBy('name')->get(),
            'balance' => Auth::user()->sms_balance,
            'characters' => mb_strlen($this->body),
            'segments' => app(MessagePersonalizer::class)->segments($this->body),
        ])->layout('layouts.app');
    }

    private function recipients(UgandaPhoneNumber $phoneNumber): array
    {
        $this->invalid_recipients = [];

        if ($this->recipient_mode === 'group' && $this->contact_group_id) {
            return ContactGroup::query()
                ->whereBelongsTo(Auth::user())
                ->findOrFail($this->contact_group_id)
                ->contacts()
                ->get(['phone', 'name', 'var1', 'var2', 'var3', 'var4', 'var5'])
                ->map(fn ($contact) => [
                    'phone' => $contact->phone,
                    'name' => null,
                    'var1' => null,
                    'var2' => null,
                    'var3' => null,
                    'var4' => null,
                    'var5' => null,
                ])
                ->all();
        }

        $text = $this->typed_recipients;
        if ($this->recipient_mode === 'upload' && $this->recipient_file) {
            $text = $this->recipient_file->get();
        }

        return collect(preg_split('/[\s,;]+/', (string) $text, flags: PREG_SPLIT_NO_EMPTY))
            ->map(function (string $raw) use ($phoneNumber) {
                $normalized = $phoneNumber->normalize($raw);

                if (! $normalized) {
                    $this->invalid_recipients[] = $raw;
                    return null;
                }

                return [
                    'phone' => $normalized['phone'],
                    'name' => null,
                    'var1' => null,
                    'var2' => null,
                    'var3' => null,
                    'var4' => null,
                    'var5' => null,
                ];
            })
            ->filter()
            ->unique('phone')
            ->values()
            ->all();
    }
}
