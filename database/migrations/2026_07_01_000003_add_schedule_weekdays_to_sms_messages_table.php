<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_messages', function (Blueprint $table): void {
            $table->json('schedule_weekdays')->nullable()->after('schedule_repeat_unit');
        });
    }

    public function down(): void
    {
        Schema::table('sms_messages', function (Blueprint $table): void {
            $table->dropColumn('schedule_weekdays');
        });
    }
};
