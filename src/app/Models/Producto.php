<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'perfil_emprendedor_id',
        'categoria_id',
        'nombre',
        'slug',
        'descripcion',
        'precio',
        'stock',
        'estado_disponibilidad',
        'destacado',
        'codigo_qr_publico',
        'publicado_at',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'stock' => 'integer',
            'destacado' => 'boolean',
            'publicado_at' => 'datetime',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function perfilEmprendedor(): BelongsTo
    {
        return $this->belongsTo(PerfilEmprendedor::class, 'perfil_emprendedor_id');
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(ImagenProducto::class, 'producto_id');
    }
}
