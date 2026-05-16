<?php

namespace Tests\Feature\Emprendedor;

use App\Livewire\Emprendedor\PerfilIndex;
use App\Models\PerfilEmprendedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PerfilIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_location_can_be_saved_without_city(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(PerfilIndex::class)
            ->set('nombreNegocio', 'Taller Andino')
            ->set('tieneLocalFisico', true)
            ->set('direccionCalle', 'Av. 16 de Julio')
            ->set('direccionNumero', '1234')
            ->set('latitud', '-16.4897000')
            ->set('longitud', '-68.1193000')
            ->call('guardar')
            ->assertHasNoErrors();

        $perfil = PerfilEmprendedor::query()->where('usuario_id', $user->id)->firstOrFail();

        $this->assertTrue($perfil->tiene_local_fisico);
        $this->assertSame('Av. 16 de Julio', $perfil->direccion_calle);
        $this->assertSame('1234', $perfil->direccion_numero);
        $this->assertNull($perfil->ciudad);
        $this->assertSame('-16.4897000', $perfil->latitud);
        $this->assertSame('-68.1193000', $perfil->longitud);
    }

    public function test_profile_location_requires_complete_coordinate_pair(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(PerfilIndex::class)
            ->set('nombreNegocio', 'Taller Andino')
            ->set('latitud', '-16.4897000')
            ->call('guardar')
            ->assertHasErrors(['longitud']);
    }

    public function test_business_account_page_displays_commercial_checklist(): void
    {
        $user = User::factory()->emprendedor()->create();

        $this->actingAs($user)
            ->get(route('emprendedor.perfil.index'))
            ->assertOk()
            ->assertSee('Cuenta empresaria')
            ->assertSee('Checklist comercial')
            ->assertSee('Primer producto publicado');
    }
}
