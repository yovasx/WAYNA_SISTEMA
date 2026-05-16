<?php

namespace Tests\Feature\Emprendedor;

use App\Models\Categoria;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GestionCatalogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_view_shows_only_own_sales_metrics(): void
    {
        $emprendedor = User::factory()->emprendedor()->create();
        $otroEmprendedor = User::factory()->emprendedor()->create();
        $cliente = User::factory()->comprador()->create();
        $categoria = Categoria::query()->create([
            'nombre' => 'Textiles',
            'icono' => 'checkroom',
            'descripcion' => 'Textiles artesanales',
        ]);

        $productoVendido = Producto::query()->create([
            'emprendedor_id' => $emprendedor->perfilEmprendedor->id,
            'categoria_id' => $categoria->id,
            'nombre' => 'Manta premium',
            'precio' => 120,
            'stock' => 4,
            'estado_stock' => 'ultimas_unidades',
            'activo' => true,
        ]);

        $productoSinVentas = Producto::query()->create([
            'emprendedor_id' => $emprendedor->perfilEmprendedor->id,
            'categoria_id' => $categoria->id,
            'nombre' => 'Bufanda nueva',
            'precio' => 80,
            'stock' => 7,
            'estado_stock' => 'disponible',
            'activo' => true,
        ]);

        $productoAjeno = Producto::query()->create([
            'emprendedor_id' => $otroEmprendedor->perfilEmprendedor->id,
            'categoria_id' => $categoria->id,
            'nombre' => 'Poncho ajeno',
            'precio' => 200,
            'stock' => 3,
            'estado_stock' => 'disponible',
            'activo' => true,
        ]);

        $pedidoPropio = Pedido::query()->create([
            'codigo' => 'PED-CAT-001',
            'usuario_id' => $cliente->id,
            'emprendedor_id' => $emprendedor->perfilEmprendedor->id,
            'estado' => 'confirmado',
            'metodo_entrega' => 'retiro_tienda',
            'total' => 240,
        ]);

        PedidoItem::query()->create([
            'pedido_id' => $pedidoPropio->id,
            'producto_id' => $productoVendido->id,
            'cantidad' => 2,
            'precio_unitario' => 120,
            'subtotal' => 240,
        ]);

        $pedidoAjeno = Pedido::query()->create([
            'codigo' => 'PED-CAT-002',
            'usuario_id' => $cliente->id,
            'emprendedor_id' => $otroEmprendedor->perfilEmprendedor->id,
            'estado' => 'confirmado',
            'metodo_entrega' => 'retiro_tienda',
            'total' => 200,
        ]);

        PedidoItem::query()->create([
            'pedido_id' => $pedidoAjeno->id,
            'producto_id' => $productoAjeno->id,
            'cantidad' => 1,
            'precio_unitario' => 200,
            'subtotal' => 200,
        ]);

        $this->actingAs($emprendedor)
            ->get(route('emprendedor.productos.index'))
            ->assertOk()
            ->assertSee('Ventas acumuladas')
            ->assertSee('Bs 240.00 en ventas')
            ->assertSee('2 und. vendidas')
            ->assertSee('Aun sin ventas')
            ->assertDontSee('Poncho ajeno');
    }
}
