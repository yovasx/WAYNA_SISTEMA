<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\ReportExportController;
use App\Http\Controllers\Admin\ReportExcelExportController;
use App\Http\Controllers\Admin\ReportPdfExportController;
use App\Livewire\Admin\CategoriasIndex as AdminCategoriasIndex;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\DonacionesIndex as AdminDonacionesIndex;
use App\Livewire\Admin\EmprendedoresIndex as AdminEmprendedoresIndex;
use App\Livewire\Admin\HistorialPuntosIndex as AdminHistorialPuntosIndex;
use App\Livewire\Admin\InsigniasIndex as AdminInsigniasIndex;
use App\Livewire\Admin\PedidosIndex as AdminPedidosIndex;
use App\Livewire\Admin\PuntosDonadoresIndex as AdminPuntosDonadoresIndex;
use App\Livewire\Admin\ReportesIndex as AdminReportesIndex;
use App\Livewire\Admin\TransaccionesIndex as AdminTransaccionesIndex;
use App\Livewire\Admin\UsuariosIndex as AdminUsuariosIndex;
use App\Livewire\Admin\ProductosIndex as AdminProductosIndex;
use App\Livewire\Admin\ReservasIndex as AdminReservasIndex;
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

Route::prefix('panel/admin')
    ->middleware(['auth', 'rol:admin'])
    ->group(function () {
        Route::get('/', AdminDashboard::class)->name('dashboard.admin');
        Route::get('reportes', AdminReportesIndex::class)->name('admin.reportes.index');
        Route::get('reportes/export', ReportExportController::class)->name('admin.reportes.export');
        Route::get('reportes/export/excel', ReportExcelExportController::class)->name('admin.reportes.export.excel');
        Route::get('reportes/export/pdf', ReportPdfExportController::class)->name('admin.reportes.export.pdf');
        Route::get('emprendedores', AdminEmprendedoresIndex::class)->name('admin.emprendedores.index');
        Route::get('categorias', AdminCategoriasIndex::class)->name('admin.categorias.index');
        Route::get('productos', AdminProductosIndex::class)->name('admin.productos.index');
        Route::get('pedidos', AdminPedidosIndex::class)->name('admin.pedidos.index');
        Route::get('donaciones', AdminDonacionesIndex::class)->name('admin.donaciones.index');
        Route::get('transacciones', AdminTransaccionesIndex::class)->name('admin.transacciones.index');
        Route::get('puntos-donadores', AdminPuntosDonadoresIndex::class)->name('admin.puntos-donadores.index');
        Route::get('historial-puntos', AdminHistorialPuntosIndex::class)->name('admin.historial-puntos.index');
        Route::get('insignias', AdminInsigniasIndex::class)->name('admin.insignias.index');
        Route::get('reservas', AdminReservasIndex::class)->name('admin.reservas.index');
        Route::get('usuarios', AdminUsuariosIndex::class)->name('admin.usuarios.index');
    });

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
