<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            // Envío gratis a partir de cierta cantidad
            $table->boolean('delivery_free_enabled')->default(false)->after('enable_delivery');
            $table->decimal('delivery_free_min_amount', 10, 2)->nullable()->after('delivery_free_enabled');

            // Envío con costo dependiendo de cobertura/zona
            $table->boolean('delivery_zones_enabled')->default(false)->after('delivery_free_min_amount');
            $table->json('delivery_zones')->nullable()->after('delivery_zones_enabled');
            // delivery_zones structure: [{"name": "Zona Centro", "cost": 50}, {"name": "Zona Norte", "cost": 80}, ...]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_free_enabled',
                'delivery_free_min_amount',
                'delivery_zones_enabled',
                'delivery_zones',
            ]);
        });
    }
};
