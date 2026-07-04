<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('integration_settings')
            ->where('provider', 'ugsms')
            ->update(['provider' => 'sms_gateway']);
    }

    public function down(): void
    {
        DB::table('integration_settings')
            ->where('provider', 'sms_gateway')
            ->update(['provider' => 'ugsms']);
    }
};
