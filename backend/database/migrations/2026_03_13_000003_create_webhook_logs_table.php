<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tx_hash');
            $table->string('log_index')->nullable();
            $table->longText('raw_payload');
            $table->string('status')->default('pending');
            $table->text('skip_reason')->nullable();
            $table->timestamps();
            $table->unique(['tx_hash', 'log_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};
