<?php

namespace App\Livewire\App;

use App\Models\ContactGroup;
use App\Services\UgandaPhoneNumber;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class Phonebook extends Component
{
    use WithFileUploads;

    public string $group_name = '';
    public string $sender_id = '';
    public ?int $active_group_id = null;
    public string $numbers = '';
    public $numbers_file = null;
    public array $invalid_numbers = [];

    public function createGroup(): void
    {
        $data = $this->validate([
            'group_name' => ['required', 'string', 'max:120'],
            'sender_id' => ['nullable', 'string', 'max:32'],
        ]);

        $group = Auth::user()->contactGroups()->create([
            'name' => $data['group_name'],
            'sender_id' => $data['sender_id'],
        ]);

        $this->active_group_id = $group->id;
        $this->reset(['group_name', 'sender_id']);
    }

    public function importContacts(): void
    {
        $data = $this->validate([
            'active_group_id' => ['required', 'exists:contact_groups,id'],
            'numbers' => ['nullable', 'string'],
            'numbers_file' => ['nullable', 'file', 'max:4096'],
        ]);

        $group = ContactGroup::query()
            ->whereBelongsTo(Auth::user())
            ->findOrFail($data['active_group_id']);

        $text = $data['numbers'] ?? '';
        if ($this->numbers_file) {
            $text .= "\n".$this->numbers_file->get();
        }

        $contacts = $this->parseImportedContacts($text, app(UgandaPhoneNumber::class));

        if ($contacts === []) {
            $this->addError('numbers', 'Paste or upload at least one valid Ugandan mobile number.');
            return;
        }

        $existingPhones = $group->contacts()->pluck('phone')->all();
        $newContacts = collect($contacts)
            ->reject(fn (array $contact) => in_array($contact['phone'], $existingPhones, true))
            ->map(fn (array $contact) => [
                'user_id' => Auth::id(),
                'name' => null,
                'phone' => $contact['phone'],
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->values()
            ->all();

        if ($newContacts !== []) {
            $group->contacts()->insert($newContacts);
        }

        $this->reset(['numbers', 'numbers_file']);
        session()->flash('status', count($newContacts).' number(s) imported into '.$group->name.'.');
    }

    public function render()
    {
        $groups = Auth::user()->contactGroups()->with('contacts')->withCount('contacts')->latest()->get();

        if (! $this->active_group_id && $groups->isNotEmpty()) {
            $this->active_group_id = $groups->first()->id;
        }

        $activeGroup = $groups->firstWhere('id', $this->active_group_id);
        $phoneNumber = app(UgandaPhoneNumber::class);

        return view('livewire.app.phonebook', [
            'groups' => $groups,
            'activeGroup' => $activeGroup,
            'providerCounts' => $activeGroup
                ? $activeGroup->contacts
                    ->groupBy(fn ($contact) => $phoneNumber->providerFor($contact->phone))
                    ->map->count()
                    ->sortKeys()
                : collect(),
        ])->layout('layouts.app');
    }

    private function parseImportedContacts(string $text, UgandaPhoneNumber $phoneNumber): array
    {
        $this->invalid_numbers = [];

        return collect(preg_split('/[\s,;]+/', $text, flags: PREG_SPLIT_NO_EMPTY))
            ->map(function (string $raw) use ($phoneNumber) {
                $token = strtolower(trim($raw));

                if (in_array($token, ['phone', 'phones', 'number', 'numbers', 'mobile', 'contact'], true)) {
                    return null;
                }

                $normalized = $phoneNumber->normalize($raw);

                if (! $normalized) {
                    $this->invalid_numbers[] = $raw;
                    return null;
                }

                return [
                    'phone' => $normalized['phone'],
                    'provider' => $normalized['provider'],
                ];
            })
            ->filter()
            ->unique('phone')
            ->values()
            ->all();
    }
}
