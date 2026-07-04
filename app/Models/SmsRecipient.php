<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['sms_message_id', 'phone', 'name', 'var1', 'var2', 'var3', 'var4', 'var5', 'rendered_body', 'status', 'provider_reference'])]
class SmsRecipient extends Model
{
    public function message(): BelongsTo
    {
        return $this->belongsTo(SmsMessage::class, 'sms_message_id');
    }
}
