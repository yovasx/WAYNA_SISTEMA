<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->normalizarNitEmprendedores();
        $this->ajustarProductos();
    }

    public function down(): void
    {
        // No se revierte automaticamente para no reintroducir divergencias legacy.
    }

    private function normalizarNitEmprendedores(): void
    {
        if (! Schema::hasTable('emprendedores') || DB::getDriverName() !== 'pgsql') {
            return;
        }

        $columnas = Schema::getColumnListing('emprendedores');

        if (in_array('NIT', $columnas, true) && ! in_array('nit', $columnas, true)) {
            DB::statement('ALTER TABLE emprendedores RENAME COLUMN "NIT" TO nit');
        }
    }

    private function ajustarProductos(): void
    {
        if (! Schema::hasTable('productos')) {
            return;
        }

        $columnas = Schema::getColumnListing('productos');

        if (in_array('estado', $columnas, true)) {
            Schema::table('productos', function (Blueprint $table) {
                $table->dropColumn('estado');
            });
        }

        if (! in_array('emprendedor_id', $columnas, true) || DB::getDriverName() !== 'pgsql') {
            return;
        }

        $productosSinEmprendedorValido = DB::table('productos')
            ->leftJoin('emprendedores', 'emprendedores.id', '=', 'productos.emprendedor_id')
            ->whereNotNull('productos.emprendedor_id')
            ->whereNull('emprendedores.id')
            ->pluck('productos.id');

        if ($productosSinEmprendedorValido->isNotEmpty()) {
            DB::table('productos')
                ->whereIn('id', $productosSinEmprendedorValido)
                ->update(['emprendedor_id' => null]);
        }

        if (in_array('qr_codigo', $columnas, true)) {
            DB::table('productos')->where('qr_codigo', '')->update(['qr_codigo' => null]);

            $duplicadosQr = DB::table('productos')
                ->select('qr_codigo')
                ->whereNotNull('qr_codigo')
                ->groupBy('qr_codigo')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('qr_codigo');

            foreach ($duplicadosQr as $qrCodigo) {
                $ids = DB::table('productos')
                    ->where('qr_codigo', $qrCodigo)
                    ->orderBy('id')
                    ->pluck('id');

                foreach ($ids->slice(1) as $productoId) {
                    DB::table('productos')
                        ->where('id', $productoId)
                        ->update(['qr_codigo' => $qrCodigo.'-'.$productoId]);
                }
            }
        }

        try {
            DB::statement('CREATE INDEX productos_emprendedor_id_index ON productos (emprendedor_id)');
        } catch (Throwable) {
            // El indice ya existe.
        }

        try {
            DB::statement('ALTER TABLE productos ADD CONSTRAINT productos_emprendedor_id_foreign FOREIGN KEY (emprendedor_id) REFERENCES emprendedores(id)');
        } catch (Throwable) {
            // La foreign key ya existe o la base no permite recrearla.
        }

        if (in_array('qr_codigo', $columnas, true)) {
            try {
                DB::statement('ALTER TABLE productos ADD CONSTRAINT productos_qr_codigo_unique UNIQUE (qr_codigo)');
            } catch (Throwable) {
                // La restriccion unique ya existe.
            }
        }

        if (DB::table('productos')->whereNull('emprendedor_id')->doesntExist()) {
            try {
                DB::statement('ALTER TABLE productos ALTER COLUMN emprendedor_id SET NOT NULL');
            } catch (Throwable) {
                // Se mantiene nullable si existen datos que todavia requieren correccion manual.
            }
        }
    }
};
