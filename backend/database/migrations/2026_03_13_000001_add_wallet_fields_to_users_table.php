<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('wallet_address')->nullable()->after('password');
            $table->string('turnkey_suborg_id')->nullable()->after('wallet_address');
            $table->string('turnkey_wallet_id')->nullable()->after('turnkey_suborg_id');
            $table->string('status')->default('Active')->after('turnkey_wallet_id');
            $table->string('otp')->nullable()->after('status');
            $table->timestamp('otp_expires_at')->nullable()->after('otp');
            $table->string('profile_pick')->nullable()->after('otp_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'wallet_address',
                'turnkey_suborg_id',
                'turnkey_wallet_id',
                'status',
                'otp',
                'otp_expires_at',
                'profile_pick',
            ]);
        });
    }
};
