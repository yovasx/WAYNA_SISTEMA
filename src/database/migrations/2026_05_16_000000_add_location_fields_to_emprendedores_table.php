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
            if (! Schema::hasColumn('emprendedores', 'tiene_local_fisico')) {
                $table->boolean('tiene_local_fisico')->default(false)->after('redes_sociales');
            }

            if (! Schema::hasColumn('emprendedores', 'ciudad')) {
                $table->string('ciudad', 120)->nullable()->after('tiene_local_fisico');
            }

            if (! Schema::hasColumn('emprendedores', 'direccion_calle')) {
                $table->string('direccion_calle', 180)->nullable()->after('ciudad');
            }

            if (! Schema::hasColumn('emprendedores', 'direccion_numero')) {
                $table->string('direccion_numero', 30)->nullable()->after('direccion_calle');
            }

            if (! Schema::hasColumn('emprendedores', 'latitud')) {
                $table->decimal('latitud', 10, 7)->nullable()->after('direccion_numero');
            }

            if (! Schema::hasColumn('emprendedores', 'longitud')) {
                $table->decimal('longitud', 10, 7)->nullable()->after('latitud');
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
            foreach (['longitud', 'latitud', 'direccion_numero', 'direccion_calle', 'ciudad', 'tiene_local_fisico'] as $columna) {
                if (in_array($columna, $columnas, true)) {
                    $table->dropColumn($columna);
                }
            }
        });
    }
};
