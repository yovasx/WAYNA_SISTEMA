<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Donacion;
use App\Models\HistorialPuntos;
use App\Models\ImagenProducto;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\PerfilEmprendedor;
use App\Models\Producto;
use App\Models\PuntosDonador;
use App\Models\Reserva;
use App\Models\Role;
use App\Models\Transaccion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WaynaRealisticSeeder extends Seeder
{
    public function run(): void
    {
        $roles = $this->seedRoles();
        $categorias = $this->seedCategorias();
        $usuarios = $this->seedUsuarios($roles);
        $emprendedores = $this->seedEmprendedores($usuarios, $categorias);
        $productos = $this->seedProductos($emprendedores, $categorias);
        $pedidos = $this->seedPedidos($usuarios, $emprendedores, $productos);
        $reservas = $this->seedReservas($usuarios, $emprendedores, $productos);
        $donaciones = $this->seedDonaciones($usuarios, $emprendedores);

        $this->seedTransacciones($pedidos, $reservas, $donaciones);
        $this->seedPuntosDonador($usuarios, $donaciones);
    }

    private function seedRoles(): Collection
    {
        return collect(['ADMINISTRADOR', 'EMPRENDEDOR', 'COMPRADOR', 'DONADOR', 'TURISTA'])
            ->mapWithKeys(fn (string $nombre) => [$nombre => Role::query()->firstOrCreate(['nombre' => $nombre])]);
    }

    private function seedCategorias(): Collection
    {
        $categorias = [
            'textiles' => [
                'nombre' => 'Textiles',
                'icono' => 'checkroom',
                'descripcion' => 'Prendas, mantas y accesorios tejidos a mano con fibras y tecnicas tradicionales andinas.',
            ],
            'ceramica' => [
                'nombre' => 'Ceramica',
                'icono' => 'emoji_food_beverage',
                'descripcion' => 'Piezas utilitarias y decorativas modeladas y cocidas por talleres artesanales locales.',
            ],
            'joyeria' => [
                'nombre' => 'Joyeria artesanal',
                'icono' => 'diamond',
                'descripcion' => 'Joyeria de autor inspirada en simbolos andinos, plata trabajada y acabados hechos a mano.',
            ],
            'cuero' => [
                'nombre' => 'Cuero',
                'icono' => 'work',
                'descripcion' => 'Accesorios y piezas funcionales en cuero curtido con procesos artesanales y durables.',
            ],
            'madera' => [
                'nombre' => 'Madera decorativa',
                'icono' => 'table_restaurant',
                'descripcion' => 'Objetos para mesa y decoracion con terminaciones cuidadas, madera local y oficio familiar.',
            ],
            'decoracion' => [
                'nombre' => 'Decoracion cultural',
                'icono' => 'home',
                'descripcion' => 'Elementos decorativos con identidad regional, pensados para hogar, hoteleria y regalo.',
            ],
        ];

        return collect($categorias)->mapWithKeys(function (array $data, string $alias) {
            return [$alias => Categoria::query()->updateOrCreate(
                ['nombre' => $data['nombre']],
                [
                    'icono' => $data['icono'],
                    'descripcion' => $data['descripcion'],
                ]
            )];
        });
    }

    private function seedUsuarios(Collection $roles): Collection
    {
        $usuarios = [
            'admin' => [
                'nombre' => 'Administrador WAYNA',
                'email' => 'admin@admin.gmail.com',
                'telefono' => '71234567',
                'password' => 'admin123',
                'estado' => 'activo',
                'foto' => 'https://randomuser.me/api/portraits/men/11.jpg',
                'roles' => ['ADMINISTRADOR'],
            ],
            'demo_emprendedor' => [
                'nombre' => 'Lucia Quispe Mamani',
                'email' => 'emprendedor@wayna.bo',
                'telefono' => '72114567',
                'password' => 'admin123',
                'estado' => 'activo',
                'foto' => 'https://randomuser.me/api/portraits/women/44.jpg',
                'roles' => ['EMPRENDEDOR'],
            ],
            'demo_usuario' => [
                'nombre' => 'Diego Soria Vargas',
                'email' => 'usuario@wayna.bo',
                'telefono' => '73455678',
                'password' => 'admin123',
                'estado' => 'activo',
                'foto' => 'https://randomuser.me/api/portraits/men/52.jpg',
                'roles' => ['COMPRADOR'],
            ],
            'mariela' => [
                'nombre' => 'Mariela Castro Rojas',
                'email' => 'mariela.castro@wayna.bo',
                'telefono' => '77781234',
                'password' => 'admin123',
                'estado' => 'activo',
                'foto' => 'https://randomuser.me/api/portraits/women/65.jpg',
                'roles' => ['COMPRADOR', 'DONADOR'],
            ],
            'ruben' => [
                'nombre' => 'Ruben Rivera Choque',
                'email' => 'ruben.rivera@wayna.bo',
                'telefono' => '69873412',
                'password' => 'admin123',
                'estado' => 'activo',
                'foto' => 'https://randomuser.me/api/portraits/men/36.jpg',
                'roles' => ['COMPRADOR', 'TURISTA'],
            ],
            'camila' => [
                'nombre' => 'Camila Pardo Ledezma',
                'email' => 'camila.pardo@wayna.bo',
                'telefono' => '72119876',
                'password' => 'admin123',
                'estado' => 'activo',
                'foto' => 'https://randomuser.me/api/portraits/women/24.jpg',
                'roles' => ['COMPRADOR'],
            ],
            'javier' => [
                'nombre' => 'Javier Gutierrez Salazar',
                'email' => 'javier.gutierrez@wayna.bo',
                'telefono' => '76542311',
                'password' => 'admin123',
                'estado' => 'pendiente',
                'foto' => 'https://randomuser.me/api/portraits/men/43.jpg',
                'roles' => ['COMPRADOR'],
            ],
            'paola' => [
                'nombre' => 'Paola Mendieta Flores',
                'email' => 'paola.mendieta@wayna.bo',
                'telefono' => '70345672',
                'password' => 'admin123',
                'estado' => 'activo',
                'foto' => 'https://randomuser.me/api/portraits/women/18.jpg',
                'roles' => ['COMPRADOR', 'DONADOR'],
            ],
            'nicolas' => [
                'nombre' => 'Nicolas Torrico Vaca',
                'email' => 'nicolas.torrico@wayna.bo',
                'telefono' => '61234578',
                'password' => 'admin123',
                'estado' => 'suspendido',
                'foto' => 'https://randomuser.me/api/portraits/men/21.jpg',
                'roles' => ['COMPRADOR'],
            ],
            'ana' => [
                'nombre' => 'Ana Lucia Murillo Arce',
                'email' => 'ana.murillo@wayna.bo',
                'telefono' => '77234501',
                'password' => 'admin123',
                'estado' => 'activo',
                'foto' => 'https://randomuser.me/api/portraits/women/71.jpg',
                'roles' => ['DONADOR', 'TURISTA'],
            ],
            'sergio' => [
                'nombre' => 'Sergio Aramayo Nina',
                'email' => 'sergio.aramayo@wayna.bo',
                'telefono' => '78904123',
                'password' => 'admin123',
                'estado' => 'activo',
                'foto' => 'https://randomuser.me/api/portraits/men/63.jpg',
                'roles' => ['COMPRADOR'],
            ],
            'valeria' => [
                'nombre' => 'Valeria Beltran Cossio',
                'email' => 'valeria.beltran@wayna.bo',
                'telefono' => '73127865',
                'password' => 'admin123',
                'estado' => 'activo',
                'foto' => 'https://randomuser.me/api/portraits/women/53.jpg',
                'roles' => ['COMPRADOR', 'DONADOR'],
            ],
            'turista_frances' => [
                'nombre' => 'Etienne Lambert',
                'email' => 'etienne.lambert@wayna.bo',
                'telefono' => '69001122',
                'password' => 'admin123',
                'estado' => 'activo',
                'foto' => 'https://randomuser.me/api/portraits/men/75.jpg',
                'roles' => ['TURISTA', 'COMPRADOR'],
            ],
            'sandra' => [
                'nombre' => 'Sandra Aguilar Montano',
                'email' => 'sandra.aguilar@wayna.bo',
                'telefono' => '79881231',
                'password' => 'admin123',
                'estado' => 'pendiente',
                'foto' => 'https://randomuser.me/api/portraits/women/32.jpg',
                'roles' => ['COMPRADOR'],
            ],
            'oscar' => [
                'nombre' => 'Oscar Padilla Condori',
                'email' => 'oscar.padilla@wayna.bo',
                'telefono' => '70667123',
                'password' => 'admin123',
                'estado' => 'activo',
                'foto' => 'https://randomuser.me/api/portraits/men/16.jpg',
                'roles' => ['COMPRADOR'],
            ],
        ];

        return collect($usuarios)->mapWithKeys(function (array $data, string $alias) use ($roles) {
            $usuario = User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'nombre_completo' => $data['nombre'],
                    'telefono' => $data['telefono'],
                    'password_hash' => Hash::make($data['password']),
                    'foto_perfil' => $data['foto'],
                    'estado' => $data['estado'],
                    'email_verified_at' => now(),
                ]
            );

            $usuario->roles()->sync(
                collect($data['roles'])->mapWithKeys(fn (string $rol) => [
                    $roles[$rol]->id => ['activo' => true, 'asignado_en' => now()],
                ])->all()
            );

            return [$alias => $usuario->fresh('roles')];
        });
    }

    private function seedEmprendedores(Collection $usuarios, Collection $categorias): Collection
    {
        $ahora = now();

        $emprendedores = [
            'tejidos_sol' => [
                'user_alias' => 'demo_emprendedor',
                'categoria' => 'textiles',
                'nombre_negocio' => 'Tejidos del Sol',
                'descripcion' => 'Taller textil liderado por mujeres artesanas que trabajan mantas, chales y caminos de mesa con iconografia del altiplano.',
                'historia' => 'Nacio como una iniciativa familiar en El Alto para transformar tecnicas heredadas de telar en una marca contemporanea y comercialmente sostenible.',
                'nit' => '1029384011',
                'estado' => 'activo',
                'foto_portada' => 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=1400&q=80',
                'aprobado_por_alias' => 'admin',
                'aprobado_en' => $ahora->copy()->subMonths(4),
            ],
            'barro_vivo' => [
                'user_alias' => 'mariela',
                'categoria' => 'ceramica',
                'nombre_negocio' => 'Barro Vivo de Copacabana',
                'descripcion' => 'Ceramica utilitaria con esmaltes sobrios y formas inspiradas en vajilla ceremonial del lago Titicaca.',
                'historia' => 'El taller combina torno manual, horneado local y una linea de producto pensada para hoteles boutique y hogares contemporaneos.',
                'nit' => '2027456018',
                'estado' => 'activo',
                'foto_portada' => 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&w=1400&q=80',
                'aprobado_por_alias' => 'admin',
                'aprobado_en' => $ahora->copy()->subMonths(3),
            ],
            'killa_plata' => [
                'user_alias' => 'ana',
                'categoria' => 'joyeria',
                'nombre_negocio' => 'Killa Plata Andina',
                'descripcion' => 'Joyeria artesanal en plata con piedras semipreciosas, pensada para colecciones pequenas y piezas de regalo.',
                'historia' => 'Su propuesta reinterpreta la luna, la chakana y los tejidos ceremoniales en una linea femenina y exportable.',
                'nit' => '3095512456',
                'estado' => 'activo',
                'foto_portada' => 'https://images.unsplash.com/photo-1617038220319-276d3cfab638?auto=format&fit=crop&w=1400&q=80',
                'aprobado_por_alias' => 'admin',
                'aprobado_en' => $ahora->copy()->subMonths(2),
            ],
            'cuero_puna' => [
                'user_alias' => 'oscar',
                'categoria' => 'cuero',
                'nombre_negocio' => 'Cuero de la Puna',
                'descripcion' => 'Bolsos, tarjeteros y accesorios de cuero con costura reforzada y acabados pensados para uso cotidiano.',
                'historia' => 'El emprendimiento trabaja con pequeños lotes y pone enfasis en resistencia, patronaje limpio y herrajes de larga vida.',
                'nit' => '4188927751',
                'estado' => 'activo',
                'foto_portada' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=1400&q=80',
                'aprobado_por_alias' => 'admin',
                'aprobado_en' => $ahora->copy()->subMonths(1),
            ],
            'maderas_valle' => [
                'user_alias' => 'valeria',
                'categoria' => 'madera',
                'nombre_negocio' => 'Maderas del Valle',
                'descripcion' => 'Piezas de mesa y decoracion hechas con maderas locales, lijado fino y tratamientos naturales.',
                'historia' => 'La marca nace del oficio carpintero familiar y de la demanda por objetos utilitarios con mejor presencia estetica.',
                'nit' => '5277819904',
                'estado' => 'activo',
                'foto_portada' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1400&q=80',
                'aprobado_por_alias' => 'admin',
                'aprobado_en' => $ahora->copy()->subWeeks(3),
            ],
            'warmi_hilos' => [
                'user_alias' => 'camila',
                'categoria' => 'textiles',
                'nombre_negocio' => 'Warmi Hilos',
                'descripcion' => 'Textiles contemporaneos con paletas suaves, lana de alpaca y piezas pensadas para turismo de diseno.',
                'historia' => 'El proyecto se estructuro para vender colecciones cortas y contar la historia de cada artesana detrás del telar.',
                'nit' => '6014567219',
                'estado' => 'pendiente',
                'foto_portada' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=1400&q=80',
                'aprobado_por_alias' => null,
                'aprobado_en' => null,
            ],
            'pacha_ceramica' => [
                'user_alias' => 'sandra',
                'categoria' => 'ceramica',
                'nombre_negocio' => 'Pacha Ceramica Viva',
                'descripcion' => 'Linea de cuencos, vasijas y luminarias de barro rojo con identidad de mercado cultural.',
                'historia' => 'Su enfoque combina tecnicas de modelado libre con acabados cálidos y piezas ideales para ambientacion.',
                'nit' => '7104928840',
                'estado' => 'pendiente',
                'foto_portada' => 'https://images.unsplash.com/photo-1517705008128-361805f42e86?auto=format&fit=crop&w=1400&q=80',
                'aprobado_por_alias' => null,
                'aprobado_en' => null,
            ],
            'llaqta_deco' => [
                'user_alias' => 'nicolas',
                'categoria' => 'decoracion',
                'nombre_negocio' => 'Llaqta Deco Ritual',
                'descripcion' => 'Decoracion cultural en fibras, madera y detalles simbolicos para ambientacion comercial y de hogar.',
                'historia' => 'El negocio se especializo en piezas escenograficas y decorativas para tiendas, hoteles y espacios culturales.',
                'nit' => '8891456723',
                'estado' => 'suspendido',
                'foto_portada' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1400&q=80',
                'aprobado_por_alias' => 'admin',
                'aprobado_en' => $ahora->copy()->subMonths(5),
            ],
        ];

        return collect($emprendedores)->mapWithKeys(function (array $data, string $alias) use ($usuarios, $categorias) {
            $usuario = $usuarios[$data['user_alias']];
            $aprobadoPor = $data['aprobado_por_alias'] ? $usuarios[$data['aprobado_por_alias']] : null;

            $usuario->update(['estado' => $data['estado'] === 'pendiente' ? 'activo' : $data['estado']]);

            $perfil = PerfilEmprendedor::query()->updateOrCreate(
                ['usuario_id' => $usuario->id],
                [
                    'categoria_id' => $categorias[$data['categoria']]->id,
                    'nombre_negocio' => $data['nombre_negocio'],
                    'descripcion' => $data['descripcion'],
                    'historia' => $data['historia'],
                    'foto_portada' => $data['foto_portada'],
                    'nit' => $data['nit'],
                    'estado' => $data['estado'],
                    'aprobado_por' => $aprobadoPor?->id,
                    'aprobado_en' => $data['aprobado_en'],
                ]
            );

            $usuario->asignarRol('EMPRENDEDOR');

            return [$alias => $perfil];
        });
    }

    private function seedProductos(Collection $emprendedores, Collection $categorias): Collection
    {
        $productos = [
            ['alias' => 'manta_solar', 'emprendedor' => 'tejidos_sol', 'categoria' => 'textiles', 'nombre' => 'Manta solar de alpaca', 'descripcion' => 'Manta tejida en telar con franjas suaves y acabado pensado para sala o dormitorio.', 'precio' => 348.00, 'stock' => 7, 'estado_stock' => 'disponible', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=1200&q=80'],
            ['alias' => 'camino_achachila', 'emprendedor' => 'tejidos_sol', 'categoria' => 'textiles', 'nombre' => 'Camino de mesa Achachila', 'descripcion' => 'Camino decorativo con patron geometrico inspirado en iconografia altiplanica.', 'precio' => 96.00, 'stock' => 14, 'estado_stock' => 'disponible', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=1200&q=80'],
            ['alias' => 'chal_nina', 'emprendedor' => 'tejidos_sol', 'categoria' => 'textiles', 'nombre' => 'Chal Nina de invierno', 'descripcion' => 'Chal liviano con mezcla de lana y diseño listo para regalo corporativo o venta boutique.', 'precio' => 189.00, 'stock' => 5, 'estado_stock' => 'ultimas_unidades', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=1200&q=80'],

            ['alias' => 'vajilla_titicaca', 'emprendedor' => 'barro_vivo', 'categoria' => 'ceramica', 'nombre' => 'Set de vajilla Titicaca', 'descripcion' => 'Juego de platos y cuencos esmaltados para mesa artesanal contemporanea.', 'precio' => 280.00, 'stock' => 6, 'estado_stock' => 'disponible', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&w=1200&q=80'],
            ['alias' => 'vasija_lago', 'emprendedor' => 'barro_vivo', 'categoria' => 'ceramica', 'nombre' => 'Vasija Lago Sagrado', 'descripcion' => 'Pieza decorativa de barro con curvas organicas y acabado mate.', 'precio' => 162.00, 'stock' => 3, 'estado_stock' => 'ultimas_unidades', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1517705008128-361805f42e86?auto=format&fit=crop&w=1200&q=80'],
            ['alias' => 'taza_katari', 'emprendedor' => 'barro_vivo', 'categoria' => 'ceramica', 'nombre' => 'Taza Katari', 'descripcion' => 'Taza de ceramica artesanal para cafe de especialidad y regalos de marca local.', 'precio' => 58.00, 'stock' => 15, 'estado_stock' => 'disponible', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=1200&q=80'],

            ['alias' => 'aretes_killa', 'emprendedor' => 'killa_plata', 'categoria' => 'joyeria', 'nombre' => 'Aretes Killa de plata', 'descripcion' => 'Aretes en plata con detalle martillado y piedra semipreciosa de tono profundo.', 'precio' => 134.00, 'stock' => 9, 'estado_stock' => 'disponible', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1617038220319-276d3cfab638?auto=format&fit=crop&w=1200&q=80'],
            ['alias' => 'collar_chakana', 'emprendedor' => 'killa_plata', 'categoria' => 'joyeria', 'nombre' => 'Collar Chakana fina', 'descripcion' => 'Collar de plata de linea delicada inspirado en la cruz andina.', 'precio' => 198.00, 'stock' => 4, 'estado_stock' => 'ultimas_unidades', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?auto=format&fit=crop&w=1200&q=80'],
            ['alias' => 'pulsera_inti', 'emprendedor' => 'killa_plata', 'categoria' => 'joyeria', 'nombre' => 'Pulsera Inti', 'descripcion' => 'Pulsera ajustable con acabado artesanal y empaque pensado para regalo premium.', 'precio' => 116.00, 'stock' => 11, 'estado_stock' => 'disponible', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?auto=format&fit=crop&w=1200&q=80'],

            ['alias' => 'bolso_puna', 'emprendedor' => 'cuero_puna', 'categoria' => 'cuero', 'nombre' => 'Bolso Puna urbano', 'descripcion' => 'Bolso de cuero de uso diario con costura reforzada y forro interior resistente.', 'precio' => 265.00, 'stock' => 8, 'estado_stock' => 'disponible', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=1200&q=80'],
            ['alias' => 'tarjetero_monte', 'emprendedor' => 'cuero_puna', 'categoria' => 'cuero', 'nombre' => 'Tarjetero Monte', 'descripcion' => 'Tarjetero minimalista para regalo institucional y ventas de impulso.', 'precio' => 72.00, 'stock' => 16, 'estado_stock' => 'disponible', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=1200&q=80'],
            ['alias' => 'cinturon_sur', 'emprendedor' => 'cuero_puna', 'categoria' => 'cuero', 'nombre' => 'Cinturon Sur', 'descripcion' => 'Cinturon artesanal con hebilla sobria y cuero de tono miel.', 'precio' => 118.00, 'stock' => 2, 'estado_stock' => 'agotado', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1594223274512-ad4803739b7c?auto=format&fit=crop&w=1200&q=80'],

            ['alias' => 'tabla_valle', 'emprendedor' => 'maderas_valle', 'categoria' => 'madera', 'nombre' => 'Tabla de servir Valle', 'descripcion' => 'Tabla de madera tratada para mesa, cocina boutique y regalos empresariales.', 'precio' => 124.00, 'stock' => 10, 'estado_stock' => 'disponible', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80'],
            ['alias' => 'porta_vela', 'emprendedor' => 'maderas_valle', 'categoria' => 'madera', 'nombre' => 'Porta vela de nogal', 'descripcion' => 'Pieza decorativa tallada a mano para ambientes cálidos.', 'precio' => 84.00, 'stock' => 9, 'estado_stock' => 'disponible', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1200&q=80'],
            ['alias' => 'organizador_killa', 'emprendedor' => 'maderas_valle', 'categoria' => 'madera', 'nombre' => 'Organizador Killa', 'descripcion' => 'Organizador de escritorio en madera pensado para hoteleria y oficinas creativas.', 'precio' => 98.00, 'stock' => 4, 'estado_stock' => 'ultimas_unidades', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=1200&q=80'],

            ['alias' => 'bufanda_warmi', 'emprendedor' => 'warmi_hilos', 'categoria' => 'textiles', 'nombre' => 'Bufanda Warmi suave', 'descripcion' => 'Bufanda de textura ligera y tonos neutros, ideal para turismo boutique.', 'precio' => 138.00, 'stock' => 13, 'estado_stock' => 'disponible', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=1200&q=80'],
            ['alias' => 'cojin_ayni', 'emprendedor' => 'warmi_hilos', 'categoria' => 'textiles', 'nombre' => 'Cojin Ayni', 'descripcion' => 'Funda de cojin tejida con patron lineal para hospedajes y tiendas de decoracion.', 'precio' => 92.00, 'stock' => 8, 'estado_stock' => 'disponible', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80'],
            ['alias' => 'tapiz_luna', 'emprendedor' => 'warmi_hilos', 'categoria' => 'textiles', 'nombre' => 'Tapiz Luna de hilo', 'descripcion' => 'Tapiz mural artesanal con lectura contemporanea y acabados finos.', 'precio' => 210.00, 'stock' => 3, 'estado_stock' => 'ultimas_unidades', 'activo' => false, 'imagen' => 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=1200&q=80'],

            ['alias' => 'cuenco_pacha', 'emprendedor' => 'pacha_ceramica', 'categoria' => 'ceramica', 'nombre' => 'Cuenco Pacha', 'descripcion' => 'Cuenco artesanal para mesa con acabado rustico y empaque listo para retail.', 'precio' => 64.00, 'stock' => 17, 'estado_stock' => 'disponible', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&w=1200&q=80'],
            ['alias' => 'florero_ritual', 'emprendedor' => 'pacha_ceramica', 'categoria' => 'ceramica', 'nombre' => 'Florero Ritual', 'descripcion' => 'Florero mediano de barro rojo para colecciones decorativas.', 'precio' => 156.00, 'stock' => 6, 'estado_stock' => 'disponible', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1517705008128-361805f42e86?auto=format&fit=crop&w=1200&q=80'],
            ['alias' => 'set_luminarias', 'emprendedor' => 'pacha_ceramica', 'categoria' => 'ceramica', 'nombre' => 'Set de luminarias de barro', 'descripcion' => 'Par de luminarias artesanales para ambientacion cultural y hotelera.', 'precio' => 220.00, 'stock' => 2, 'estado_stock' => 'ultimas_unidades', 'activo' => true, 'imagen' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1200&q=80'],

            ['alias' => 'centro_llaqta', 'emprendedor' => 'llaqta_deco', 'categoria' => 'decoracion', 'nombre' => 'Centro de mesa Llaqta', 'descripcion' => 'Centro de mesa con fibras y base de madera para ambientaciones culturales.', 'precio' => 132.00, 'stock' => 0, 'estado_stock' => 'agotado', 'activo' => false, 'imagen' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1200&q=80'],
            ['alias' => 'espejo_andino', 'emprendedor' => 'llaqta_deco', 'categoria' => 'decoracion', 'nombre' => 'Espejo Andino', 'descripcion' => 'Pieza decorativa con marco artesanal y enfoque para hoteleria boutique.', 'precio' => 245.00, 'stock' => 1, 'estado_stock' => 'agotado', 'activo' => false, 'imagen' => 'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=1200&q=80'],
            ['alias' => 'lampara_templo', 'emprendedor' => 'llaqta_deco', 'categoria' => 'decoracion', 'nombre' => 'Lampara Templo', 'descripcion' => 'Lampara de atmosfera calida con materiales mixtos y estilo escenografico.', 'precio' => 198.00, 'stock' => 2, 'estado_stock' => 'ultimas_unidades', 'activo' => false, 'imagen' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1200&q=80'],
        ];

        return collect($productos)->mapWithKeys(function (array $data) use ($emprendedores, $categorias) {
            $perfil = $emprendedores[$data['emprendedor']];
            $categoria = $categorias[$data['categoria']];
            $qrCodigo = 'PROD-'.Str::upper(Str::slug($data['alias'], '-'));

            $producto = Producto::query()->updateOrCreate(
                [
                    'emprendedor_id' => $perfil->id,
                    'nombre' => $data['nombre'],
                ],
                [
                    'categoria_id' => $categoria->id,
                    'descripcion' => $data['descripcion'],
                    'precio' => $data['precio'],
                    'stock' => $data['stock'],
                    'estado_stock' => $data['estado_stock'],
                    'foto_principal' => $data['imagen'],
                    'qr_codigo' => $qrCodigo,
                    'qr_url' => 'https://wayna.bo/producto/'.$data['alias'],
                    'activo' => $data['activo'],
                ]
            );

            ImagenProducto::query()->updateOrCreate(
                ['producto_id' => $producto->id, 'orden' => 1],
                ['url_foto' => $data['imagen']]
            );

            return [$data['alias'] => $producto];
        });
    }

    private function seedPedidos(Collection $usuarios, Collection $emprendedores, Collection $productos): Collection
    {
        $referenciaMes = now()->startOfMonth();

        $pedidos = [
            [
                'codigo' => 'PED-WAYNA-001',
                'usuario' => 'demo_usuario',
                'emprendedor' => 'tejidos_sol',
                'estado' => 'entregado',
                'metodo_entrega' => 'retiro_tienda',
                'notas' => 'Compra de regalo corporativo entregada sin observaciones.',
                'created_at' => $referenciaMes->copy()->addDays(2)->setTime(10, 30),
                'items' => [
                    ['producto' => 'manta_solar', 'cantidad' => 1],
                    ['producto' => 'camino_achachila', 'cantidad' => 2],
                ],
            ],
            [
                'codigo' => 'PED-WAYNA-002',
                'usuario' => 'mariela',
                'emprendedor' => 'barro_vivo',
                'estado' => 'confirmado',
                'metodo_entrega' => 'coordinado_emprendedor',
                'notas' => 'Cliente solicita embalaje reforzado para transporte.',
                'created_at' => $referenciaMes->copy()->addDays(5)->setTime(15, 20),
                'items' => [
                    ['producto' => 'vajilla_titicaca', 'cantidad' => 1],
                    ['producto' => 'taza_katari', 'cantidad' => 4],
                ],
            ],
            [
                'codigo' => 'PED-WAYNA-003',
                'usuario' => 'ruben',
                'emprendedor' => 'killa_plata',
                'estado' => 'pendiente',
                'metodo_entrega' => 'retiro_tienda',
                'notas' => 'Pendiente de confirmacion de pago QR.',
                'created_at' => $referenciaMes->copy()->addDays(7)->setTime(11, 45),
                'items' => [
                    ['producto' => 'collar_chakana', 'cantidad' => 1],
                ],
            ],
            [
                'codigo' => 'PED-WAYNA-004',
                'usuario' => 'paola',
                'emprendedor' => 'cuero_puna',
                'estado' => 'entregado',
                'metodo_entrega' => 'coordinado_emprendedor',
                'notas' => 'Entrega realizada en oficina del cliente.',
                'created_at' => $referenciaMes->copy()->subMonth()->addDays(12)->setTime(9, 15),
                'items' => [
                    ['producto' => 'bolso_puna', 'cantidad' => 1],
                    ['producto' => 'tarjetero_monte', 'cantidad' => 2],
                ],
            ],
            [
                'codigo' => 'PED-WAYNA-005',
                'usuario' => 'sergio',
                'emprendedor' => 'maderas_valle',
                'estado' => 'confirmado',
                'metodo_entrega' => 'retiro_tienda',
                'notas' => 'Cliente requiere factura a nombre de empresa.',
                'created_at' => $referenciaMes->copy()->subMonth()->addDays(18)->setTime(13, 50),
                'items' => [
                    ['producto' => 'tabla_valle', 'cantidad' => 2],
                    ['producto' => 'organizador_killa', 'cantidad' => 1],
                ],
            ],
            [
                'codigo' => 'PED-WAYNA-006',
                'usuario' => 'valeria',
                'emprendedor' => 'pacha_ceramica',
                'estado' => 'cancelado',
                'metodo_entrega' => 'coordinado_emprendedor',
                'notas' => 'Cliente cancelo por cambio de fecha del evento.',
                'created_at' => $referenciaMes->copy()->subMonths(2)->addDays(10)->setTime(17, 10),
                'items' => [
                    ['producto' => 'set_luminarias', 'cantidad' => 1],
                ],
            ],
            [
                'codigo' => 'PED-WAYNA-007',
                'usuario' => 'oscar',
                'emprendedor' => 'warmi_hilos',
                'estado' => 'confirmado',
                'metodo_entrega' => 'retiro_tienda',
                'notas' => 'Pedido de temporada alta para boutique en Sucre.',
                'created_at' => $referenciaMes->copy()->subMonths(2)->addDays(6)->setTime(14, 5),
                'items' => [
                    ['producto' => 'bufanda_warmi', 'cantidad' => 3],
                    ['producto' => 'cojin_ayni', 'cantidad' => 2],
                ],
            ],
            [
                'codigo' => 'PED-WAYNA-008',
                'usuario' => 'etienne.lambert@wayna.bo',
                'emprendedor' => 'tejidos_sol',
                'estado' => 'entregado',
                'metodo_entrega' => 'coordinado_emprendedor',
                'notas' => 'Compra internacional con retiro por operador turistico.',
                'created_at' => $referenciaMes->copy()->subMonths(3)->addDays(8)->setTime(16, 40),
                'items' => [
                    ['producto' => 'chal_nina', 'cantidad' => 2],
                ],
            ],
        ];

        return collect($pedidos)->mapWithKeys(function (array $data) use ($usuarios, $emprendedores, $productos) {
            $usuario = $this->resolverUsuarioSemilla($usuarios, $data['usuario']);
            $emprendedor = $emprendedores[$data['emprendedor']];

            $pedido = Pedido::query()->firstOrNew(['codigo' => $data['codigo']]);
            $pedido->fill([
                'usuario_id' => $usuario->id,
                'emprendedor_id' => $emprendedor->id,
                'estado' => $data['estado'],
                'metodo_entrega' => $data['metodo_entrega'],
                'notas' => $data['notas'],
                'total' => 0,
            ]);
            $pedido->save();

            PedidoItem::query()->where('pedido_id', $pedido->id)->delete();

            $total = 0;

            foreach ($data['items'] as $item) {
                $producto = $productos[$item['producto']];
                $subtotal = round((float) $producto->precio * $item['cantidad'], 2);

                PedidoItem::query()->create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            DB::table('pedidos')->where('id', $pedido->id)->update([
                'total' => $total,
                'created_at' => $data['created_at'],
                'updated_at' => $data['created_at'],
            ]);

            return [$data['codigo'] => $pedido->fresh('items')];
        });
    }

    private function seedReservas(Collection $usuarios, Collection $emprendedores, Collection $productos): Collection
    {
        $reservas = [
            [
                'codigo' => 'RSV-WAYNA-001',
                'usuario' => 'camila',
                'emprendedor' => 'barro_vivo',
                'tipo' => 'producto',
                'producto' => 'vajilla_titicaca',
                'fecha_reserva' => now()->addDays(3)->toDateString(),
                'hora_reserva' => '10:00:00',
                'estado' => 'confirmada',
                'qr_acceso' => 'QR-RSV-001',
                'notas' => 'Recojo programado en feria artesanal del fin de semana.',
                'created_at' => now()->subDays(4),
            ],
            [
                'codigo' => 'RSV-WAYNA-002',
                'usuario' => 'sergio',
                'emprendedor' => 'maderas_valle',
                'tipo' => 'producto',
                'producto' => 'tabla_valle',
                'fecha_reserva' => now()->addDays(5)->toDateString(),
                'hora_reserva' => '15:30:00',
                'estado' => 'pendiente',
                'qr_acceso' => null,
                'notas' => 'Cliente esta coordinando entrega parcial.',
                'created_at' => now()->subDays(2),
            ],
            [
                'codigo' => 'RSV-WAYNA-003',
                'usuario' => 'demo_usuario',
                'emprendedor' => 'tejidos_sol',
                'tipo' => 'taller',
                'producto' => null,
                'fecha_reserva' => now()->addDays(10)->toDateString(),
                'hora_reserva' => '17:00:00',
                'estado' => 'pendiente',
                'qr_acceso' => null,
                'notas' => 'Reserva grupal para experiencia de telar guiado.',
                'created_at' => now()->subDay(),
            ],
            [
                'codigo' => 'RSV-WAYNA-004',
                'usuario' => 'ruben',
                'emprendedor' => 'killa_plata',
                'tipo' => 'experiencia_cultural',
                'producto' => null,
                'fecha_reserva' => now()->subDays(8)->toDateString(),
                'hora_reserva' => '11:00:00',
                'estado' => 'reprogramada',
                'qr_acceso' => 'QR-RSV-004',
                'notas' => 'Experiencia reprogramada por cambios climaticos.',
                'created_at' => now()->subDays(15),
            ],
            [
                'codigo' => 'RSV-WAYNA-005',
                'usuario' => 'paola',
                'emprendedor' => 'pacha_ceramica',
                'tipo' => 'producto',
                'producto' => 'florero_ritual',
                'fecha_reserva' => now()->addDays(7)->toDateString(),
                'hora_reserva' => '12:15:00',
                'estado' => 'confirmada',
                'qr_acceso' => 'QR-RSV-005',
                'notas' => 'Entrega junto a ambientacion de evento pequeño.',
                'created_at' => now()->subDays(3),
            ],
        ];

        return collect($reservas)->mapWithKeys(function (array $data) use ($usuarios, $emprendedores, $productos) {
            $usuario = $this->resolverUsuarioSemilla($usuarios, $data['usuario']);
            $reserva = Reserva::query()->updateOrCreate(
                ['codigo' => $data['codigo']],
                [
                    'usuario_id' => $usuario->id,
                    'emprendedor_id' => $emprendedores[$data['emprendedor']]->id,
                    'tipo' => $data['tipo'],
                    'producto_id' => $data['producto'] ? $productos[$data['producto']]->id : null,
                    'fecha_reserva' => $data['fecha_reserva'],
                    'hora_reserva' => $data['hora_reserva'],
                    'estado' => $data['estado'],
                    'qr_acceso' => $data['qr_acceso'],
                    'notas' => $data['notas'],
                    'created_at' => $data['created_at'],
                ]
            );

            return [$data['codigo'] => $reserva];
        });
    }

    private function seedDonaciones(Collection $usuarios, Collection $emprendedores): Collection
    {
        $donaciones = [
            [
                'alias' => 'donacion_001',
                'usuario' => 'mariela',
                'emprendedor' => 'tejidos_sol',
                'monto' => 90.00,
                'es_anonima' => false,
                'mensaje' => 'Gracias por mantener tecnicas vivas y mostrarlas con tanto cuidado.',
                'certificado_url' => 'https://example.com/certificados/seed-donacion-001.pdf',
                'created_at' => now()->startOfMonth()->addDays(1)->setTime(9, 20),
            ],
            [
                'alias' => 'donacion_002',
                'usuario' => 'paola',
                'emprendedor' => 'barro_vivo',
                'monto' => 150.00,
                'es_anonima' => false,
                'mensaje' => 'Aporte para fortalecer la produccion de la nueva coleccion.',
                'certificado_url' => 'https://example.com/certificados/seed-donacion-002.pdf',
                'created_at' => now()->startOfMonth()->addDays(4)->setTime(13, 10),
            ],
            [
                'alias' => 'donacion_003',
                'usuario' => 'ana',
                'emprendedor' => null,
                'monto' => 60.00,
                'es_anonima' => true,
                'mensaje' => 'Para el fondo general de capacitacion y visibilidad.',
                'certificado_url' => 'https://example.com/certificados/seed-donacion-003.pdf',
                'created_at' => now()->startOfMonth()->subMonth()->addDays(7)->setTime(18, 45),
            ],
            [
                'alias' => 'donacion_004',
                'usuario' => 'valeria',
                'emprendedor' => 'killa_plata',
                'monto' => 120.00,
                'es_anonima' => false,
                'mensaje' => 'Me encanta ver productos con identidad tan clara y cuidada.',
                'certificado_url' => 'https://example.com/certificados/seed-donacion-004.pdf',
                'created_at' => now()->startOfMonth()->subMonth()->addDays(12)->setTime(11, 5),
            ],
            [
                'alias' => 'donacion_005',
                'usuario' => 'demo_usuario',
                'emprendedor' => 'maderas_valle',
                'monto' => 80.00,
                'es_anonima' => false,
                'mensaje' => 'Aporte para mantener la produccion de piezas pequeñas.',
                'certificado_url' => 'https://example.com/certificados/seed-donacion-005.pdf',
                'created_at' => now()->startOfMonth()->subMonths(2)->addDays(9)->setTime(14, 30),
            ],
            [
                'alias' => 'donacion_006',
                'usuario' => 'mariela',
                'emprendedor' => 'pacha_ceramica',
                'monto' => 95.00,
                'es_anonima' => false,
                'mensaje' => 'Que la nueva linea de ceramica siga creciendo con buena vitrina.',
                'certificado_url' => 'https://example.com/certificados/seed-donacion-006.pdf',
                'created_at' => now()->startOfMonth()->subMonths(2)->addDays(14)->setTime(16, 40),
            ],
        ];

        return collect($donaciones)->mapWithKeys(function (array $data) use ($usuarios, $emprendedores) {
            $usuario = $this->resolverUsuarioSemilla($usuarios, $data['usuario']);
            $donacion = Donacion::query()->updateOrCreate(
                ['certificado_url' => $data['certificado_url']],
                [
                    'usuario_id' => $usuario->id,
                    'emprendedor_id' => $data['emprendedor'] ? $emprendedores[$data['emprendedor']]->id : null,
                    'monto' => $data['monto'],
                    'es_anonima' => $data['es_anonima'],
                    'mensaje' => $data['mensaje'],
                    'created_at' => $data['created_at'],
                ]
            );

            return [$data['alias'] => $donacion];
        });
    }

    private function seedTransacciones(Collection $pedidos, Collection $reservas, Collection $donaciones): void
    {
        foreach ($pedidos as $pedido) {
            $estado = match ($pedido->estado) {
                'cancelado' => 'fallida',
                'pendiente' => 'pendiente',
                default => 'completada',
            };

            Transaccion::query()->updateOrCreate(
                [
                    'referencia_id' => $pedido->id,
                    'referencia_tipo' => 'pedido',
                ],
                [
                    'usuario_id' => $pedido->usuario_id,
                    'metodo_pago' => collect(['qr', 'nfc', 'microtransaccion'])->random(),
                    'monto' => $pedido->total,
                    'estado' => $estado,
                    'codigo_qr' => 'TX-PED-'.$pedido->id,
                    'respuesta_pasarela' => ['origen' => 'seed', 'pedido' => $pedido->codigo],
                    'procesado_en' => $estado === 'completada' ? Carbon::parse($pedido->created_at)->addHours(2) : null,
                    'created_at' => $pedido->created_at,
                ]
            );
        }

        foreach ($donaciones as $donacion) {
            Transaccion::query()->updateOrCreate(
                [
                    'referencia_id' => $donacion->id,
                    'referencia_tipo' => 'donacion',
                ],
                [
                    'usuario_id' => $donacion->usuario_id,
                    'metodo_pago' => 'qr',
                    'monto' => $donacion->monto,
                    'estado' => 'completada',
                    'codigo_qr' => 'TX-DON-'.$donacion->id,
                    'respuesta_pasarela' => ['origen' => 'seed', 'donacion' => $donacion->id],
                    'procesado_en' => Carbon::parse($donacion->created_at)->addMinutes(35),
                    'created_at' => $donacion->created_at,
                ]
            );
        }

        foreach ($reservas as $reserva) {
            $estado = in_array($reserva->estado, ['confirmada', 'reprogramada'], true) ? 'completada' : ($reserva->estado === 'pendiente' ? 'pendiente' : 'fallida');

            Transaccion::query()->updateOrCreate(
                [
                    'referencia_id' => $reserva->id,
                    'referencia_tipo' => 'reserva',
                ],
                [
                    'usuario_id' => $reserva->usuario_id,
                    'metodo_pago' => 'microtransaccion',
                    'monto' => $reserva->producto?->precio ?? 45,
                    'estado' => $estado,
                    'codigo_qr' => 'TX-RSV-'.$reserva->id,
                    'respuesta_pasarela' => ['origen' => 'seed', 'reserva' => $reserva->codigo],
                    'procesado_en' => $estado === 'completada' ? Carbon::parse($reserva->created_at)->addMinutes(25) : null,
                    'created_at' => $reserva->created_at,
                ]
            );
        }
    }

    private function seedPuntosDonador(Collection $usuarios, Collection $donaciones): void
    {
        $donacionesPorUsuario = $donaciones->groupBy('usuario_id');

        foreach ($usuarios as $usuario) {
            $lista = $donacionesPorUsuario->get($usuario->id, collect());

            if ($lista->isEmpty()) {
                continue;
            }

            $puntos = 0;

            foreach ($lista as $index => $donacion) {
                $multiplicador = $index === 0 ? 1.5 : 1.0;
                $ganados = (int) round(((float) $donacion->monto / 10) * $multiplicador);

                HistorialPuntos::query()->updateOrCreate(
                    [
                        'usuario_id' => $usuario->id,
                        'donacion_id' => $donacion->id,
                    ],
                    [
                        'puntos_ganados' => $ganados,
                        'multiplicador' => $multiplicador,
                        'created_at' => $donacion->created_at,
                    ]
                );

                $puntos += $ganados;
            }

            $nivel = $puntos >= 60 ? 'oro' : ($puntos >= 25 ? 'plata' : 'bronce');

            PuntosDonador::query()->updateOrCreate(
                ['usuario_id' => $usuario->id],
                [
                    'puntos_total' => $puntos,
                    'nivel' => $nivel,
                    'updated_at' => now(),
                ]
            );
        }
    }

    private function resolverUsuarioSemilla(Collection $usuarios, string $aliasOEmail): User
    {
        if ($usuarios->has($aliasOEmail)) {
            return $usuarios[$aliasOEmail];
        }

        return $usuarios->firstWhere('email', $aliasOEmail);
    }
}
