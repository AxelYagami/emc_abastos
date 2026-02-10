<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 160);
            $table->string('slug', 100)->unique();
            $table->string('dominio', 255)->nullable()->unique();
            $table->string('logo_path', 500)->nullable();
            $table->string('favicon_path', 500)->nullable();
            $table->string('primary_color', 20)->default('#16a34a');
            $table->string('secondary_color', 20)->default('#6b7280');
            $table->jsonb('settings')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Agregar portal_id a empresas
        Schema::table('empresas', function (Blueprint $table) {
            $table->foreignId('portal_id')->nullable()->after('id')->constrained('portales')->onDelete('set null');
        });

        // Agregar portal_id a portal_config para config por portal
        Schema::table('portal_config', function (Blueprint $table) {
            $table->foreignId('portal_id')->nullable()->after('id')->constrained('portales')->onDelete('cascade');
            $table->dropUnique(['key']);
            $table->unique(['portal_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::table('portal_config', function (Blueprint $table) {
            $table->dropForeign(['portal_id']);
            $table->dropUnique(['portal_id', 'key']);
            $table->dropColumn('portal_id');
            $table->unique(['key']);
        });

        Schema::table('empresas', function (Blueprint $table) {
            $table->dropForeign(['portal_id']);
            $table->dropColumn('portal_id');
        });

        Schema::dropIfExists('portales');
    }
};
