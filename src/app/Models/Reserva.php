<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';

    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'usuario_id',
        'emprendedor_id',
        'tipo',
        'producto_id',
        'fecha_reserva',
        'hora_reserva',
        'estado',
        'qr_acceso',
        'notas',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'fecha_reserva' => 'date',
            'hora_reserva' => 'string',
            'created_at' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function emprendedor(): BelongsTo
    {
        return $this->belongsTo(PerfilEmprendedor::class, 'emprendedor_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
