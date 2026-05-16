<?php

namespace App\Livewire\Emprendedor;

use App\Models\Categoria;
use App\Models\PerfilEmprendedor;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class PerfilIndex extends Component
{
    use WithFileUploads;

    public string $nombreNegocio = '';

    public int|string|null $categoriaId = null;

    public string $nit = '';

    public string $descripcion = '';

    public string $historia = '';

    public string $videoUrl = '';

    public string $instagram = '';

    public string $facebook = '';

    public string $tiktok = '';

    public string $whatsapp = '';

    public mixed $fotoPortadaNueva = null;

    public bool $eliminarFotoPortada = false;

    public mixed $logoNuevo = null;

    public bool $eliminarLogo = false;

    public function mount(): void
    {
        $this->cargarFormulario();
    }

    public function guardar(): void
    {
        $perfil = $this->asegurarPerfilEmprendedor();

        $datos = $this->validate([
            'nombreNegocio' => ['required', 'string', 'max:180'],
            'categoriaId' => ['nullable', 'integer', 'exists:categorias,id'],
            'nit' => ['nullable', 'string', 'max:20'],
            'descripcion' => ['nullable', 'string'],
            'historia' => ['nullable', 'string'],
            'videoUrl' => ['nullable', 'url', 'max:500'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'tiktok' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'fotoPortadaNueva' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'logoNuevo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [], [
            'nombreNegocio' => 'nombre del negocio',
            'categoriaId' => 'categoria',
            'nit' => 'NIT',
            'descripcion' => 'descripcion',
            'historia' => 'historia',
            'videoUrl' => 'video',
            'instagram' => 'Instagram',
            'facebook' => 'Facebook',
            'tiktok' => 'TikTok',
            'whatsapp' => 'WhatsApp',
            'fotoPortadaNueva' => 'foto de portada',
            'logoNuevo' => 'logo',
        ]);

        $perfil->update([
            'nombre_negocio' => $datos['nombreNegocio'],
            'categoria_id' => $datos['categoriaId'] ? (int) $datos['categoriaId'] : null,
            'nit' => $datos['nit'] !== '' ? $datos['nit'] : null,
            'descripcion' => $datos['descripcion'] !== '' ? $datos['descripcion'] : null,
            'historia' => $datos['historia'] !== '' ? $datos['historia'] : null,
            'video_url' => $datos['videoUrl'] !== '' ? $datos['videoUrl'] : null,
            'foto_portada' => $this->guardarArchivoPublico(
                $perfil->foto_portada,
                $this->fotoPortadaNueva,
                'emprendedores/portadas',
                $this->eliminarFotoPortada
            ),
            'logo_url' => $this->guardarArchivoPublico(
                $perfil->logo_url,
                $this->logoNuevo,
                'emprendedores/logos',
                $this->eliminarLogo
            ),
            'redes_sociales' => $this->redesPayload(),
        ]);

        $this->fotoPortadaNueva = null;
        $this->logoNuevo = null;
        $this->eliminarFotoPortada = false;
        $this->eliminarLogo = false;
        $this->cargarFormulario();

        session()->flash('perfil_estado', 'Perfil del negocio actualizado correctamente.');
    }

    public function render(): View
    {
        return view('livewire.emprendedor.perfil-index', [
            'categorias' => Categoria::query()->orderBy('nombre')->get(['id', 'nombre']),
        ])->layout('layouts.emprendedor', [
            'pageTitle' => 'Perfil del negocio',
        ]);
    }

    public function fotoPortadaActualUrl(): ?string
    {
        return $this->resolverUrlArchivo($this->asegurarPerfilEmprendedor()->foto_portada);
    }

    public function logoActualUrl(): ?string
    {
        return $this->resolverUrlArchivo($this->asegurarPerfilEmprendedor()->logo_url);
    }

    public function videoEmbedUrl(): ?string
    {
        $url = trim($this->videoUrl);

        if ($url === '') {
            return null;
        }

        if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/)([^&?/]+)~i', $url, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1];
        }

        if (preg_match('~vimeo\.com/(\d+)~i', $url, $matches)) {
            return 'https://player.vimeo.com/video/'.$matches[1];
        }

        return null;
    }

    private function cargarFormulario(): void
    {
        $perfil = $this->asegurarPerfilEmprendedor();
        $redes = is_array($perfil->redes_sociales) ? $perfil->redes_sociales : [];

        $this->resetValidation();
        $this->nombreNegocio = $perfil->nombre_negocio ?? '';
        $this->categoriaId = $perfil->categoria_id;
        $this->nit = $perfil->nit ?? '';
        $this->descripcion = $perfil->descripcion ?? '';
        $this->historia = $perfil->historia ?? '';
        $this->videoUrl = $perfil->video_url ?? '';
        $this->instagram = $redes['instagram'] ?? '';
        $this->facebook = $redes['facebook'] ?? '';
        $this->tiktok = $redes['tiktok'] ?? '';
        $this->whatsapp = $redes['whatsapp'] ?? '';
    }

    private function redesPayload(): ?array
    {
        $redes = [
            'instagram' => trim($this->instagram),
            'facebook' => trim($this->facebook),
            'tiktok' => trim($this->tiktok),
            'whatsapp' => trim($this->whatsapp),
        ];

        $redes = array_filter($redes, static fn (string $valor) => $valor !== '');

        return $redes !== [] ? $redes : null;
    }

    private function asegurarPerfilEmprendedor(): PerfilEmprendedor
    {
        $usuario = auth()->user();

        return PerfilEmprendedor::firstOrCreate([
            'usuario_id' => $usuario->id,
        ], [
            'nombre_negocio' => $usuario->name,
            'estado' => 'pendiente',
        ]);
    }

    private function guardarArchivoPublico(?string $actual, mixed $nuevoArchivo, string $carpeta, bool $eliminar): ?string
    {
        if ($nuevoArchivo) {
            $this->eliminarArchivoPublico($actual);

            return $nuevoArchivo->store($carpeta, 'public');
        }

        if ($eliminar) {
            $this->eliminarArchivoPublico($actual);

            return null;
        }

        return $actual;
    }

    private function eliminarArchivoPublico(?string $ruta): void
    {
        if (! $ruta || filter_var($ruta, FILTER_VALIDATE_URL)) {
            return;
        }

        if (Storage::disk('public')->exists($ruta)) {
            Storage::disk('public')->delete($ruta);
        }
    }

    private function resolverUrlArchivo(?string $ruta): ?string
    {
        if (! $ruta) {
            return null;
        }

        if (filter_var($ruta, FILTER_VALIDATE_URL)) {
            return $ruta;
        }

        return Storage::disk('public')->url($ruta);
    }
}
