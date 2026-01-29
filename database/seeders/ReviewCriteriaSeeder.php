<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ReviewCriterion;

class ReviewCriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $criteria = [
            [
                'name' => 'Quality',
                'slug' => 'quality',
                'description' => 'Overall quality and craftsmanship of the product',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Value for Money',
                'slug' => 'value-for-money',
                'description' => 'Whether the product offers good value for its price',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Appearance',
                'slug' => 'appearance',
                'description' => 'Visual appeal and design of the product',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Durability',
                'slug' => 'durability',
                'description' => 'How well the product holds up over time',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Ease of Use',
                'slug' => 'ease-of-use',
                'description' => 'How easy the product is to use and operate',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($criteria as $criterion) {
            ReviewCriterion::firstOrCreate(
                ['slug' => $criterion['slug']],
                $criterion
            );
        }
    }
}
