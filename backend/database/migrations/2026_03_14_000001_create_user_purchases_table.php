<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('wallet_address', 42)->index();
            $table->string('token_address', 42);
            $table->string('tx_hash', 66)->nullable();
            $table->timestamps();
            $table->unique(['wallet_address', 'token_address']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_purchases');
    }
};
