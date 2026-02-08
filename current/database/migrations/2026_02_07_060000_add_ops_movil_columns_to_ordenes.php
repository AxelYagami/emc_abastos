<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->unsignedBigInteger('repartidor_id')->nullable()->after('tipo_entrega');
            $table->string('repartidor_nombre', 120)->nullable()->after('repartidor_id');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('preparing_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('en_ruta_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->index('repartidor_id');
            $table->index(['empresa_id', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->dropIndex(['repartidor_id']);
            $table->dropIndex(['empresa_id', 'status', 'created_at']);
            $table->dropColumn([
                'repartidor_id', 'repartidor_nombre',
                'confirmed_at', 'preparing_at', 'ready_at',
                'en_ruta_at', 'delivered_at', 'cancelled_at',
            ]);
        });
    }
};
