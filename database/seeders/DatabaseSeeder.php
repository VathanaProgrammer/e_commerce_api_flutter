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
            'name' => 'LUXE',
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

        $customerId = DB::table('users')->insertGetId([
            'email' => 'customer@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'password_hash' => Hash::make('123'),
            'role' => 'customer',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */
        $electronicsId = DB::table('categories')->insertGetId([
            'name' => 'Electronics',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $clothingId = DB::table('categories')->insertGetId([
            'name' => 'Clothing',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */
        $productId = DB::table('products')->insertGetId([
            'category_id' => $electronicsId,
            'name' => 'Smart Phone',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Product Description Lines
        |--------------------------------------------------------------------------
        */
        DB::table('product_description_lines')->insert([
            [
                'product_id' => $productId,
                'text' => '6.5 inch AMOLED display',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $productId,
                'text' => '5000mAh battery',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Attributes + Values
        |--------------------------------------------------------------------------
        */
        $colorAttrId = DB::table('attributes')->insertGetId([
            'name' => 'Color',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $blackValueId = DB::table('attribute_values')->insertGetId([
            'attribute_id' => $colorAttrId,
            'value' => 'Black',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Product Variants
        |--------------------------------------------------------------------------
        */
        $variantId = DB::table('product_variants')->insertGetId([
            'product_id' => $productId,
            'sku' => 'PHONE-BLACK',
            'price' => 299.99,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('product_variant_attributes')->insert([
            'product_variant_id' => $variantId,
            'attribute_value_id' => $blackValueId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Transaction + Sale Line
        |--------------------------------------------------------------------------
        */
        $transactionId = DB::table('transactions')->insertGetId([
            'user_id' => $customerId,
            'total_sell_price' => 299.99,
            'total_items' => 1,
            'status' => 'completed',
            'invoice_no' => 'INV-0001',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('transaction_sale_lines')->insert([
            'transaction_id' => $transactionId,
            'product_variant_id' => $variantId,
            'price' => 299.99,
            'qty' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Payment
        |--------------------------------------------------------------------------
        */
        DB::table('payments')->insert([
            'transaction_id' => $transactionId,
            'amount' => 299.99,
            'method' => 'cash',
            'status' => 'completed',
            'paid_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}