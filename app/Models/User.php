<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory,Notifiable;

    protected $fillable = ['name', 'email', 'password', 'is_admin', 'role', 'active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed', 'is_admin' => 'boolean', 'active' => 'boolean'];
    }

    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor' || $this->is_admin;
    }

    public function workTeams(): BelongsToMany
    {
        return $this->belongsToMany(WorkTeam::class, 'team_user')->withPivot('is_leader')->withTimestamps();
    }

    public function workLogs(): HasMany
    {
        return $this->hasMany(WorkLog::class, 'created_by');
    }
}
