<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Esta migracion solo limpia divergencias de esquemas heredados. En SQLite
        // de pruebas no aporta valor y algunos dropColumn legacy no son compatibles.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $this->limpiarCategorias();
        $this->limpiarProductos();
        $this->limpiarTablasSobrantes();
    }

    public function down(): void
    {
        // No se revierte — es limpieza de esquema legacy.
    }

    private function limpiarCategorias(): void
    {
        if (! Schema::hasTable('categorias')) {
            return;
        }

        $columnas = Schema::getColumnListing('categorias');

        if (in_array('slug', $columnas, true)) {
            Schema::table('categorias', function ($table) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            });
        }

        if (in_array('activa', $columnas, true)) {
            Schema::table('categorias', function ($table) {
                $table->dropColumn('activa');
            });
        }

        if (in_array('estado', $columnas, true)) {
            Schema::table('categorias', function ($table) {
                $table->dropColumn('estado');
            });
        }
    }

    private function limpiarProductos(): void
    {
        if (! Schema::hasTable('productos')) {
            return;
        }

        $columnas = Schema::getColumnListing('productos');

        $sobrantes = array_intersect($columnas, [
            'slug',
            'user_id',
            'imagen_url',
            'estado_disponibilidad',
            'destacado',
            'codigo_qr_publico',
            'publicado_at',
            'perfil_emprendedor_id',
        ]);

        foreach ($sobrantes as $columna) {
            Schema::table('productos', function ($table) use ($columna) {
                $table->dropColumn($columna);
            });
        }
    }

    private function limpiarTablasSobrantes(): void
    {
        $tablesToDrop = [
            'perfiles_emprendedores',
            'imagenes_producto',
            'rankings_donadores',
            'items_carrito',
            'carritos',
            'registros_notificaciones',
        ];

        foreach ($tablesToDrop as $table) {
            if (Schema::hasTable($table)) {
                Schema::dropIfExists($table);
            }
        }
    }
};
