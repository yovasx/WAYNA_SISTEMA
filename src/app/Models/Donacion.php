<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donacion extends Model
{
    use HasFactory;

    protected $table = 'donaciones';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'emprendedor_id',
        'monto',
        'es_anonima',
        'mensaje',
        'certificado_url',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'es_anonima' => 'boolean',
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
}
