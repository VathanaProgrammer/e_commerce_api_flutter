<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Business
        |--------------------------------------------------------------------------
        */
        $businessId = DB::table('businesses')->insertGetId([
            'name' => 'Codefy',
            'logo' => null,
            'mobile' => '012345678',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */
        $adminId = DB::table('users')->insertGetId([
            'email' => 'admin@example.com',
            'prefix' => 'Mr',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'gender' => 'male',
            'profile_image_url' => null,
            'is_active' => true,
            'username' => 'admin',
            'password_hash' => Hash::make('123'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */
        $electronicsId = DB::table('categories')->insertGetId([
            'name' => 'Tech Accessories',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}