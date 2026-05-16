<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class EntrepreneurAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_entrepreneur_login_screen_can_be_rendered(): void
    {
        $this->get(route('emprendedor.login'))
            ->assertOk()
            ->assertSee('Ingresa a tu negocio')
            ->assertSee('Crear cuenta emprendedora');
    }

    public function test_entrepreneur_register_screen_can_be_rendered(): void
    {
        $this->get(route('emprendedor.register'))
            ->assertOk()
            ->assertSee('Crea tu espacio emprendedor')
            ->assertSee('Crear cuenta emprendedora');
    }

    public function test_new_users_can_register_from_dedicated_entrepreneur_form(): void
    {
        $component = Volt::test('pages.auth.emprendedor-register')
            ->set('name', 'Marca Andina')
            ->set('email', 'marca-andina@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('register');

        $component->assertRedirect(route('dashboard', absolute: false));

        $user = User::query()->where('email', 'marca-andina@example.com')->firstOrFail();

        $this->assertTrue($user->tieneRol('emprendedor'));
        $this->assertDatabaseHas('emprendedores', ['usuario_id' => $user->id]);
    }
}
