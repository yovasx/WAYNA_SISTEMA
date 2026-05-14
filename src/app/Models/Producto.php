<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'emprendedor_id',
        'categoria_id',
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'estado_stock',
        'foto_principal',
        'qr_codigo',
        'qr_url',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'stock' => 'integer',
            'activo' => 'boolean',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function perfilEmprendedor(): BelongsTo
    {
        return $this->belongsTo(PerfilEmprendedor::class, 'emprendedor_id');
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(ImagenProducto::class, 'producto_id');
    }

    protected function estadoDisponibilidad(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes) => $attributes['estado_stock'] ?? 'disponible',
            set: fn (?string $value) => ['estado_stock' => $value],
        );
    }
}
