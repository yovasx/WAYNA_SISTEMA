<div class="space-y-8">
    @if (session('perfil_estado'))
        <div class="wayna-alert-success">
            {{ session('perfil_estado') }}
        </div>
    @endif

    <x-admin.page-header eyebrow="Perfil del negocio" title="Identidad del emprendimiento" description="Gestiona la historia, branding visual, video por URL y redes sociales del negocio sin mezclarlo con la operacion diaria del dashboard." />

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.6fr)_minmax(320px,0.9fr)]">
        <div class="space-y-6">
            <x-admin.panel-card title="Identidad" description="Datos base con los que se presenta tu negocio dentro de WAYNA.">
                <div class="grid gap-5 lg:grid-cols-2">
                    <div class="lg:col-span-2">
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Nombre del negocio</label>
                        <input wire:model.live="nombreNegocio" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Nombre comercial del emprendimiento">
                        <x-input-error :messages="$errors->get('nombreNegocio')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Categoria</label>
                        <select wire:model.live="categoriaId" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0">
                            <option value="">Sin categoria</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('categoriaId')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">NIT</label>
                        <input wire:model.live="nit" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Opcional">
                        <x-input-error :messages="$errors->get('nit')" class="mt-2" />
                    </div>
                </div>
            </x-admin.panel-card>

            <x-admin.panel-card title="Historia del negocio" description="Aqui defines la narrativa corta y larga con la que se presentara tu emprendimiento.">
                <div class="space-y-5">
                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Descripcion</label>
                        <textarea wire:model.live="descripcion" rows="4" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Resumen breve del negocio."></textarea>
                        <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Historia</label>
                        <textarea wire:model.live="historia" rows="6" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Cuenta el origen del negocio, su tecnica y propuesta artesanal."></textarea>
                        <x-input-error :messages="$errors->get('historia')" class="mt-2" />
                    </div>
                </div>
            </x-admin.panel-card>

            <x-admin.panel-card title="Ubicacion del negocio" description="Esta seccion es opcional. Si tienes un local fisico, puedes cargar una referencia simple y marcarlo en el mapa.">
                <div class="space-y-6" data-business-location-root>
                    <label class="flex items-center gap-3 rounded-2xl border border-[#d8d2de] bg-white px-4 py-4">
                        <input wire:model.live="tieneLocalFisico" type="checkbox" class="rounded border-[#cbc4d4] text-[#5f4cae] focus:ring-[#5f4cae]">
                        <span>
                            <span class="block text-sm font-medium text-slate-900">Mi negocio tiene local fisico</span>
                            <span class="mt-1 block text-xs text-slate-500">Puedes dejar esta seccion vacia si solo atiendes por coordinacion o medios digitales.</span>
                        </span>
                    </label>

                    <div class="grid gap-5 lg:grid-cols-3">
                        <div>
                            <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Ciudad</label>
                            <input wire:model.live="ciudad" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Ej. La Paz">
                            <x-input-error :messages="$errors->get('ciudad')" class="mt-2" />
                        </div>

                        <div>
                            <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Nombre de la calle</label>
                            <input wire:model.live="direccionCalle" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Ej. Av. 16 de Julio">
                            <x-input-error :messages="$errors->get('direccionCalle')" class="mt-2" />
                        </div>

                        <div>
                            <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Numero</label>
                            <input wire:model.live="direccionNumero" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Ej. 1234">
                            <x-input-error :messages="$errors->get('direccionNumero')" class="mt-2" />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div wire:ignore class="space-y-4">
                            <div class="wayna-map-shell">
                                <div
                                    data-business-map
                                    data-lat="{{ $latitud ?? '' }}"
                                    data-lng="{{ $longitud ?? '' }}"
                                    class="wayna-map"
                                ></div>
                            </div>
                        </div>

                        <input type="hidden" wire:model.live="latitud" data-map-lat>
                        <input type="hidden" wire:model.live="longitud" data-map-lng>

                        <div class="rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4 text-sm text-slate-600">
                            <p class="font-medium text-slate-900">Mapa del negocio</p>
                            <p class="mt-2">Haz clic en el mapa para marcar tu negocio. Tambien puedes arrastrar el marcador para ajustar la ubicacion.</p>
                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                <div class="rounded-2xl border border-[#e5deef] bg-white px-4 py-3">
                                    <p class="font-mono-data text-[11px] uppercase tracking-[0.2em] text-slate-500">Latitud</p>
                                    <p data-map-lat-display class="mt-2 text-sm text-slate-900">{{ $latitud !== null && $latitud !== '' ? $latitud : 'Sin marcar' }}</p>
                                </div>
                                <div class="rounded-2xl border border-[#e5deef] bg-white px-4 py-3">
                                    <p class="font-mono-data text-[11px] uppercase tracking-[0.2em] text-slate-500">Longitud</p>
                                    <p data-map-lng-display class="mt-2 text-sm text-slate-900">{{ $longitud !== null && $longitud !== '' ? $longitud : 'Sin marcar' }}</p>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('latitud')" class="mt-3" />
                            <x-input-error :messages="$errors->get('longitud')" class="mt-2" />
                        </div>
                    </div>
                </div>
            </x-admin.panel-card>

            <x-admin.panel-card title="Portada, logo y video" description="Define la identidad visual del negocio y el video de presentacion por URL.">
                <div class="space-y-6">
                    <div class="grid gap-5 lg:grid-cols-[220px_minmax(0,1fr)]">
                        <div class="flex h-40 w-full items-center justify-center overflow-hidden rounded-[1.5rem] border border-dashed border-[#d8d2de] bg-[#fcfbfe] text-slate-400">
                            @if ($fotoPortadaNueva)
                                <img src="{{ $fotoPortadaNueva->temporaryUrl() }}" alt="Nueva portada" class="h-full w-full object-cover">
                            @elseif ($this->fotoPortadaActualUrl() && ! $eliminarFotoPortada)
                                <img src="{{ $this->fotoPortadaActualUrl() }}" alt="Portada actual" class="h-full w-full object-cover">
                            @else
                                <span class="material-symbols-outlined text-[42px]">image</span>
                            @endif
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Foto de portada</label>
                                <input wire:model.live="fotoPortadaNueva" type="file" accept="image/png,image/jpeg,image/webp" class="mt-2 block w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-[#f7f2fb] file:px-3 file:py-2 file:text-sm file:font-medium file:text-[#5f4cae]">
                                <x-input-error :messages="$errors->get('fotoPortadaNueva')" class="mt-2" />
                            </div>

                            @if ($this->fotoPortadaActualUrl())
                                <label class="flex items-center gap-3 rounded-2xl border border-[#d8d2de] bg-white px-4 py-4">
                                    <input wire:model.live="eliminarFotoPortada" type="checkbox" class="rounded border-[#cbc4d4] text-[#5f4cae] focus:ring-[#5f4cae]">
                                    <span class="text-sm text-slate-700">Quitar portada actual</span>
                                </label>
                            @endif
                        </div>
                    </div>

                    <div class="grid gap-5 lg:grid-cols-[180px_minmax(0,1fr)]">
                        <div class="flex h-32 w-32 items-center justify-center overflow-hidden rounded-[1.5rem] border border-dashed border-[#d8d2de] bg-[#fcfbfe] text-slate-400">
                            @if ($logoNuevo)
                                <img src="{{ $logoNuevo->temporaryUrl() }}" alt="Nuevo logo" class="h-full w-full object-cover">
                            @elseif ($this->logoActualUrl() && ! $eliminarLogo)
                                <img src="{{ $this->logoActualUrl() }}" alt="Logo actual" class="h-full w-full object-cover">
                            @else
                                <span class="material-symbols-outlined text-[36px]">brand_awareness</span>
                            @endif
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Logo del negocio</label>
                                <input wire:model.live="logoNuevo" type="file" accept="image/png,image/jpeg,image/webp" class="mt-2 block w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-[#f7f2fb] file:px-3 file:py-2 file:text-sm file:font-medium file:text-[#5f4cae]">
                                <x-input-error :messages="$errors->get('logoNuevo')" class="mt-2" />
                            </div>

                            @if ($this->logoActualUrl())
                                <label class="flex items-center gap-3 rounded-2xl border border-[#d8d2de] bg-white px-4 py-4">
                                    <input wire:model.live="eliminarLogo" type="checkbox" class="rounded border-[#cbc4d4] text-[#5f4cae] focus:ring-[#5f4cae]">
                                    <span class="text-sm text-slate-700">Quitar logo actual</span>
                                </label>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Video por URL</label>
                        <input wire:model.live="videoUrl" type="url" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="https://www.youtube.com/watch?v=...">
                        <x-input-error :messages="$errors->get('videoUrl')" class="mt-2" />
                        <p class="mt-2 text-xs text-slate-500">Acepta enlaces de YouTube o Vimeo. Si la URL no se puede embeber, igual se guardara como enlace publico.</p>
                    </div>
                </div>
            </x-admin.panel-card>
        </div>

        <aside class="space-y-6">
            <x-admin.panel-card title="Redes sociales" description="Canales rapidos para que tu negocio conecte con clientes y comunidad.">
                <div class="space-y-4">
                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Instagram</label>
                        <input wire:model.live="instagram" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="@tu_cuenta o URL">
                        <x-input-error :messages="$errors->get('instagram')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Facebook</label>
                        <input wire:model.live="facebook" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Pagina o URL">
                        <x-input-error :messages="$errors->get('facebook')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">TikTok</label>
                        <input wire:model.live="tiktok" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="@tu_cuenta o URL">
                        <x-input-error :messages="$errors->get('tiktok')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">WhatsApp</label>
                        <input wire:model.live="whatsapp" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Numero o enlace corto">
                        <x-input-error :messages="$errors->get('whatsapp')" class="mt-2" />
                    </div>
                </div>
            </x-admin.panel-card>

            <x-admin.panel-card title="Vista previa" description="Asi se completa la identidad del negocio para futuras pantallas publicas.">
                <div class="space-y-4">
                    <div class="rounded-[1.5rem] border border-[#ebe6ef] bg-[#fcfbfe] p-4">
                        <p class="font-medium text-slate-900">{{ $nombreNegocio ?: 'Nombre del negocio' }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $descripcion ?: 'Agrega una descripcion breve que explique tu propuesta artesanal.' }}</p>
                    </div>

                    <div class="rounded-[1.5rem] border border-[#ebe6ef] bg-[#fcfbfe] p-4 text-sm text-slate-600">
                        <p class="font-medium text-slate-900">Ubicacion publica</p>
                        @if ($tieneLocalFisico)
                            <p class="mt-2">Negocio con atencion local activa.</p>
                            <p class="mt-2">{{ collect([$direccionCalle !== '' ? trim($direccionCalle.' '.$direccionNumero) : '', $ciudad])->filter(fn ($item) => trim((string) $item) !== '')->implode(', ') ?: 'Direccion aun no completada.' }}</p>
                            <p class="mt-2">{{ ($latitud !== null && $latitud !== '' && $longitud !== null && $longitud !== '') ? 'Ubicacion marcada en el mapa.' : 'Aun no hay un punto marcado en el mapa.' }}</p>
                        @else
                            <p class="mt-2">Atencion digital o por coordinacion previa.</p>
                        @endif
                    </div>

                    @if ($this->videoEmbedUrl())
                        <div class="overflow-hidden rounded-[1.5rem] border border-[#ebe6ef] bg-black">
                            <iframe src="{{ $this->videoEmbedUrl() }}" title="Video del negocio" class="aspect-video w-full" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    @elseif ($videoUrl !== '')
                        <a href="{{ $videoUrl }}" target="_blank" rel="noreferrer" class="inline-flex rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-[#5f4cae] hover:border-[#5f4cae] hover:text-[#4a3597]">
                            Ver enlace del video
                        </a>
                    @endif

                    <div class="rounded-[1.5rem] border border-[#ebe6ef] bg-[#fcfbfe] p-4 text-sm text-slate-600">
                        <p class="font-medium text-slate-900">Redes cargadas</p>
                        <p class="mt-2">{{ collect([$instagram, $facebook, $tiktok, $whatsapp])->filter(fn ($item) => trim($item) !== '')->count() }} canal(es) listos para mostrarse.</p>
                    </div>
                </div>
            </x-admin.panel-card>

            <div class="flex justify-end">
                <button type="button" wire:click="guardar" class="rounded-2xl bg-[#5f4cae] px-5 py-3 text-sm font-medium text-white transition hover:opacity-90">
                    Guardar cambios
                </button>
            </div>
        </aside>
    </div>
</div>
