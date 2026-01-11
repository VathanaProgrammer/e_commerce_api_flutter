<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'email',
        'prefix',
        'first_name',
        'last_name',
        'gender',
        'profile_image_url',
        'is_active',
        'username',
        'password_hash',
        'last_login',
        'role'
    ];

    protected $casts = [
        'last_login' => 'datetime',
    ];

    protected $hidden = ['password_hash'];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getFullNameAttribute()
    {
        return trim(($this->prefix ? $this->prefix . ' ' : '') . $this->first_name . ' ' . $this->last_name);
    }

    public function getProfileImageUrlAttribute($value)
    {
        if ($value && file_exists(public_path($value))) {
            return asset($value);
        }

        return 'https://static.vecteezy.com/system/resources/previews/013/042/571/original/default-avatar-profile-icon-social-media-user-photo-in-flat-style-vector.jpg';
    }

    public function getUserProfile()
    {
        return [
            'id' => $this->id,
            'name' => $this->full_name,
            'email' => $this->email,
            'username' => $this->username,
            'role' => $this->role,
            'gender' => $this->gender,
            'is_active' => $this->is_active,
            'profile_image' => $this->profile_image_url,
        ];
    }
}