<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('entrega_id')->nullable();
            $table->string('event_key', 80)->nullable();
            $table->jsonb('payload_json')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('error')->nullable();
            $table->string('provider_msg_id', 255)->nullable();
            $table->timestamps();

            $table->index(['empresa_id', 'created_at']);
            $table->index(['empresa_id', 'status']);
            $table->index(['empresa_id', 'order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_notification_logs');
    }
};
