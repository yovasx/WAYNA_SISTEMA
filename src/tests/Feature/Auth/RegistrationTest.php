<?php

namespace Tests\Feature\Auth;

use App\Models\PerfilEmprendedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSee('Crear cuenta')
            ->assertSee('Tipo de cuenta');
    }

    public function test_new_compradores_can_register(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('rol', 'usuario')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('register');

        $component->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
        $this->assertTrue(User::query()->where('email', 'test@example.com')->firstOrFail()->tieneRol('comprador'));
    }

    public function test_new_emprendedores_can_register(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Taller Andino')
            ->set('email', 'emprendedor-test@example.com')
            ->set('rol', 'emprendedor')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('register');

        $component->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();

        $user = User::query()->where('email', 'emprendedor-test@example.com')->firstOrFail();

        $this->assertTrue($user->tieneRol('emprendedor'));
        $this->assertDatabaseHas('emprendedores', [
            'usuario_id' => $user->id,
        ]);
    }

    public function test_admin_can_not_register_from_public_form(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Admin Attempt')
            ->set('email', 'admin-attempt@example.com')
            ->set('rol', 'admin')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('register');

        $component
            ->assertHasErrors(['rol'])
            ->assertNoRedirect();

        $this->assertDatabaseMissing('usuarios', [
            'email' => 'admin-attempt@example.com',
        ]);
    }
}
