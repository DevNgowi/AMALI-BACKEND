<?php

namespace Database\Seeders;

use App\Models\ItemGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run(): void
    {
        $item_groups = [
            ['name' => 'Fresh Produce'],           // Includes Fruits, Vegetables
            ['name' => 'Meat & Seafood'],         // Includes Beef, Poultry, Pork, Seafood
            ['name' => 'Dairy & Eggs'],           // Includes Milk & Cream, Cheese, Yogurt, Eggs
            ['name' => 'Bakery'],                 // Includes Bread & Rolls, Pastries & Cakes, Cookies & Biscuits
            ['name' => 'Pantry Staples'],         // Includes Grains & Rice, Pasta & Noodles, Canned Goods, Condiments & Sauces, Spices & Herbs, Oils & Vinegars
            ['name' => 'Snacks'],                 // Includes Chips & Pretzels, Crackers, Nuts & Seeds
            ['name' => 'Beverages'],              // Includes Soft Drinks, Juices, Water, Coffee & Tea, Alcoholic Beverages (Beer, Wine, Spirits)
            ['name' => 'Frozen Foods'],           // Includes Frozen Meals, Frozen Vegetables, Frozen Desserts
            ['name' => 'Breakfast Foods'],        // Includes Cereal & Granola, Pancake Mixes & Syrups, Jams & Spreads
            ['name' => 'Baby Care'],              // Includes Diapers & Wipes, Baby Food, Formula
            ['name' => 'Personal Care'],          // Includes Shampoo & Conditioner, Soap & Body Wash, Oral Care, Skin Care
            ['name' => 'Household Cleaning'],     // Includes Laundry Detergents, Dish Soap, Cleaning Supplies
            ['name' => 'Paper Products'],         // Includes Toilet Paper, Paper Towels, Tissues
            ['name' => 'Pet Care'],               // Includes Pet Food, Pet Supplies
            ['name' => 'Pharmacy'],               // Includes Over-the-Counter Medicines, First Aid
            ['name' => 'Home & Kitchen'],         // Includes Kitchenware, Home Goods
            ['name' => 'Seasonal Items'],         // Includes Greeting Cards & Stationery, seasonal products
            ['name' => 'Health & Wellness'],      // Includes Vitamins & Supplements, Sports Nutrition
            ['name' => 'Organic & Natural Foods'],
            ['name' => 'International Foods'],
            ['name' => 'Ready Meals'],            // Includes Deli & Prepared Foods, Salads & Sandwiches, Hot Foods
            ['name' => 'Party Supplies'],
        ];

        foreach ($item_groups as $group) {
            $existingItemGroup = ItemGroup::where('name', $group['name'])->first();

            if (!$existingItemGroup) {
                ItemGroup::create($group);
                $this->command->info("Item group '{$group['name']}' seeded successfully.");
            } else {
                $this->command->info("Item group '{$group['name']}' already exists. Skipping.");
            }
        }
    }
}
