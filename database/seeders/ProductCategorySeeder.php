<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // List of product categories
        $categories = [
            ['name' => 'Books'],
            ['name' => 'Clothing'],
            ['name' => 'Electronics'],
            ['name' => 'Home & Garden'],
            ['name' => 'Toys'],
            ['name' => 'Sports & Outdoors'],
            ['name' => 'Health & Beauty'],
            ['name' => 'Automotive'],
            ['name' => 'Food & Beverages'],
            ['name' => 'Furniture'],
            ['name' => 'Produce'],
            ['name' => 'Meat'],
            ['name' => 'Seafood'],
            ['name' => 'Dairy'],
            ['name' => 'Bakery'],
            ['name' => 'Frozen Foods'],
            ['name' => 'Canned Goods'],
            ['name' => 'Pantry Staples'],
            ['name' => 'Beverages'],
            ['name' => 'Snacks'],
            ['name' => 'Deli'],
            ['name' => 'Household Items'],
            ['name' => 'Personal Care'],
            ['name' => 'Baby Products'],
            ['name' => 'Pet Supplies'],
            ['name' => 'General Merchandise'],
        ];

        foreach ($categories as $category) {
            $existingProductCategory = Category::where('name', $category['name'])->first();

            if (!$existingProductCategory) {
                Category::create($category);
            }
        }
    }
}