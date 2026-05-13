<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\PerfilEmprendedor;
use App\Models\Producto;
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
        $admin = User::updateOrCreate([
            'email' => 'admin@admin.gmail.com',
        ], [
            'name' => 'Administrador WAYNA',
            'password' => Hash::make('admin123'),
            'rol' => 'administrador',
            'estado' => 'activo',
            'email_verified_at' => now(),
        ]);

        $emprendedor = User::updateOrCreate([
            'email' => 'emprendedor@wayna.bo',
        ], [
            'name' => 'Tejidos del Sol',
            'password' => Hash::make('admin123'),
            'rol' => 'emprendedor',
            'estado' => 'activo',
            'email_verified_at' => now(),
        ]);

        User::updateOrCreate([
            'email' => 'usuario@wayna.bo',
        ], [
            'name' => 'Usuario Demo',
            'password' => Hash::make('admin123'),
            'rol' => 'comprador',
            'estado' => 'activo',
            'email_verified_at' => now(),
        ]);

        $textiles = Categoria::updateOrCreate([
            'nombre' => 'Textiles',
        ], [
            'slug' => 'textiles',
            'descripcion' => 'Piezas tejidas a mano con identidad andina.',
            'activa' => true,
        ]);

        $ceramica = Categoria::updateOrCreate([
            'nombre' => 'Ceramica',
        ], [
            'slug' => 'ceramica',
            'descripcion' => 'Ceramica artesanal para mesa y decoracion.',
            'activa' => true,
        ]);

        $perfil = PerfilEmprendedor::updateOrCreate([
            'usuario_id' => $emprendedor->id,
        ], [
            'categoria_id' => $textiles->id,
            'nombre_emprendimiento' => 'Tejidos del Sol',
            'historia' => 'Marca demo para validar el panel emprendedor y el catalogo inicial.',
            'ciudad' => 'La Paz',
            'pais' => 'Bolivia',
            'estado_aprobacion' => 'aprobado',
            'acepta_donaciones' => true,
        ]);

        Producto::updateOrCreate([
            'slug' => 'manta-aymara-edicion-taller',
        ], [
            'perfil_emprendedor_id' => $perfil->id,
            'categoria_id' => $textiles->id,
            'nombre' => 'Manta Aymara Edicion Taller',
            'descripcion' => 'Manta artesanal tejida en telar con acabados tradicionales.',
            'precio' => 320.00,
            'stock' => 6,
            'estado_disponibilidad' => 'disponible',
            'destacado' => true,
            'publicado_at' => now(),
        ]);

        Producto::updateOrCreate([
            'slug' => 'vasija-terracota-patrimonial',
        ], [
            'perfil_emprendedor_id' => $perfil->id,
            'categoria_id' => $ceramica->id,
            'nombre' => 'Vasija Terracota Patrimonial',
            'descripcion' => 'Pieza de ceramica utilitaria con acabado mate y sello local.',
            'precio' => 145.00,
            'stock' => 3,
            'estado_disponibilidad' => 'ultimas_unidades',
            'publicado_at' => now(),
        ]);

        Producto::updateOrCreate([
            'slug' => 'camino-de-mesa-llama',
        ], [
            'perfil_emprendedor_id' => $perfil->id,
            'categoria_id' => $textiles->id,
            'nombre' => 'Camino de Mesa Llama',
            'descripcion' => 'Producto de muestra para pruebas del panel administrativo.',
            'precio' => 89.50,
            'stock' => 12,
            'estado_disponibilidad' => 'disponible',
            'publicado_at' => now(),
        ]);
    }
}
