<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'min_amount', 'sms_unit_price', 'is_active'])]
class PriceTier extends Model
{
    protected function casts(): array
    {
        return [
            'min_amount' => 'integer',
            'sms_unit_price' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
