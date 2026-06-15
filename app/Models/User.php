<?php
// app/Models/User.php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne; // ← AGREGAR
use App\Models\Role;

/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $last_login_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|Role[] $roles
 * @property-read Role|null $role
 * @property-read \Illuminate\Database\Eloquent\Collection|Diario[] $diarios
 * @property-read Paciente|null $paciente // ← AGREGAR
 *
 * @method bool hasRole(string|Role $role)
 * @method bool hasPermission(string $permission)
 * @method bool hasPermissions(array $permissions)
 * @method bool isAdmin()
 * @method $this assignRole(string|Role $role)
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación muchos a muchos con roles
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * Relación uno a muchos con diarios
     * Un usuario puede tener muchos diarios
     *
     * @return HasMany
     */
    public function diarios(): HasMany
    {
        return $this->hasMany(Diario::class, 'user_id');
    }

    /**
     * NUEVA RELACIÓN: Un usuario puede ser un paciente (1 a 1)
     *
     * @return HasOne
     */
    public function paciente(): HasOne
    {
        return $this->hasOne(Paciente::class);
    }

    /**
     * Verificar si el usuario es un paciente (tiene perfil clínico)
     *
     * @return bool
     */
    public function esPaciente(): bool
    {
        return $this->hasRole('paciente') && $this->paciente !== null;
    }

    /**
     * Obtener el perfil clínico del paciente
     *
     * @return Paciente|null
     */
    public function obtenerPaciente(): ?Paciente
    {
        return $this->paciente;
    }

    /**
     * Verificar si el usuario tiene un rol específico
     *
     * @param string|Role $role
     * @return bool
     */
    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }
        
        if ($role instanceof Role) {
            return $this->roles->contains('id', $role->id);
        }
        
        return !!$role->intersect($this->roles)->count();
    }

    /**
     * Asignar un rol al usuario
     *
     * @param string|Role $role
     * @return $this
     */
    public function assignRole($role): self
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }
        
        $this->roles()->syncWithoutDetaching([$role->id]);
        return $this;
    }

    /**
     * Verificar si el usuario es administrador
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Obtener el primer rol del usuario
     *
     * @return Role|null
     */
    public function getRoleAttribute(): ?Role
    {
        return $this->roles->first();
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Verificar si el usuario tiene varios permisos
     *
     * @param array $permissions
     * @return bool
     */
    public function hasPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Obtener los diarios del usuario para una fecha específica
     *
     * @param string $fecha
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDiariosPorFecha(string $fecha)
    {
        return $this->diarios()->where('fecha', $fecha)->get();
    }

    /**
     * Obtener el último diario del usuario
     *
     * @return Diario|null
     */
    public function getUltimoDiario()
    {
        return $this->diarios()->latest('fecha')->first();
    }
}