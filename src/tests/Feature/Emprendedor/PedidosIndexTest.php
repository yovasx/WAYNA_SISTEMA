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
        $categoria = Categoria::query()->create([
            'nombre' => 'Ceramica',
            'icono' => 'local_cafe',
            'descripcion' => 'Piezas ceramicas',
        ]);
        $producto = Producto::query()->create([
            'emprendedor_id' => $emprendedor->perfilEmprendedor->id,
            'categoria_id' => $categoria->id,
            'nombre' => 'Taza artesanal',
            'precio' => 80,
            'stock' => 6,
            'estado_stock' => 'disponible',
            'activo' => true,
        ]);

        $pedido = Pedido::query()->create([
            'codigo' => 'PED-STATE-001',
            'usuario_id' => $cliente->id,
            'emprendedor_id' => $emprendedor->perfilEmprendedor->id,
            'estado' => 'pendiente',
            'metodo_entrega' => 'retiro_tienda',
            'total' => 80,
        ]);

        PedidoItem::query()->create([
            'pedido_id' => $pedido->id,
            'producto_id' => $producto->id,
            'cantidad' => 1,
            'precio_unitario' => 80,
            'subtotal' => 80,
        ]);

        $this->actingAs($emprendedor);

        Livewire::test(PedidosIndex::class)
            ->call('confirmarPedido', $pedido->id)
            ->assertHasNoErrors();

        $this->assertSame('confirmado', $pedido->fresh()->estado);
        $this->assertSame(5, $producto->fresh()->stock);
        $this->assertSame('ultimas_unidades', $producto->fresh()->estado_disponibilidad);

        Livewire::test(PedidosIndex::class)
            ->call('marcarEntregado', $pedido->id)
            ->assertHasNoErrors();

        $this->assertSame('entregado', $pedido->fresh()->estado);
        $this->assertSame(5, $producto->fresh()->stock);
    }

    public function test_canceling_a_confirmed_order_restores_inventory(): void
    {
        $emprendedor = User::factory()->emprendedor()->create();
        $cliente = User::factory()->comprador()->create();
        $categoria = Categoria::query()->create([
            'nombre' => 'Joyeria',
            'icono' => 'diamond',
            'descripcion' => 'Joyeria artesanal',
        ]);
        $producto = Producto::query()->create([
            'emprendedor_id' => $emprendedor->perfilEmprendedor->id,
            'categoria_id' => $categoria->id,
            'nombre' => 'Collar de plata',
            'precio' => 140,
            'stock' => 3,
            'estado_stock' => 'ultimas_unidades',
            'activo' => true,
        ]);

        $pedido = Pedido::query()->create([
            'codigo' => 'PED-STATE-002',
            'usuario_id' => $cliente->id,
            'emprendedor_id' => $emprendedor->perfilEmprendedor->id,
            'estado' => 'pendiente',
            'metodo_entrega' => 'retiro_tienda',
            'total' => 280,
        ]);

        PedidoItem::query()->create([
            'pedido_id' => $pedido->id,
            'producto_id' => $producto->id,
            'cantidad' => 2,
            'precio_unitario' => 140,
            'subtotal' => 280,
        ]);

        $this->actingAs($emprendedor);

        Livewire::test(PedidosIndex::class)
            ->call('confirmarPedido', $pedido->id)
            ->assertHasNoErrors();

        $this->assertSame(1, $producto->fresh()->stock);
        $this->assertSame('ultimas_unidades', $producto->fresh()->estado_disponibilidad);

        Livewire::test(PedidosIndex::class)
            ->call('cancelarPedido', $pedido->id)
            ->assertHasNoErrors();

        $this->assertSame('cancelado', $pedido->fresh()->estado);
        $this->assertSame(3, $producto->fresh()->stock);
        $this->assertSame('ultimas_unidades', $producto->fresh()->estado_disponibilidad);
    }
}
