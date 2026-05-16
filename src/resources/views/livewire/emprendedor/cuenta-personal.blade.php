<div class="space-y-8">
    @if (session('cuenta_estado'))
        <div class="wayna-alert-success">{{ session('cuenta_estado') }}</div>
    @endif

    @if (session('password_estado'))
        <div class="wayna-alert-success">{{ session('password_estado') }}</div>
    @endif

    <x-admin.page-header eyebrow="Cuenta personal" title="Mi cuenta" description="Gestiona tus datos personales, tu foto de perfil y la seguridad de acceso al panel emprendedor." />

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.45fr)_minmax(320px,0.7fr)]">
        <div class="space-y-6">
            <x-admin.panel-card title="Datos personales" description="Informacion base del responsable que opera este emprendimiento dentro de WAYNA.">
                <form wire:submit="guardarDatosPersonales" class="space-y-5">
                    <div class="grid gap-5 lg:grid-cols-2">
                        <div class="lg:col-span-2">
                            <label class="wayna-label">Nombre completo</label>
                            <input wire:model.live="nombreCompleto" type="text" class="wayna-input mt-2" placeholder="Tu nombre completo o identidad visible de la cuenta">
                            <x-input-error :messages="$errors->get('nombreCompleto')" class="mt-2" />
                        </div>

                        <div>
                            <label class="wayna-label">Correo</label>
                            <input wire:model.live="email" type="email" class="wayna-input mt-2" placeholder="nombre@ejemplo.com">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <label class="wayna-label">Telefono</label>
                            <input wire:model.live="telefono" type="text" class="wayna-input mt-2" placeholder="Ej. 71234567">
                            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="wayna-btn-primary">Guardar datos personales</button>
                    </div>
                </form>
            </x-admin.panel-card>

            <x-admin.panel-card title="Seguridad" description="Actualiza tu contrasena para mantener el acceso de tu cuenta protegido.">
                <form wire:submit="guardarPassword" class="space-y-5">
                    <div>
                        <label class="wayna-label">Contrasena actual</label>
                        <input wire:model.live="currentPassword" type="password" class="wayna-input mt-2" placeholder="Escribe tu contrasena actual">
                        <x-input-error :messages="$errors->get('currentPassword')" class="mt-2" />
                    </div>

                    <div class="grid gap-5 lg:grid-cols-2">
                        <div>
                            <label class="wayna-label">Nueva contrasena</label>
                            <input wire:model.live="password" type="password" class="wayna-input mt-2" placeholder="Minimo 8 caracteres">
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <label class="wayna-label">Confirmar nueva contrasena</label>
                            <input wire:model.live="passwordConfirmation" type="password" class="wayna-input mt-2" placeholder="Repite la nueva contrasena">
                            <x-input-error :messages="$errors->get('passwordConfirmation')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="wayna-btn-primary">Actualizar contrasena</button>
                    </div>
                </form>
            </x-admin.panel-card>
        </div>

        <aside class="space-y-6">
            <x-admin.panel-card title="Foto de perfil" description="Imagen personal visible en tu cuenta y en futuras superficies del panel.">
                <form wire:submit="guardarDatosPersonales" class="space-y-5">
                    <div class="mx-auto flex h-40 w-40 items-center justify-center overflow-hidden rounded-[1.75rem] border border-dashed border-[#d8d2de] bg-[#fcfbfe] text-slate-400">
                        @if ($fotoPerfilNueva)
                            <img src="{{ $fotoPerfilNueva->temporaryUrl() }}" alt="Nueva foto de perfil" class="h-full w-full object-cover">
                        @elseif ($this->fotoPerfilActualUrl() && ! $eliminarFotoPerfil)
                            <img src="{{ $this->fotoPerfilActualUrl() }}" alt="Foto de perfil actual" class="h-full w-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-[48px]">account_circle</span>
                        @endif
                    </div>

                    <div>
                        <label class="wayna-label">Subir imagen</label>
                        <input wire:model.live="fotoPerfilNueva" type="file" accept="image/png,image/jpeg,image/webp" class="wayna-file-input mt-2">
                        <x-input-error :messages="$errors->get('fotoPerfilNueva')" class="mt-2" />
                    </div>

                    @if ($this->fotoPerfilActualUrl())
                        <label class="flex items-center gap-3 rounded-2xl border border-stroke bg-surface-raised px-4 py-4 text-sm text-ink-soft">
                            <input wire:model.live="eliminarFotoPerfil" type="checkbox" class="wayna-checkbox">
                            <span>Quitar foto de perfil actual</span>
                        </label>
                    @endif

                    <div class="rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4 text-sm text-slate-600">
                        <p class="font-medium text-slate-900">Vista actual</p>
                        <p class="mt-2">{{ $nombreCompleto !== '' ? $nombreCompleto : 'Tu cuenta personal' }}</p>
                        <p class="mt-1">{{ $telefono !== '' ? $telefono : 'Sin telefono cargado' }}</p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="wayna-btn-secondary">Guardar foto</button>
                    </div>
                </form>
            </x-admin.panel-card>
        </aside>
    </div>
</div>
