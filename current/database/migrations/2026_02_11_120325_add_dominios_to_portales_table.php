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
        Schema::table('portales', function (Blueprint $table) {
            $table->json('dominios')->nullable()->after('descripcion');
            // dominios structure: ["bodegadigital.com.mx", "www.bodegadigital.com.mx", "portal-mty.com"]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portales', function (Blueprint $table) {
            $table->dropColumn('dominios');
        });
    }
};
