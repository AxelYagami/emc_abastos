<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('portal_id')->nullable()->after('empresa_id');
            $table->index('portal_id');
        });

        // Migrate existing clients: set portal_id from their empresa
        DB::statement('UPDATE clientes SET portal_id = (SELECT portal_id FROM empresas WHERE empresas.id = clientes.empresa_id) WHERE portal_id IS NULL');
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('portal_id');
        });
    }
};
