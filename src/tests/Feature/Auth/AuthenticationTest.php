<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response
            ->assertOk()
            ->assertSee('Iniciar sesion')
            ->assertSee('Olvide mi contrasena');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->comprador()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->comprador()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'wrong-password');

        $component->call('login');

        $component
            ->assertHasErrors()
            ->assertNoRedirect();

        $this->assertGuest();
    }

    public function test_comprador_is_redirected_to_user_dashboard(): void
    {
        $user = User::factory()->comprador()->create();

        $this->actingAs($user);

        $this->get('/dashboard')
            ->assertRedirect(route('dashboard.usuario', absolute: false));
    }

    public function test_emprendedor_is_redirected_to_business_dashboard(): void
    {
        $user = User::factory()->emprendedor()->create();

        $this->actingAs($user);

        $this->get('/dashboard')
            ->assertRedirect(route('dashboard.emprendedor', absolute: false));
    }

    public function test_admin_is_redirected_to_admin_dashboard(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user);

        $this->get('/dashboard')
            ->assertRedirect(route('dashboard.admin', absolute: false));
    }

    public function test_inactive_users_can_not_authenticate(): void
    {
        $user = User::factory()->comprador()->inactivo()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasErrors(['form.email'])
            ->assertNoRedirect();

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->comprador()->create();

        $this->actingAs($user);

        $component = Volt::test('layout.navigation');

        $component->call('logout');

        $component
            ->assertHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
