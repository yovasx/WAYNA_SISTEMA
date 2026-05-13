<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\PerfilEmprendedor;
use App\Models\Producto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = collect(['ADMINISTRADOR', 'EMPRENDEDOR', 'COMPRADOR', 'DONADOR', 'TURISTA'])
            ->mapWithKeys(fn (string $nombre) => [$nombre => Role::query()->firstOrCreate(['nombre' => $nombre])]);

        $admin = User::updateOrCreate([
            'email' => 'admin@admin.gmail.com',
        ], [
            'nombre_completo' => 'Administrador WAYNA',
            'password_hash' => Hash::make('admin123'),
            'estado' => 'activo',
            'email_verified_at' => now(),
        ]);
        $admin->roles()->sync([$roles['ADMINISTRADOR']->id => ['activo' => true, 'asignado_en' => now()]]);

        $emprendedor = User::updateOrCreate([
            'email' => 'emprendedor@wayna.bo',
        ], [
            'nombre_completo' => 'Tejidos del Sol',
            'password_hash' => Hash::make('admin123'),
            'estado' => 'activo',
            'email_verified_at' => now(),
        ]);
        $emprendedor->roles()->sync([$roles['EMPRENDEDOR']->id => ['activo' => true, 'asignado_en' => now()]]);

        $comprador = User::updateOrCreate([
            'email' => 'usuario@wayna.bo',
        ], [
            'nombre_completo' => 'Usuario Demo',
            'password_hash' => Hash::make('admin123'),
            'estado' => 'activo',
            'email_verified_at' => now(),
        ]);
        $comprador->roles()->sync([$roles['COMPRADOR']->id => ['activo' => true, 'asignado_en' => now()]]);

        $textiles = Categoria::updateOrCreate([
            'nombre' => 'Textiles',
        ], [
            'icono' => 'checkroom',
            'descripcion' => 'Piezas tejidas a mano con identidad andina.',
        ]);

        $ceramica = Categoria::updateOrCreate([
            'nombre' => 'Ceramica',
        ], [
            'icono' => 'emoji_food_beverage',
            'descripcion' => 'Ceramica artesanal para mesa y decoracion.',
        ]);

        $perfil = PerfilEmprendedor::updateOrCreate([
            'usuario_id' => $emprendedor->id,
        ], [
            'categoria_id' => $textiles->id,
            'nombre_negocio' => 'Tejidos del Sol',
            'descripcion' => 'Marca demo para validar el panel emprendedor y el catalogo inicial.',
            'historia' => 'Marca demo para validar el panel emprendedor y el catalogo inicial.',
            'estado' => 'activo',
            'aprobado_por' => $admin->id,
            'aprobado_en' => now(),
        ]);

        Producto::updateOrCreate([
            'nombre' => 'Manta Aymara Edicion Taller',
        ], [
            'emprendedor_id' => $perfil->id,
            'categoria_id' => $textiles->id,
            'descripcion' => 'Manta artesanal tejida en telar con acabados tradicionales.',
            'precio' => 320.00,
            'stock' => 6,
            'estado_stock' => 'disponible',
            'activo' => true,
        ]);

        Producto::updateOrCreate([
            'nombre' => 'Vasija Terracota Patrimonial',
        ], [
            'emprendedor_id' => $perfil->id,
            'categoria_id' => $ceramica->id,
            'descripcion' => 'Pieza de ceramica utilitaria con acabado mate y sello local.',
            'precio' => 145.00,
            'stock' => 3,
            'estado_stock' => 'ultimas_unidades',
            'activo' => true,
        ]);

        Producto::updateOrCreate([
            'nombre' => 'Camino de Mesa Llama',
        ], [
            'emprendedor_id' => $perfil->id,
            'categoria_id' => $textiles->id,
            'descripcion' => 'Producto de muestra para pruebas del panel administrativo.',
            'precio' => 89.50,
            'stock' => 12,
            'estado_stock' => 'disponible',
            'activo' => true,
        ]);
    }
}
