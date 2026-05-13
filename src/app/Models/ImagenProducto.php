<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImagenProducto extends Model
{
    use HasFactory;

    protected $table = 'imagenes_producto';

    protected $fillable = [
        'producto_id',
        'url',
        'texto_alternativo',
        'orden',
        'es_principal',
    ];

    protected function casts(): array
    {
        return [
            'es_principal' => 'boolean',
            'orden' => 'integer',
        ];
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
