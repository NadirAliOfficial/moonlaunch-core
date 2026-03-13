<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->id();
            $table->string('chain_id')->default('56');
            $table->string('token_address')->unique();
            $table->string('pair_address')->nullable();
            $table->string('name')->nullable();
            $table->string('symbol')->nullable();
            $table->string('decimals')->nullable();
            $table->string('logo')->nullable();
            $table->string('initial_price')->nullable();
            $table->string('price_currency')->nullable();
            $table->string('tx_hash')->nullable();
            $table->string('log_index')->nullable();
            $table->string('block_number')->nullable();
            $table->timestamp('detected_at')->nullable();
            // Phase separation fields (required by engineering directive)
            $table->string('source_type')->default('pancakeswap');
            $table->string('trade_mode')->default('router_swap');
            $table->string('status')->default('active');
            $table->boolean('is_tradeable')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tokens');
    }
};
