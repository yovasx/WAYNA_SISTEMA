<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('emprendedores')) {
            return;
        }

        Schema::table('emprendedores', function (Blueprint $table) {
            if (! Schema::hasColumn('emprendedores', 'logo_url')) {
                $table->string('logo_url', 500)->nullable()->after('foto_portada');
            }

            if (! Schema::hasColumn('emprendedores', 'redes_sociales')) {
                $table->json('redes_sociales')->nullable()->after('video_url');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('emprendedores')) {
            return;
        }

        $columnas = Schema::getColumnListing('emprendedores');

        Schema::table('emprendedores', function (Blueprint $table) use ($columnas) {
            if (in_array('redes_sociales', $columnas, true)) {
                $table->dropColumn('redes_sociales');
            }

            if (in_array('logo_url', $columnas, true)) {
                $table->dropColumn('logo_url');
            }
        });
    }
};
