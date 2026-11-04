<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'user_type', 'nip', 'name', 'email', 'google_id', 'password', 'phone_wa',
        'photo_path', 'birth_date', 'nik', 'job_title', 'staff_category',
        'organization_name', 'status', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token', 'nik'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'nik' => 'encrypted',
            'birth_date' => 'date',
            'last_login_at' => 'datetime',
        ];
    }

    public function placements(): HasMany
    {
        return $this->hasMany(UserUnit::class);
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'user_units')
            ->withPivot(['position', 'is_primary', 'start_date', 'end_date'])
            ->wherePivotNull('end_date');
    }

    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function isStaff(): bool
    {
        return $this->user_type === 'staff';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
