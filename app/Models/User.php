<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'email', 'prefix', 'first_name', 'last_name', 'gender',
        'profile_image_url', 'is_active', 'username', 'password_hash', 'role'
    ];

    protected $hidden = ['password_hash'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}