<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('emprendedores') || Schema::hasColumn('emprendedores', 'video_url')) {
            return;
        }

        Schema::table('emprendedores', function (Blueprint $table) {
            $table->string('video_url', 500)->nullable()->after('foto_portada');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('emprendedores') || ! Schema::hasColumn('emprendedores', 'video_url')) {
            return;
        }

        Schema::table('emprendedores', function (Blueprint $table) {
            $table->dropColumn('video_url');
        });
    }
};
