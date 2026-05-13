<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerfilEmprendedor extends Model
{
    use HasFactory;

    protected $table = 'perfiles_emprendedores';

    protected $fillable = [
        'usuario_id',
        'categoria_id',
        'nombre_emprendimiento',
        'historia',
        'foto_perfil_url',
        'portada_url',
        'direccion',
        'ciudad',
        'pais',
        'sitio_web',
        'redes_sociales',
        'estado_aprobacion',
        'acepta_donaciones',
    ];

    protected function casts(): array
    {
        return [
            'redes_sociales' => 'array',
            'acepta_donaciones' => 'boolean',
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
        return $this->hasMany(Producto::class, 'perfil_emprendedor_id');
    }
}
