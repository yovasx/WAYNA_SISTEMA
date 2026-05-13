<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'rol', 'estado', 'email_verified_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public function perfilEmprendedor(): HasOne
    {
        return $this->hasOne(PerfilEmprendedor::class, 'usuario_id');
    }

    public function productos(): HasManyThrough
    {
        return $this->hasManyThrough(Producto::class, PerfilEmprendedor::class, 'usuario_id', 'perfil_emprendedor_id');
    }

    public function tieneRol(string ...$roles): bool
    {
        $mapa = [
            'admin' => 'administrador',
            'administrador' => 'administrador',
            'usuario' => 'comprador',
            'comprador' => 'comprador',
            'cliente' => 'comprador',
            'emprendedor' => 'emprendedor',
            'donador' => 'donador',
        ];

        $rolActual = $mapa[$this->rol] ?? $this->rol;

        foreach ($roles as $rol) {
            if (($mapa[$rol] ?? $rol) === $rolActual) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
