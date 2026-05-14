<?php

namespace App\Livewire\Admin;

use App\Models\Insignia;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class InsigniasIndex extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public bool $mostrarModal = false;

    public ?int $insigniaIdEditando = null;

    public ?int $insigniaIdEliminar = null;

    public string $nombre = '';

    public string $descripcion = '';

    public string $icono = '';

    public string $criterioJson = '';

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function abrirModal(?int $insigniaId = null): void
    {
        $this->resetFormulario();
        $this->insigniaIdEditando = $insigniaId;

        if ($insigniaId) {
            $insignia = Insignia::query()->findOrFail($insigniaId);
            $this->nombre = $insignia->nombre;
            $this->descripcion = $insignia->descripcion ?? '';
            $this->icono = $insignia->icono ?? '';
            $this->criterioJson = $insignia->criterio_json
                ? json_encode($insignia->criterio_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                : '';
        }

        $this->mostrarModal = true;
    }

    public function guardar(): void
    {
        $datos = $this->validate([
            'nombre' => ['required', 'string', 'max:100', Rule::unique('insignias', 'nombre')->ignore($this->insigniaIdEditando)],
            'descripcion' => ['nullable', 'string'],
            'icono' => ['nullable', 'string', 'max:500'],
            'criterioJson' => ['nullable', 'string'],
        ], [], [
            'nombre' => 'nombre de insignia',
            'descripcion' => 'descripcion',
            'icono' => 'icono',
            'criterioJson' => 'criterio JSON',
        ]);

        $criterio = null;

        if ($datos['criterioJson'] !== '') {
            $criterio = json_decode($datos['criterioJson'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->addError('criterioJson', 'El criterio JSON debe tener un formato valido.');

                return;
            }
        }

        $payload = [
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] !== '' ? $datos['descripcion'] : null,
            'icono' => $datos['icono'] !== '' ? $datos['icono'] : null,
            'criterio_json' => $criterio,
        ];

        if ($this->insigniaIdEditando) {
            Insignia::query()->findOrFail($this->insigniaIdEditando)->update($payload);
            session()->flash('admin_status', 'Insignia actualizada correctamente.');
        } else {
            Insignia::query()->create($payload);
            session()->flash('admin_status', 'Insignia creada correctamente.');
        }

        $this->cerrarModal();
    }

    public function confirmarEliminar(int $insigniaId): void
    {
        $this->insigniaIdEliminar = $insigniaId;
    }

    public function eliminar(): void
    {
        if (! $this->insigniaIdEliminar) {
            return;
        }

        Insignia::query()->findOrFail($this->insigniaIdEliminar)->delete();
        $this->insigniaIdEliminar = null;
        session()->flash('admin_status', 'Insignia eliminada correctamente.');
        $this->resetPage();
    }

    public function cerrarModal(): void
    {
        $this->mostrarModal = false;
        $this->resetFormulario();
    }

    public function render(): View
    {
        $insignias = Insignia::query()
            ->when($this->busqueda !== '', function ($query) {
                $query->where(function ($subquery) {
                    $subquery
                        ->where('nombre', 'like', '%'.$this->busqueda.'%')
                        ->orWhere('descripcion', 'like', '%'.$this->busqueda.'%');
                });
            })
            ->orderBy('nombre')
            ->paginate(10);

        return view('livewire.admin.insignias-index', [
            'insignias' => $insignias,
            'resumen' => [
                'totales' => Insignia::query()->count(),
                'con_icono' => Insignia::query()->whereNotNull('icono')->count(),
                'con_criterio' => Insignia::query()->whereNotNull('criterio_json')->count(),
                'sin_criterio' => Insignia::query()->whereNull('criterio_json')->count(),
            ],
        ])->layout('layouts.admin', [
            'pageTitle' => 'Insignias',
        ]);
    }

    private function resetFormulario(): void
    {
        $this->resetValidation();
        $this->insigniaIdEditando = null;
        $this->nombre = '';
        $this->descripcion = '';
        $this->icono = '';
        $this->criterioJson = '';
    }
}
