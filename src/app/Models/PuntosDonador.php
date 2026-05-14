<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PuntosDonador extends Model
{
    use HasFactory;

    protected $table = 'puntos_donador';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'puntos_total',
        'nivel',
    ];

    protected function casts(): array
    {
        return [
            'puntos_total' => 'integer',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
