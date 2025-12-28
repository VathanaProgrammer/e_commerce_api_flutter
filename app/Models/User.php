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
        'role'
    ];

    protected $hidden = ['password_hash'];


    public function getAuthPassword()
    {
        return $this->password_hash; // maps Laravel password to your DB column
    }


    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}