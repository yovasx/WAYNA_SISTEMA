<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insignia extends Model
{
    use HasFactory;

    protected $table = 'insignias';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'icono',
        'criterio_json',
    ];

    protected function casts(): array
    {
        return [
            'criterio_json' => 'array',
        ];
    }
}
