<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerfilEmprendedor extends Model
{
    use HasFactory;

    protected $table = 'emprendedores';

    protected $fillable = [
        'usuario_id',
        'categoria_id',
        'nombre_negocio',
        'nombre_emprendimiento',
        'descripcion',
        'historia',
        'foto_portada',
        'logo_url',
        'video_url',
        'redes_sociales',
        'tiene_local_fisico',
        'ciudad',
        'direccion_calle',
        'direccion_numero',
        'latitud',
        'longitud',
        'portada_url',
        'nit',
        'estado',
        'estado_aprobacion',
        'aprobado_por',
        'aprobado_en',
    ];

    protected function casts(): array
    {
        return [
            'aprobado_en' => 'datetime',
            'redes_sociales' => 'array',
            'tiene_local_fisico' => 'boolean',
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'emprendedor_id');
    }

    protected function nombreEmprendimiento(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes) => $attributes['nombre_negocio'] ?? null,
            set: fn (?string $value) => ['nombre_negocio' => $value],
        );
    }

    protected function portadaUrl(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes) => $attributes['foto_portada'] ?? null,
            set: fn (?string $value) => ['foto_portada' => $value],
        );
    }

    protected function fotoPerfilUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->usuario?->foto_perfil,
        );
    }

    protected function estadoAprobacion(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes) => match ($attributes['estado'] ?? null) {
                'activo' => 'aprobado',
                'suspendido' => 'suspendido',
                default => 'pendiente',
            },
            set: fn (?string $value) => ['estado' => match ($value) {
                'aprobado', 'activo' => 'activo',
                'suspendido' => 'suspendido',
                default => 'pendiente',
            }],
        );
    }

    protected function direccionFormateada(): Attribute
    {
        return Attribute::make(
            get: function () {
                $partes = array_filter([
                    trim((string) ($this->direccion_calle ?? '')),
                    trim((string) ($this->direccion_numero ?? '')),
                ], fn (string $valor) => $valor !== '');

                $direccion = implode(' ', $partes);

                if ($direccion !== '' && $this->ciudad) {
                    return $direccion.', '.$this->ciudad;
                }

                if ($direccion !== '') {
                    return $direccion;
                }

                return $this->ciudad ?: null;
            },
        );
    }
}
