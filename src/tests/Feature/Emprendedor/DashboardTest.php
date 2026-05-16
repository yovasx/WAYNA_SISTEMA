<?php

namespace Tests\Feature\Emprendedor;

use App\Models\Categoria;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_operational_sections_without_business_checklist(): void
    {
        $emprendedor = User::factory()->emprendedor()->create();
        $cliente = User::factory()->comprador()->create();
        $categoria = Categoria::query()->create([
            'nombre' => 'Textiles',
            'icono' => 'checkroom',
            'descripcion' => 'Textiles artesanales',
        ]);

        $producto = Producto::query()->create([
            'emprendedor_id' => $emprendedor->perfilEmprendedor->id,
            'categoria_id' => $categoria->id,
            'nombre' => 'Manta premium',
            'precio' => 220,
            'stock' => 3,
            'estado_stock' => 'ultimas_unidades',
            'activo' => true,
        ]);

        $pedido = Pedido::query()->create([
            'codigo' => 'PED-DASH-001',
            'usuario_id' => $cliente->id,
            'emprendedor_id' => $emprendedor->perfilEmprendedor->id,
            'estado' => 'confirmado',
            'metodo_entrega' => 'retiro_tienda',
            'total' => 220,
        ]);

        PedidoItem::query()->create([
            'pedido_id' => $pedido->id,
            'producto_id' => $producto->id,
            'cantidad' => 1,
            'precio_unitario' => 220,
            'subtotal' => 220,
        ]);

        $this->actingAs($emprendedor)
            ->get(route('dashboard.emprendedor'))
            ->assertOk()
            ->assertSee('Tendencia de ventas')
            ->assertSee('Pedidos por estado')
            ->assertSee('Top productos vendidos')
            ->assertSee('Salud del inventario')
            ->assertDontSee('Checklist del negocio');
    }
}
