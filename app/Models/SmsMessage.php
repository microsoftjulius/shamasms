<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'sender_id',
    'body',
    'mode',
    'status',
    'segments',
    'recipient_count',
    'scheduled_at',
    'schedule_repeat_count',
    'schedule_repeat_unit',
    'schedule_weekdays',
    'schedule_time',
    'external_reference',
])]
class SmsMessage extends Model
{
    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'segments' => 'integer',
            'recipient_count' => 'integer',
            'schedule_repeat_count' => 'integer',
            'schedule_weekdays' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(SmsRecipient::class);
    }
}
