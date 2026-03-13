<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id('roles_id');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('name');
            $table->string('role_type')->default('admin');
            $table->string('status')->default('Active');
            $table->string('otp')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
