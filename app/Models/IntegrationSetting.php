<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['provider', 'label', 'base_url', 'api_key', 'api_secret', 'username', 'password', 'is_sandbox', 'is_active', 'metadata'])]
class IntegrationSetting extends Model
{
    protected function casts(): array
    {
        return [
            'is_sandbox' => 'boolean',
            'is_active' => 'boolean',
            'metadata' => 'array',
        ];
    }
}
