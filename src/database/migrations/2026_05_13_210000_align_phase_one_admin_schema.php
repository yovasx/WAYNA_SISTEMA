<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $this->alinearCategorias();
        $this->crearPerfilesEmprendedores();
        $this->alinearProductos();
        $this->crearImagenesProducto();
    }

    public function down(): void
    {
        // Esta migracion alinea un esquema historico divergente.
        // No se revierte automaticamente para evitar perdida accidental.
    }

    private function alinearCategorias(): void
    {
        if (! Schema::hasTable('categorias')) {
            return;
        }

        Schema::table('categorias', function (Blueprint $table) {
            if (! Schema::hasColumn('categorias', 'slug')) {
                $table->string('slug', 140)->nullable()->after('nombre');
            }

            if (! Schema::hasColumn('categorias', 'activa')) {
                $table->boolean('activa')->default(true)->after('descripcion');
            }
        });

        $categorias = DB::table('categorias')->select('id', 'nombre', 'slug')->orderBy('id')->get();
        $usados = [];

        foreach ($categorias as $categoria) {
            $base = Str::slug($categoria->nombre ?: 'categoria');
            $slug = $base !== '' ? $base : 'categoria';
            $contador = 1;

            while (in_array($slug, $usados, true) || DB::table('categorias')->where('slug', $slug)->where('id', '!=', $categoria->id)->exists()) {
                $slug = $base.'-'.$contador;
                $contador++;
            }

            DB::table('categorias')->where('id', $categoria->id)->update([
                'slug' => $slug,
                'activa' => Schema::hasColumn('categorias', 'estado')
                    ? DB::table('categorias')->where('id', $categoria->id)->value('estado')
                    : true,
            ]);

            $usados[] = $slug;
        }

        try {
            Schema::table('categorias', function (Blueprint $table) {
                $table->unique('slug');
            });
        } catch (Throwable) {
            // El indice ya existe o el motor no permite recrearlo.
        }
    }

    private function crearPerfilesEmprendedores(): void
    {
        if (! Schema::hasTable('perfiles_emprendedores')) {
            Schema::create('perfiles_emprendedores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->unique()->constrained('users')->cascadeOnDelete();
                $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
                $table->string('nombre_emprendimiento', 180);
                $table->text('historia')->nullable();
                $table->string('foto_perfil_url')->nullable();
                $table->string('portada_url')->nullable();
                $table->string('direccion')->nullable();
                $table->string('ciudad', 120)->nullable();
                $table->string('pais', 120)->default('Bolivia');
                $table->string('sitio_web')->nullable();
                $table->json('redes_sociales')->nullable();
                $table->string('estado_aprobacion', 255)->default('pendiente');
                $table->boolean('acepta_donaciones')->default(true);
                $table->timestamps();
            });
        }

        $usuarios = DB::table('users')->where('rol', 'emprendedor');

        if (Schema::hasColumn('productos', 'user_id')) {
            $usuarios->orWhereIn('id', DB::table('productos')->whereNotNull('user_id')->pluck('user_id'));
        }

        $usuarios = $usuarios->select('id', 'name')->distinct()->get();

        foreach ($usuarios as $usuario) {
            if (! DB::table('perfiles_emprendedores')->where('usuario_id', $usuario->id)->exists()) {
                DB::table('perfiles_emprendedores')->insert([
                    'usuario_id' => $usuario->id,
                    'nombre_emprendimiento' => $usuario->name,
                    'pais' => 'Bolivia',
                    'estado_aprobacion' => 'aprobado',
                    'acepta_donaciones' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function alinearProductos(): void
    {
        if (! Schema::hasTable('productos')) {
            return;
        }

        Schema::table('productos', function (Blueprint $table) {
            if (! Schema::hasColumn('productos', 'perfil_emprendedor_id')) {
                $table->foreignId('perfil_emprendedor_id')->nullable()->after('id');
            }

            if (! Schema::hasColumn('productos', 'slug')) {
                $table->string('slug', 220)->nullable()->after('nombre');
            }

            if (! Schema::hasColumn('productos', 'estado_disponibilidad')) {
                $table->string('estado_disponibilidad', 255)->default('disponible')->after('stock');
            }

            if (! Schema::hasColumn('productos', 'destacado')) {
                $table->boolean('destacado')->default(false)->after('estado_disponibilidad');
            }

            if (! Schema::hasColumn('productos', 'codigo_qr_publico')) {
                $table->string('codigo_qr_publico')->nullable()->after('destacado');
            }

            if (! Schema::hasColumn('productos', 'publicado_at')) {
                $table->timestamp('publicado_at')->nullable()->after('codigo_qr_publico');
            }
        });

        $productosQuery = DB::table('productos')->select('id', 'nombre', 'created_at');

        if (Schema::hasColumn('productos', 'user_id')) {
            $productosQuery->addSelect('user_id');
        }

        if (Schema::hasColumn('productos', 'estado')) {
            $productosQuery->addSelect('estado');
        }

        $productos = $productosQuery->orderBy('id')->get();
        $slugs = [];

        foreach ($productos as $producto) {
            $perfilId = null;

            if (Schema::hasColumn('productos', 'user_id') && $producto->user_id) {
                $perfilId = DB::table('perfiles_emprendedores')->where('usuario_id', $producto->user_id)->value('id');
            }

            $base = Str::slug($producto->nombre ?: 'producto');
            $slug = $base !== '' ? $base : 'producto';
            $contador = 1;

            while (in_array($slug, $slugs, true) || DB::table('productos')->where('slug', $slug)->where('id', '!=', $producto->id)->exists()) {
                $slug = $base.'-'.$contador;
                $contador++;
            }

            $estadoLegacy = Schema::hasColumn('productos', 'estado') ? DB::table('productos')->where('id', $producto->id)->value('estado') : null;
            $estadoDisponibilidad = $estadoLegacy === 'inactivo' ? 'agotado' : 'disponible';

            DB::table('productos')->where('id', $producto->id)->update([
                'perfil_emprendedor_id' => $perfilId,
                'slug' => $slug,
                'estado_disponibilidad' => $estadoDisponibilidad,
                'publicado_at' => $producto->created_at,
            ]);

            $slugs[] = $slug;
        }

        try {
            Schema::table('productos', function (Blueprint $table) {
                $table->unique('slug');
            });
        } catch (Throwable) {
            // El indice ya existe o fue creado antes.
        }
    }

    private function crearImagenesProducto(): void
    {
        if (! Schema::hasTable('imagenes_producto')) {
            Schema::create('imagenes_producto', function (Blueprint $table) {
                $table->id();
                $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
                $table->string('url');
                $table->string('texto_alternativo')->nullable();
                $table->unsignedSmallInteger('orden')->default(1);
                $table->boolean('es_principal')->default(false);
                $table->timestamps();
            });
        }
    }
};
