<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portales', function (Blueprint $table) {
            // Información general
            $table->string('tagline', 300)->nullable()->after('nombre');
            $table->text('descripcion')->nullable()->after('tagline');
            
            // Hero section
            $table->string('hero_title', 200)->nullable()->after('descripcion');
            $table->string('hero_subtitle', 300)->nullable()->after('hero_title');
            $table->string('hero_cta_text', 50)->default('Explorar tiendas')->after('hero_subtitle');
            
            // Template
            $table->string('active_template', 50)->default('default')->after('hero_cta_text');
            
            // Flyer settings
            $table->boolean('flyer_enabled')->default(true)->after('active_template');
            $table->string('flyer_title', 100)->default('Productos destacados')->after('flyer_enabled');
            $table->string('flyer_subtitle', 200)->nullable()->after('flyer_title');
            $table->integer('flyer_max_per_store')->default(5)->after('flyer_subtitle');
            $table->string('flyer_accent_color', 20)->nullable()->after('flyer_max_per_store');
            
            // Developer info
            $table->string('developer_name', 100)->nullable()->after('secondary_color');
            $table->string('developer_url', 255)->nullable()->after('developer_name');
            $table->string('developer_email', 255)->nullable()->after('developer_url');
            $table->string('developer_whatsapp', 20)->nullable()->after('developer_email');
            
            // Additional settings
            $table->string('home_redirect_path', 100)->default('portal')->after('settings');
            $table->integer('promos_per_store')->default(1)->after('home_redirect_path');
            $table->boolean('show_prices_in_portal')->default(true)->after('promos_per_store');
            $table->boolean('ai_assistant_enabled')->default(true)->after('show_prices_in_portal');
            $table->string('ai_assistant_title', 50)->default('Asistente IA')->after('ai_assistant_enabled');
            $table->text('ai_assistant_welcome')->nullable()->after('ai_assistant_title');
        });
    }

    public function down(): void
    {
        Schema::table('portales', function (Blueprint $table) {
            $table->dropColumn([
                'tagline', 'descripcion', 'hero_title', 'hero_subtitle', 'hero_cta_text',
                'active_template', 'flyer_enabled', 'flyer_title', 'flyer_subtitle',
                'flyer_max_per_store', 'flyer_accent_color', 'developer_name', 'developer_url',
                'developer_email', 'developer_whatsapp', 'home_redirect_path', 'promos_per_store',
                'show_prices_in_portal', 'ai_assistant_enabled', 'ai_assistant_title', 'ai_assistant_welcome'
            ]);
        });
    }
};
