<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre_completo',
        'name',
        'email',
        'telefono',
        'password_hash',
        'password',
        'foto_perfil',
        'estado',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    public function perfilEmprendedor(): HasOne
    {
        return $this->hasOne(PerfilEmprendedor::class, 'usuario_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'usuario_roles', 'usuario_id', 'rol_id')
            ->withPivot(['activo', 'asignado_en']);
    }

    public function productos(): HasManyThrough
    {
        return $this->hasManyThrough(Producto::class, PerfilEmprendedor::class, 'usuario_id', 'emprendedor_id');
    }

    public function tieneRol(string ...$roles): bool
    {
        $mapa = [
            'admin' => 'ADMINISTRADOR',
            'administrador' => 'ADMINISTRADOR',
            'usuario' => 'COMPRADOR',
            'comprador' => 'COMPRADOR',
            'cliente' => 'COMPRADOR',
            'emprendedor' => 'EMPRENDEDOR',
            'donador' => 'DONADOR',
            'turista' => 'TURISTA',
        ];

        $rolesActivos = $this->roles()
            ->wherePivot('activo', true)
            ->pluck('nombre')
            ->map(fn (string $nombre) => strtoupper($nombre))
            ->all();

        foreach ($roles as $rol) {
            if (in_array($mapa[$rol] ?? strtoupper($rol), $rolesActivos, true)) {
                return true;
            }
        }

        return false;
    }

    public function asignarRol(string $rolNombre, bool $activo = true): void
    {
        $rol = Role::query()->firstOrCreate([
            'nombre' => strtoupper($rolNombre),
        ]);

        $this->roles()->syncWithoutDetaching([
            $rol->id => [
                'activo' => $activo,
                'asignado_en' => now(),
            ],
        ]);
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes) => $attributes['nombre_completo'] ?? null,
            set: fn (?string $value) => ['nombre_completo' => $value],
        );
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes) => $attributes['password_hash'] ?? null,
            set: fn (?string $value) => ['password_hash' => $value],
        );
    }

    protected function fotoPerfil(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes) => $attributes['foto_perfil'] ?? null,
            set: fn (?string $value) => ['foto_perfil' => $value],
        );
    }

    protected function rol(): Attribute
    {
        return Attribute::make(
            get: function () {
                $rol = $this->roles()->wherePivot('activo', true)->value('nombre')
                    ?? $this->roles()->value('nombre');

                return $rol ? strtolower($rol) : null;
            },
        );
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
        ];
    }
}
