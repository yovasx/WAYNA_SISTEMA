<?php

namespace Tests\Feature\Emprendedor;

use App\Livewire\Emprendedor\CuentaPersonal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class CuentaPersonalTest extends TestCase
{
    use RefreshDatabase;

    public function test_entrepreneur_account_page_is_displayed(): void
    {
        $user = User::factory()->emprendedor()->create();

        $this->actingAs($user)
            ->get(route('emprendedor.cuenta.index'))
            ->assertOk()
            ->assertSee('Mi cuenta')
            ->assertSee('Datos personales');
    }

    public function test_entrepreneur_can_update_personal_data(): void
    {
        $user = User::factory()->emprendedor()->create([
            'telefono' => '70000000',
        ]);

        $this->actingAs($user);

        Livewire::test(CuentaPersonal::class)
            ->set('nombreCompleto', 'Lucia Quispe')
            ->set('email', 'lucia@example.com')
            ->set('telefono', '71234567')
            ->call('guardarDatosPersonales')
            ->assertHasNoErrors();

        $user->refresh();

        $this->assertSame('Lucia Quispe', $user->name);
        $this->assertSame('lucia@example.com', $user->email);
        $this->assertSame('71234567', $user->telefono);
    }

    public function test_entrepreneur_can_update_password_from_personal_account(): void
    {
        $user = User::factory()->emprendedor()->create();

        $this->actingAs($user);

        Livewire::test(CuentaPersonal::class)
            ->set('currentPassword', 'password')
            ->set('password', 'nueva-clave-segura')
            ->set('passwordConfirmation', 'nueva-clave-segura')
            ->call('guardarPassword')
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('nueva-clave-segura', $user->fresh()->password));
    }
}
