<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->crearUsuarios();
        $this->crearRoles();
        $this->migrarUsuariosExistentes();
        $this->crearEmprendedores();
        $this->ajustarCategorias();
        $this->ajustarProductos();
        $this->crearProductoFotos();
        $this->crearPedidos();
        $this->crearPedidoItems();
        $this->crearTransacciones();
        $this->crearReservas();
        $this->crearDonaciones();
        $this->crearPuntosDonador();
        $this->crearHistorialPuntos();
        $this->crearInsignias();
    }

    public function down(): void
    {
        // No se implementa reversa automatica por tratarse de una sincronizacion de negocio.
    }

    private function crearUsuarios(): void
    {
        if (! Schema::hasTable('usuarios')) {
            Schema::create('usuarios', function (Blueprint $table) {
                $table->id();
                $table->string('nombre_completo', 150);
                $table->string('email')->unique();
                $table->string('telefono', 20)->nullable();
                $table->string('password_hash');
                $table->string('foto_perfil', 500)->nullable();
                $table->string('estado')->default('pendiente');
                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }
    }

    private function crearRoles(): void
    {
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('nombre')->unique();
            });
        }

        if (! Schema::hasTable('usuario_roles')) {
            Schema::create('usuario_roles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
                $table->foreignId('rol_id')->constrained('roles')->cascadeOnDelete();
                $table->boolean('activo')->default(true);
                $table->timestamp('asignado_en')->useCurrent();
                $table->unique(['usuario_id', 'rol_id']);
            });
        }
    }

    private function migrarUsuariosExistentes(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $usuarios = DB::table('users')->get();

        foreach ($usuarios as $usuario) {
            $nuevoId = DB::table('usuarios')->where('email', $usuario->email)->value('id');

            if (! $nuevoId) {
                $nuevoId = DB::table('usuarios')->insertGetId([
                    'nombre_completo' => $usuario->name,
                    'email' => $usuario->email,
                    'telefono' => $usuario->telefono ?? null,
                    'password_hash' => $usuario->password,
                    'foto_perfil' => null,
                    'estado' => $usuario->estado ?? 'activo',
                    'email_verified_at' => $usuario->email_verified_at ?? null,
                    'remember_token' => $usuario->remember_token ?? null,
                    'created_at' => $usuario->created_at ?? now(),
                    'updated_at' => $usuario->updated_at ?? now(),
                ]);
            }

            if (Schema::hasColumn('users', 'rol') && $usuario->rol) {
                $rolId = DB::table('roles')->where('nombre', strtoupper(match ($usuario->rol) {
                    'admin', 'administrador' => 'ADMINISTRADOR',
                    'emprendedor' => 'EMPRENDEDOR',
                    'donador' => 'DONADOR',
                    'turista' => 'TURISTA',
                    default => 'COMPRADOR',
                }))->value('id');

                if (! $rolId) {
                    $rolId = DB::table('roles')->insertGetId(['nombre' => strtoupper(match ($usuario->rol) {
                        'admin', 'administrador' => 'ADMINISTRADOR',
                        'emprendedor' => 'EMPRENDEDOR',
                        'donador' => 'DONADOR',
                        'turista' => 'TURISTA',
                        default => 'COMPRADOR',
                    })]);
                }

                DB::table('usuario_roles')->updateOrInsert([
                    'usuario_id' => $nuevoId,
                    'rol_id' => $rolId,
                ], [
                    'activo' => true,
                    'asignado_en' => now(),
                ]);
            }
        }
    }

    private function crearEmprendedores(): void
    {
        if (! Schema::hasTable('emprendedores')) {
            Schema::create('emprendedores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete()->unique();
                $table->string('nombre_negocio', 150);
                $table->text('descripcion')->nullable();
                $table->text('historia')->nullable();
                $table->string('foto_portada', 500)->nullable();
                $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
                $table->string('nit', 20)->nullable();
                $table->string('estado')->default('pendiente');
                $table->foreignId('aprobado_por')->nullable()->references('id')->on('usuarios')->nullOnDelete();
                $table->timestamp('aprobado_en')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('perfiles_emprendedores')) {
            $perfiles = DB::table('perfiles_emprendedores')->get();

            foreach ($perfiles as $perfil) {
                $email = DB::table('users')->where('id', $perfil->usuario_id)->value('email');
                $usuarioIdNuevo = $email ? DB::table('usuarios')->where('email', $email)->value('id') : null;

                if (! $usuarioIdNuevo || DB::table('emprendedores')->where('usuario_id', $usuarioIdNuevo)->exists()) {
                    continue;
                }

                DB::table('emprendedores')->insert([
                    'usuario_id' => $usuarioIdNuevo,
                    'nombre_negocio' => $perfil->nombre_emprendimiento ?? DB::table('usuarios')->where('id', $usuarioIdNuevo)->value('nombre_completo'),
                    'descripcion' => null,
                    'historia' => $perfil->historia ?? null,
                    'foto_portada' => $perfil->portada_url ?? null,
                    'categoria_id' => $perfil->categoria_id ?? null,
                    'nit' => null,
                    'estado' => match ($perfil->estado_aprobacion ?? 'pendiente') {
                        'aprobado' => 'activo',
                        'suspendido' => 'suspendido',
                        default => 'pendiente',
                    },
                    'aprobado_por' => null,
                    'aprobado_en' => null,
                    'created_at' => $perfil->created_at ?? now(),
                    'updated_at' => $perfil->updated_at ?? now(),
                ]);
            }
        }
    }

    private function ajustarCategorias(): void
    {
        if (Schema::hasTable('categorias') && ! Schema::hasColumn('categorias', 'icono')) {
            Schema::table('categorias', function (Blueprint $table) {
                $table->string('icono')->nullable()->after('nombre');
            });
        }
    }

    private function ajustarProductos(): void
    {
        if (! Schema::hasTable('productos')) {
            return;
        }

        Schema::table('productos', function (Blueprint $table) {
            if (! Schema::hasColumn('productos', 'emprendedor_id')) {
                $table->foreignId('emprendedor_id')->nullable()->after('id');
            }
            if (! Schema::hasColumn('productos', 'estado_stock')) {
                $table->string('estado_stock')->default('disponible')->after('stock');
            }
            if (! Schema::hasColumn('productos', 'foto_principal')) {
                $table->string('foto_principal', 500)->nullable()->after('estado_stock');
            }
            if (! Schema::hasColumn('productos', 'qr_codigo')) {
                $table->string('qr_codigo', 100)->nullable()->after('foto_principal');
            }
            if (! Schema::hasColumn('productos', 'qr_url')) {
                $table->string('qr_url', 500)->nullable()->after('qr_codigo');
            }
            if (! Schema::hasColumn('productos', 'activo')) {
                $table->boolean('activo')->default(true)->after('qr_url');
            }
        });

        $productos = DB::table('productos')->get();

        foreach ($productos as $producto) {
            $emprendedorId = $producto->emprendedor_id ?? null;

            if (! $emprendedorId && Schema::hasColumn('productos', 'perfil_emprendedor_id')) {
                $usuarioIdViejo = DB::table('perfiles_emprendedores')->where('id', $producto->perfil_emprendedor_id)->value('usuario_id');
                $email = $usuarioIdViejo ? DB::table('users')->where('id', $usuarioIdViejo)->value('email') : null;
                $usuarioIdNuevo = $email ? DB::table('usuarios')->where('email', $email)->value('id') : null;
                $emprendedorId = $usuarioIdNuevo ? DB::table('emprendedores')->where('usuario_id', $usuarioIdNuevo)->value('id') : null;
            }

            DB::table('productos')->where('id', $producto->id)->update([
                'emprendedor_id' => $emprendedorId,
                'estado_stock' => $producto->estado_stock ?? ($producto->estado_disponibilidad ?? 'disponible'),
                'foto_principal' => $producto->foto_principal ?? ($producto->imagen_url ?? null),
                'qr_codigo' => $producto->qr_codigo ?? ($producto->codigo_qr_publico ?? null),
                'activo' => $producto->activo ?? ($producto->estado !== 'inactivo'),
            ]);
        }
    }

    private function crearProductoFotos(): void
    {
        if (! Schema::hasTable('producto_fotos')) {
            Schema::create('producto_fotos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
                $table->string('url_foto', 500);
                $table->integer('orden');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('imagenes_producto')) {
            $fotos = DB::table('imagenes_producto')->get();

            foreach ($fotos as $foto) {
                DB::table('producto_fotos')->updateOrInsert([
                    'producto_id' => $foto->producto_id,
                    'url_foto' => $foto->url,
                    'orden' => $foto->orden ?? 1,
                ], [
                    'created_at' => $foto->created_at ?? now(),
                    'updated_at' => $foto->updated_at ?? now(),
                ]);
            }
        }
    }

    private function crearPedidos(): void
    {
        if (! Schema::hasTable('pedidos')) {
            Schema::create('pedidos', function (Blueprint $table) {
                $table->id();
                $table->string('codigo', 20)->unique();
                $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
                $table->foreignId('emprendedor_id')->constrained('emprendedores')->cascadeOnDelete();
                $table->string('estado')->default('pendiente');
                $table->string('metodo_entrega')->default('retiro_tienda');
                $table->decimal('total', 10, 2);
                $table->text('notas')->nullable();
                $table->timestamps();
            });
        }
    }

    private function crearPedidoItems(): void
    {
        if (! Schema::hasTable('pedido_items')) {
            Schema::create('pedido_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
                $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
                $table->integer('cantidad');
                $table->decimal('precio_unitario', 10, 2);
                $table->decimal('subtotal', 10, 2);
            });
        }
    }

    private function crearTransacciones(): void
    {
        if (! Schema::hasTable('transacciones')) {
            Schema::create('transacciones', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('referencia_id');
                $table->string('referencia_tipo');
                $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
                $table->string('metodo_pago');
                $table->decimal('monto', 10, 2);
                $table->string('estado')->default('pendiente');
                $table->string('codigo_qr')->nullable();
                $table->jsonb('respuesta_pasarela')->nullable();
                $table->timestamp('procesado_en')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->index(['referencia_id', 'referencia_tipo']);
            });
        }
    }

    private function crearReservas(): void
    {
        if (! Schema::hasTable('reservas')) {
            Schema::create('reservas', function (Blueprint $table) {
                $table->id();
                $table->string('codigo', 20)->unique();
                $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
                $table->foreignId('emprendedor_id')->constrained('emprendedores')->cascadeOnDelete();
                $table->string('tipo');
                $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
                $table->date('fecha_reserva');
                $table->time('hora_reserva');
                $table->string('estado')->default('pendiente');
                $table->string('qr_acceso')->nullable();
                $table->text('notas')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    private function crearDonaciones(): void
    {
        if (! Schema::hasTable('donaciones')) {
            Schema::create('donaciones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
                $table->foreignId('emprendedor_id')->nullable()->constrained('emprendedores')->nullOnDelete();
                $table->decimal('monto', 10, 2);
                $table->boolean('es_anonima')->default(false);
                $table->text('mensaje')->nullable();
                $table->string('certificado_url', 500)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    private function crearPuntosDonador(): void
    {
        if (! Schema::hasTable('puntos_donador')) {
            Schema::create('puntos_donador', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->unique()->constrained('usuarios')->cascadeOnDelete();
                $table->integer('puntos_total')->default(0);
                $table->string('nivel')->default('bronce');
                $table->timestamp('updated_at')->useCurrent();
            });
        }
    }

    private function crearHistorialPuntos(): void
    {
        if (! Schema::hasTable('historial_puntos')) {
            Schema::create('historial_puntos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
                $table->foreignId('donacion_id')->constrained('donaciones')->cascadeOnDelete();
                $table->integer('puntos_ganados');
                $table->decimal('multiplicador', 3, 1)->default(1);
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    private function crearInsignias(): void
    {
        if (! Schema::hasTable('insignias')) {
            Schema::create('insignias', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 100)->unique();
                $table->text('descripcion')->nullable();
                $table->string('icono', 500)->nullable();
                $table->jsonb('criterio_json')->nullable();
            });
        }
    }
};
