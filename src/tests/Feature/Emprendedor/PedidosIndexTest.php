<?php

namespace Tests\Feature\Emprendedor;

use App\Livewire\Emprendedor\PedidosIndex;
use App\Models\Categoria;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PedidosIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_entrepreneur_only_sees_their_own_orders(): void
    {
        $emprendedor = User::factory()->emprendedor()->create();
        $otroEmprendedor = User::factory()->emprendedor()->create();
        $cliente = User::factory()->comprador()->create();
        $categoria = Categoria::query()->create([
            'nombre' => 'Textiles',
            'icono' => 'checkroom',
            'descripcion' => 'Textiles artesanales',
        ]);

        $productoPropio = Producto::query()->create([
            'emprendedor_id' => $emprendedor->perfilEmprendedor->id,
            'categoria_id' => $categoria->id,
            'nombre' => 'Manta propia',
            'precio' => 120,
            'stock' => 8,
            'estado_stock' => 'disponible',
            'activo' => true,
        ]);

        $productoAjeno = Producto::query()->create([
            'emprendedor_id' => $otroEmprendedor->perfilEmprendedor->id,
            'categoria_id' => $categoria->id,
            'nombre' => 'Manta ajena',
            'precio' => 150,
            'stock' => 4,
            'estado_stock' => 'disponible',
            'activo' => true,
        ]);

        $pedidoPropio = Pedido::query()->create([
            'codigo' => 'PED-OWN-001',
            'usuario_id' => $cliente->id,
            'emprendedor_id' => $emprendedor->perfilEmprendedor->id,
            'estado' => 'pendiente',
            'metodo_entrega' => 'retiro_tienda',
            'total' => 120,
        ]);

        PedidoItem::query()->create([
            'pedido_id' => $pedidoPropio->id,
            'producto_id' => $productoPropio->id,
            'cantidad' => 1,
            'precio_unitario' => 120,
            'subtotal' => 120,
        ]);

        $pedidoAjeno = Pedido::query()->create([
            'codigo' => 'PED-OTHER-001',
            'usuario_id' => $cliente->id,
            'emprendedor_id' => $otroEmprendedor->perfilEmprendedor->id,
            'estado' => 'pendiente',
            'metodo_entrega' => 'retiro_tienda',
            'total' => 150,
        ]);

        PedidoItem::query()->create([
            'pedido_id' => $pedidoAjeno->id,
            'producto_id' => $productoAjeno->id,
            'cantidad' => 1,
            'precio_unitario' => 150,
            'subtotal' => 150,
        ]);

        $this->actingAs($emprendedor);

        Livewire::test(PedidosIndex::class)
            ->assertSee('PED-OWN-001')
            ->assertDontSee('PED-OTHER-001');
    }

    public function test_entrepreneur_can_confirm_and_deliver_own_order(): void
    {
        $emprendedor = User::factory()->emprendedor()->create();
        $cliente = User::factory()->comprador()->create();

        $pedido = Pedido::query()->create([
            'codigo' => 'PED-STATE-001',
            'usuario_id' => $cliente->id,
            'emprendedor_id' => $emprendedor->perfilEmprendedor->id,
            'estado' => 'pendiente',
            'metodo_entrega' => 'retiro_tienda',
            'total' => 80,
        ]);

        $this->actingAs($emprendedor);

        Livewire::test(PedidosIndex::class)
            ->call('confirmarPedido', $pedido->id)
            ->assertHasNoErrors();

        $this->assertSame('confirmado', $pedido->fresh()->estado);

        Livewire::test(PedidosIndex::class)
            ->call('marcarEntregado', $pedido->id)
            ->assertHasNoErrors();

        $this->assertSame('entregado', $pedido->fresh()->estado);
    }
}
