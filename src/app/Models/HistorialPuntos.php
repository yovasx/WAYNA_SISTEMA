<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialPuntos extends Model
{
    use HasFactory;

    protected $table = 'historial_puntos';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'donacion_id',
        'puntos_ganados',
        'multiplicador',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'puntos_ganados' => 'integer',
            'multiplicador' => 'decimal:1',
            'created_at' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function donacion(): BelongsTo
    {
        return $this->belongsTo(Donacion::class, 'donacion_id');
    }
}
