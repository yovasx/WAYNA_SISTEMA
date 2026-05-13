<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>WAYNA</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#fdf8ff] text-slate-900 antialiased">
        <header class="fixed inset-x-0 top-0 z-50 border-b border-[#d8d2de] bg-[#fdf8ff]/95 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-[1280px] items-center justify-between px-4 sm:px-6 lg:px-10">
                <div class="flex items-center gap-8">
                    <a href="{{ route('home') }}" class="font-display text-3xl text-[#5f4cae]">WAYNA</a>

                    <nav class="hidden items-center gap-6 md:flex">
                        <a href="#catalogo" class="border-b-2 border-[#5f4cae] pb-1 text-sm text-[#5f4cae] transition hover:opacity-80">Catalog</a>
                        <a href="#emprendedores" class="text-sm text-slate-600 transition hover:text-[#5f4cae]">Entrepreneurs</a>
                        <a href="#donar" class="text-sm text-slate-600 transition hover:text-[#5f4cae]">Donate</a>
                    </nav>
                </div>

                <div class="flex items-center gap-3 text-sm">
                    @auth
                        <a href="{{ route('carrito.index') }}" class="relative rounded-full p-2 text-slate-600 transition hover:bg-white hover:text-[#5f4cae]" aria-label="Carrito">
                            <span class="material-symbols-outlined">shopping_cart</span>
                            @if ($carritoCantidad > 0)
                                <span class="absolute -right-1 -top-1 inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-[#a03f29] px-1 text-[10px] font-medium text-white">{{ $carritoCantidad }}</span>
                            @endif
                        </a>

                        <a href="{{ route('notificaciones.index') }}" class="relative rounded-full p-2 text-slate-600 transition hover:bg-white hover:text-[#5f4cae]" aria-label="Notificaciones">
                            <span class="material-symbols-outlined">notifications</span>
                            @if ($notificacionesCantidad > 0)
                                <span class="absolute -right-1 -top-1 inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-[#5f4cae] px-1 text-[10px] font-medium text-white">{{ $notificacionesCantidad }}</span>
                            @endif
                        </a>

                        <a href="{{ route('profile') }}" class="flex items-center gap-2 rounded-full border border-[#d8d2de] bg-white px-3 py-2 text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]" aria-label="Perfil">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#e6deff] font-medium text-[#4a3597]">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                            <span class="hidden lg:inline">{{ auth()->user()->name }}</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full border border-[#d8d2de] px-4 py-2 text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">Iniciar sesion</a>
                        <a href="{{ route('register') }}" class="rounded-full bg-[#5f4cae] px-4 py-2 text-white transition hover:opacity-90">Crear cuenta</a>
                    @endauth
                </div>
            </div>
        </header>

        <main class="pt-16">
            <section class="bg-[#f1efe8] px-4 py-10 sm:px-6 lg:px-10 lg:py-14">
                <div class="mx-auto grid max-w-[1280px] items-center gap-8 lg:grid-cols-2 lg:gap-10">
                    <div class="max-w-xl">
                        <div class="inline-flex rounded-full border border-[#cac4d4] bg-white/80 px-3 py-1">
                            <span class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-600">{{ max($emprendedores->count(), 0) }}+ emprendedores artesanales</span>
                        </div>

                        <h1 class="mt-6 font-display text-5xl leading-tight text-slate-900 lg:text-6xl">Arte boliviano, ahora en un solo lugar</h1>
                        <p class="mt-5 text-lg leading-8 text-slate-600">Descubre el legado de maestros artesanos de La Paz y otras regiones. Cada pieza cuenta una historia de tradicion, cultura y esfuerzo compartido.</p>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="#productos-populares" class="rounded-2xl bg-[#a03f29] px-5 py-3 text-sm font-medium text-white transition hover:opacity-90">Explorar catalogo</a>
                            <a href="#emprendedores" class="rounded-2xl border border-slate-800 px-5 py-3 text-sm font-medium text-slate-900 transition hover:bg-white/60">Ver emprendedores</a>
                            @auth
                                <a href="{{ route('dashboard') }}" class="rounded-2xl border border-[#d8d2de] bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">Ir a mi panel</a>
                            @endauth
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        @php
                            $mosaico = $productosPopulares->flatMap(function ($producto) {
                                return $producto->imagenes->take(1)->pluck('url');
                            })->merge($emprendedores->pluck('portada_url'))->filter()->take(4)->values();
                        @endphp
                        @for ($i = 0; $i < 4; $i++)
                            @php $imagen = $mosaico[$i] ?? null; @endphp
                            <div class="aspect-square overflow-hidden rounded-xl border border-[#d8d2de] bg-white">
                                @if ($imagen)
                                    <img src="{{ $imagen }}" alt="Vista artesanal WAYNA" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#f7f2fb] to-[#e6deff] font-display text-2xl text-[#5f4cae]">WAYNA</div>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>
            </section>

            <section id="catalogo" class="px-4 py-10 sm:px-6 lg:px-10">
                <div class="mx-auto max-w-[1280px]">
                    <h2 class="font-display text-4xl text-slate-900">Explorar por categoria</h2>
                    <div class="hide-scrollbar mt-6 flex gap-3 overflow-x-auto pb-2">
                        <a href="#productos-populares" class="whitespace-nowrap rounded-full bg-[#5f4cae] px-5 py-2 text-sm font-medium text-white">Todos</a>
                        @forelse ($categorias as $categoria)
                            <a href="#productos-populares" class="whitespace-nowrap rounded-full border border-[#cac4d4] bg-[#f7f2fb] px-5 py-2 text-sm text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">
                                {{ $categoria->nombre }}
                            </a>
                        @empty
                            <span class="whitespace-nowrap rounded-full border border-dashed border-[#cac4d4] bg-[#f7f2fb] px-5 py-2 text-sm text-slate-500">Sin categorias aun</span>
                        @endforelse
                    </div>
                </div>
            </section>

            <section id="emprendedores" class="bg-[#f7f2fb] px-4 py-16 sm:px-6 lg:px-10">
                <div class="mx-auto max-w-[1280px]">
                    <div class="mb-8 flex items-end justify-between gap-4">
                        <h2 class="font-display text-4xl text-slate-900">Conoce a nuestros emprendedores</h2>
                        <a href="{{ route('emprendedores.index') }}" class="hidden items-center gap-1 text-sm font-medium text-[#5f4cae] hover:underline md:flex">Ver todos <span class="material-symbols-outlined text-base">arrow_forward</span></a>
                    </div>

                    <div class="hide-scrollbar flex gap-6 overflow-x-auto pb-2">
                        @forelse ($emprendedores as $emprendedor)
                            <article class="min-w-[280px] max-w-[280px] overflow-hidden rounded-xl border border-[#d8d2de] bg-white transition hover:-translate-y-1">
                                <div class="relative h-40 bg-[#e6deff]">
                                    @if ($emprendedor->portada_url)
                                        <img src="{{ $emprendedor->portada_url }}" alt="Portada de {{ $emprendedor->nombre_emprendimiento }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center font-display text-2xl text-[#5f4cae]">{{ $emprendedor->nombre_emprendimiento }}</div>
                                    @endif
                                    <div class="absolute left-3 top-3 rounded-full bg-white/90 px-2 py-1 font-mono-data text-[10px] uppercase tracking-[0.22em] text-slate-600">
                                        {{ $emprendedor->categoria?->nombre ?? 'Artesania' }}
                                    </div>
                                </div>

                                <div class="relative p-4">
                                    <div class="-mt-10 mb-3 h-16 w-16 overflow-hidden rounded-full border-4 border-white bg-[#f1ecf5]">
                                        @if ($emprendedor->foto_perfil_url)
                                            <img src="{{ $emprendedor->foto_perfil_url }}" alt="Perfil de {{ $emprendedor->nombre_emprendimiento }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center font-display text-xl text-[#5f4cae]">{{ strtoupper(mb_substr($emprendedor->nombre_emprendimiento, 0, 1)) }}</div>
                                        @endif
                                    </div>

                                    <h3 class="font-display text-2xl text-slate-900">{{ $emprendedor->nombre_emprendimiento }}</h3>
                                    <p class="mt-1 text-sm text-slate-600">{{ $emprendedor->historia ? \Illuminate\Support\Str::limit($emprendedor->historia, 70) : 'Perfil listo para mostrar historia, ciudad y coleccion artesanal.' }}</p>
                                    <div class="mt-4 flex items-center justify-between border-t border-[#ebe6ef] pt-3">
                                        <span class="font-mono-data text-[11px] uppercase tracking-[0.2em] text-slate-500">{{ $emprendedor->productos_count }} productos</span>
                                        <span class="font-mono-data text-[11px] uppercase tracking-[0.2em] text-slate-500">{{ $emprendedor->ciudad ?: 'Bolivia' }}</span>
                                    </div>
                                </div>
                            </article>
                        @empty
                            @for ($i = 0; $i < 4; $i++)
                                <article class="min-w-[280px] max-w-[280px] overflow-hidden rounded-xl border border-dashed border-[#d8d2de] bg-white">
                                    <div class="h-40 bg-[#ebe6ef]"></div>
                                    <div class="p-4">
                                        <div class="-mt-10 mb-3 h-16 w-16 rounded-full border-4 border-white bg-[#d8d2de]"></div>
                                        <h3 class="font-display text-2xl text-slate-900">Emprendedor listo</h3>
                                        <p class="mt-1 text-sm text-slate-600">Este carrusel mostrara automaticamente perfiles aprobados cuando se registren y completen sus datos.</p>
                                    </div>
                                </article>
                            @endfor
                        @endforelse
                    </div>
                </div>
            </section>

            <section id="productos-populares" class="px-4 py-16 sm:px-6 lg:px-10">
                <div class="mx-auto max-w-[1280px]">
                    <div class="mb-8 flex items-end justify-between gap-4">
                        <h2 class="font-display text-4xl text-slate-900">Productos mas populares</h2>
                        <a href="{{ route('catalogo.index') }}" class="hidden text-sm font-medium text-[#5f4cae] hover:underline md:block">Ver catalogo completo</a>
                    </div>

                    <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                        @forelse ($productosPopulares as $producto)
                            @php
                                $imagenProducto = $producto->imagenes->firstWhere('es_principal', true) ?? $producto->imagenes->first();
                            @endphp
                            <article class="group cursor-pointer">
                                <div class="relative mb-3 aspect-square overflow-hidden rounded-lg border border-[#d8d2de] bg-white">
                                    @if ($imagenProducto)
                                        <img src="{{ $imagenProducto->url }}" alt="{{ $imagenProducto->texto_alternativo ?: $producto->nombre }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-[#f1ecf5] font-display text-3xl text-[#5f4cae]">{{ strtoupper(mb_substr($producto->nombre, 0, 1)) }}</div>
                                    @endif
                                    @auth
                                        <a href="{{ route('carrito.index') }}" class="absolute bottom-3 right-3 inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/90 text-[#5f4cae] opacity-0 shadow-sm transition duration-300 group-hover:translate-y-0 group-hover:opacity-100">
                                            <span class="material-symbols-outlined">add_shopping_cart</span>
                                        </a>
                                    @endauth
                                </div>
                                <h3 class="text-sm text-slate-900 transition group-hover:text-[#5f4cae]">{{ $producto->nombre }}</h3>
                                <p class="mt-1 text-sm text-slate-600">{{ $producto->perfilEmprendedor?->nombre_emprendimiento ?? 'WAYNA' }}</p>
                                <div class="mt-2 flex items-center justify-between">
                                    <span class="font-mono-data text-sm text-[#a03f29]">Bs. {{ number_format((float) $producto->precio, 2) }}</span>
                                    <span class="font-mono-data text-[10px] uppercase tracking-[0.18em] text-slate-500">{{ $producto->stock }} en stock</span>
                                </div>
                            </article>
                        @empty
                            @for ($i = 0; $i < 4; $i++)
                                <article>
                                    <div class="mb-3 aspect-square rounded-lg border border-dashed border-[#d8d2de] bg-[#f7f2fb]"></div>
                                    <h3 class="text-sm text-slate-900">Producto listo para aparecer</h3>
                                    <p class="mt-1 text-sm text-slate-600">Esta grilla se llenara sola cuando se publiquen productos.</p>
                                </article>
                            @endfor
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="bg-[#312f36] px-4 py-16 text-[#f4eff8] sm:px-6 lg:px-10">
                <div class="mx-auto grid max-w-[1280px] gap-10 lg:grid-cols-[0.95fr_1.05fr]">
                    <div>
                        <p class="font-mono-data text-xs uppercase tracking-[0.28em] text-[#cbbeff]">Top donadores</p>
                        <h2 class="mt-3 font-display text-4xl">Quienes impulsan este ecosistema</h2>
                        <p class="mt-4 max-w-lg text-sm leading-7 text-[#d7d1dd]">Aunque aun no existan donadores registrados, este bloque ya esta listo para mostrar el ranking, puntos y posicion del periodo actual en cuanto entren datos.</p>
                    </div>

                    <div class="grid gap-4">
                        @forelse ($topDonadores as $donador)
                            <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-5 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-[#5f4cae] font-medium text-white">{{ $donador->anonimo ? '?' : strtoupper(mb_substr($donador->name, 0, 1)) }}</div>
                                    <div>
                                        <p class="text-sm font-medium text-white">{{ $donador->anonimo ? 'Donador anonimo' : $donador->name }}</p>
                                        <p class="font-mono-data text-[11px] uppercase tracking-[0.18em] text-[#cbbeff]">Periodo {{ $periodoRanking }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-mono-data text-sm text-white">{{ $donador->total_puntos }} pts</p>
                                    <p class="text-xs text-[#d7d1dd]">Puesto #{{ $donador->posicion }}</p>
                                </div>
                            </div>
                        @empty
                            @for ($i = 1; $i <= 3; $i++)
                                <div class="flex items-center justify-between rounded-2xl border border-dashed border-white/15 bg-white/5 px-5 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-white">Top donador #{{ $i }}</p>
                                        <p class="text-xs text-[#d7d1dd]">Aparecera cuando existan rankings cargados.</p>
                                    </div>
                                    <span class="font-mono-data text-xs uppercase tracking-[0.18em] text-[#cbbeff]">Pendiente</span>
                                </div>
                            @endfor
                        @endforelse
                    </div>
                </div>
            </section>

            <section id="donar" class="px-4 py-16 sm:px-6 lg:px-10">
                <div class="mx-auto max-w-[1280px] rounded-[2rem] border border-[#d8d2de] bg-[#f1ecf5] p-8 lg:p-10">
                    <div class="grid gap-8 lg:grid-cols-[1.15fr_0.85fr] lg:items-end">
                        <div>
                            <p class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Impacto de donacion</p>
                            <h2 class="mt-3 font-display text-4xl text-slate-900">Cada aporte sostiene talleres, historias y continuidad cultural.</h2>
                            <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600">Este mensaje final ya queda listo para crecer con campañas reales. Cuando entren nuevas donaciones, esta seccion mostrara el impacto acumulado sin necesidad de rehacer la UI.</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-white px-5 py-4">
                                <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Monto total</p>
                                <p class="mt-3 font-display text-3xl text-[#5f4cae]">Bs {{ number_format($impactoDonaciones['total'], 2) }}</p>
                            </div>
                            <div class="rounded-2xl bg-white px-5 py-4">
                                <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Donaciones</p>
                                <p class="mt-3 font-display text-3xl text-[#a03f29]">{{ $impactoDonaciones['cantidad'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('donar.index') }}" class="rounded-2xl bg-[#a03f29] px-5 py-3 text-sm font-medium text-white transition hover:opacity-90">Donar ahora</a>
                        <a href="#emprendedores" class="rounded-2xl border border-[#d8d2de] bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">Conocer emprendedores</a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-[#d8d2de] bg-white/70 px-4 py-10 sm:px-6 lg:px-10">
            <div class="mx-auto grid max-w-[1280px] gap-8 lg:grid-cols-[1.1fr_0.9fr_0.9fr_0.9fr]">
                <div>
                    <a href="{{ route('home') }}" class="font-display text-3xl text-[#5f4cae]">WAYNA</a>
                    <p class="mt-4 max-w-sm text-sm leading-7 text-slate-600">Marketplace y ecosistema cultural listo para conectar artesania boliviana, comercio justo y donacion con una interfaz preparada para crecer.</p>
                </div>

                <div>
                    <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Explorar</p>
                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        <a href="#catalogo" class="block hover:text-[#5f4cae]">Catalogo</a>
                        <a href="#emprendedores" class="block hover:text-[#5f4cae]">Emprendedores</a>
                        <a href="#donar" class="block hover:text-[#5f4cae]">Donar</a>
                    </div>
                </div>

                <div>
                    <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Cuenta</p>
                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        @auth
                            <a href="{{ route('profile') }}" class="block hover:text-[#5f4cae]">Mi perfil</a>
                            <a href="{{ route('dashboard') }}" class="block hover:text-[#5f4cae]">Mi panel</a>
                        @else
                            <a href="{{ route('login') }}" class="block hover:text-[#5f4cae]">Iniciar sesion</a>
                            <a href="{{ route('register') }}" class="block hover:text-[#5f4cae]">Crear cuenta</a>
                        @endauth
                    </div>
                </div>

                <div>
                    <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Estado</p>
                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        <p>Footer listo para integraciones futuras.</p>
                        <p>Version visual preparada para 2026.</p>
                    </div>
                </div>
            </div>

            <div class="mx-auto mt-10 flex max-w-[1280px] flex-col gap-3 border-t border-[#ebe6ef] pt-6 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                <p>© 2026 WAYNA. Todos los derechos reservados.</p>
                <p>Inicio preparado para catalogo, emprendedores, donaciones, ranking y footer final.</p>
            </div>
        </footer>
    </body>
</html>
