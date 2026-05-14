<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaccion extends Model
{
    use HasFactory;

    protected $table = 'transacciones';

    public $timestamps = false;

    protected $fillable = [
        'referencia_id',
        'referencia_tipo',
        'usuario_id',
        'metodo_pago',
        'monto',
        'estado',
        'codigo_qr',
        'respuesta_pasarela',
        'procesado_en',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'respuesta_pasarela' => 'array',
            'procesado_en' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function referencia(): MorphTo
    {
        return $this->morphTo('referencia', 'referencia_tipo', 'referencia_id');
    }
}
