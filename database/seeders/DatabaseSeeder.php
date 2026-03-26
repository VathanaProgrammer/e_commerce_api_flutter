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
        | Roles and Permissions
        |--------------------------------------------------------------------------
        */
        $this->call(RolePermissionSeeder::class);

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

        /*
        |--------------------------------------------------------------------------
        | Review Criteria
        |--------------------------------------------------------------------------
        */
        // Review criteria seeder removed
    }
}
