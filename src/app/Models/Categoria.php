<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'icono',
        'descripcion',
        'slug',
        'activa',
    ];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    public function getSlugAttribute(): string
    {
        return Str::slug($this->nombre);
    }

    protected function slug(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::slug($this->nombre),
            set: fn () => [],
        );
    }

    protected function activa(): Attribute
    {
        return Attribute::make(
            get: fn () => true,
            set: fn () => [],
        );
    }
}
