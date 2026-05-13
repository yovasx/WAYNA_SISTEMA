<?php

use App\Http\Controllers\HomeController;
use App\Livewire\Emprendedor\Dashboard as EmprendedorDashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::view('catalogo', 'placeholders.section', [
    'titulo' => 'Catalogo WAYNA',
    'descripcion' => 'Esta seccion quedara conectada al catalogo completo. Por ahora el inicio ya muestra categorias y productos populares listos para crecer.',
])->name('catalogo.index');

Route::view('emprendedores', 'placeholders.section', [
    'titulo' => 'Emprendedores WAYNA',
    'descripcion' => 'Aqui se centralizara la vista completa de emprendedores. Por ahora el inicio ya incluye el carrusel principal listo para poblarse.',
])->name('emprendedores.index');

Route::view('donar', 'placeholders.section', [
    'titulo' => 'Donar en WAYNA',
    'descripcion' => 'La experiencia de donacion tendra su flujo completo aqui. Mientras tanto, el inicio ya muestra el bloque de impacto y llamado a donar.',
])->name('donar.index');

Route::view('carrito', 'placeholders.section', [
    'titulo' => 'Carrito de compras',
    'descripcion' => 'El carrito completo se conectara aqui. El icono en el inicio ya queda listo para usarlo con datos reales.',
])->middleware('auth')->name('carrito.index');

Route::view('notificaciones', 'placeholders.section', [
    'titulo' => 'Notificaciones',
    'descripcion' => 'Las notificaciones del usuario se mostraran aqui. El icono del inicio ya queda preparado para la integracion real.',
])->middleware('auth')->name('notificaciones.index');

Route::get('dashboard', function (Request $request) {
    if ($request->user()->tieneRol('admin')) {
        return redirect()->route('dashboard.admin');
    }

    if ($request->user()->tieneRol('emprendedor')) {
        return redirect()->route('dashboard.emprendedor');
    }

    return redirect()->route('dashboard.usuario');
})
    ->middleware(['auth'])
    ->name('dashboard');

Route::view('panel/admin', 'dashboards.role', [
    'titulo' => 'Panel administrativo',
    'subtitulo' => 'Administra usuarios, categorias, productos y actividad del sistema.',
    'kpis' => [
        ['label' => 'Usuarios activos', 'value' => '03', 'tone' => 'violet'],
        ['label' => 'Categorias base', 'value' => '02', 'tone' => 'amber'],
        ['label' => 'Productos demo', 'value' => '03', 'tone' => 'green'],
    ],
])
    ->middleware(['auth', 'rol:admin'])
    ->name('dashboard.admin');

Route::view('panel/usuario', 'dashboards.role', [
    'titulo' => 'Panel de usuario',
    'subtitulo' => 'Revisa tus compras, donaciones y productos destacados de WAYNA.',
    'kpis' => [
        ['label' => 'Pedidos', 'value' => '00', 'tone' => 'violet'],
        ['label' => 'Favoritos', 'value' => '00', 'tone' => 'amber'],
        ['label' => 'Donaciones', 'value' => '00', 'tone' => 'green'],
    ],
])
    ->middleware(['auth', 'rol:usuario'])
    ->name('dashboard.usuario');

Route::get('panel/emprendedor', EmprendedorDashboard::class)
    ->middleware(['auth', 'rol:emprendedor'])
    ->name('dashboard.emprendedor');

Route::post('logout', function (Request $request) {
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home');
})
    ->middleware(['auth'])
    ->name('logout');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
