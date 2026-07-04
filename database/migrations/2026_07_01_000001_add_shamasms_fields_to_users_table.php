<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('username')->unique()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->string('company')->nullable()->after('phone');
            $table->unsignedInteger('sms_balance')->default(0)->after('company');
            $table->boolean('is_admin')->default(false)->after('sms_balance');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['username', 'phone', 'company', 'sms_balance', 'is_admin']);
        });
    }
};
