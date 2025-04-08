<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ItemGroup; // Adjust namespace if different
use App\Models\ItemCategory; // Adjust namespace if different
use Illuminate\Database\Seeder;

class ItemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define item groups and their categories
        $groupCategories = [
            'Fresh Produce' => [
                'Fruits',
                'Vegetables',
                'Herbs',
            ],
            'Meat & Seafood' => [
                'Beef',
                'Poultry',
                'Pork',
                'Seafood',
                'Lamb',
            ],
            'Dairy & Eggs' => [
                'Milk & Cream',
                'Cheese',
                'Yogurt',
                'Eggs',
                'Butter & Margarine',
            ],
            'Bakery' => [
                'Bread & Rolls',
                'Pastries & Cakes',
                'Cookies & Biscuits',
                'Donuts',
            ],
            'Pantry Staples' => [
                'Grains & Rice',
                'Pasta & Noodles',
                'Canned Goods',
                'Condiments & Sauces',
                'Spices & Herbs',
                'Oils & Vinegars',
            ],
            'Snacks' => [
                'Chips & Pretzels',
                'Crackers',
                'Nuts & Seeds',
                'Popcorn',
            ],
            'Beverages' => [
                'Soft Drinks',
                'Juices',
                'Water',
                'Coffee & Tea',
                'Alcoholic Beverages',
            ],
            'Frozen Foods' => [
                'Frozen Meals',
                'Frozen Vegetables',
                'Frozen Desserts',
                'Ice Cream',
            ],
            'Breakfast Foods' => [
                'Cereal & Granola',
                'Pancake Mixes & Syrups',
                'Jams & Spreads',
                'Oatmeal',
            ],
            'Baby Care' => [
                'Diapers & Wipes',
                'Baby Food',
                'Formula',
                'Baby Accessories',
            ],
            'Personal Care' => [
                'Shampoo & Conditioner',
                'Soap & Body Wash',
                'Oral Care',
                'Skin Care',
                'Hair Care',
            ],
            'Household Cleaning' => [
                'Laundry Detergents',
                'Dish Soap',
                'Cleaning Supplies',
                'Air Fresheners',
            ],
            'Paper Products' => [
                'Toilet Paper',
                'Paper Towels',
                'Tissues',
                'Napkins',
            ],
            'Pet Care' => [
                'Pet Food',
                'Pet Treats',
                'Pet Supplies',
            ],
            'Pharmacy' => [
                'Over-the-Counter Medicines',
                'First Aid',
                'Pain Relief',
            ],
            'Home & Kitchen' => [
                'Kitchenware',
                'Cookware',
                'Home Goods',
            ],
            'Seasonal Items' => [
                'Greeting Cards & Stationery',
                'Holiday Decorations',
                'Seasonal Snacks',
            ],
            'Health & Wellness' => [
                'Vitamins & Supplements',
                'Sports Nutrition',
                'Weight Management',
            ],
            'Organic & Natural Foods' => [
                'Organic Snacks',
                'Natural Beverages',
                'Gluten-Free Products',
            ],
            'International Foods' => [
                'Asian Foods',
                'Mexican Foods',
                'European Foods',
            ],
            'Ready Meals' => [
                'Deli Meats',
                'Prepared Salads',
                'Hot Foods',
            ],
            'Party Supplies' => [
                'Plates & Cups',
                'Decorations',
                'Balloons',
            ],
        ];

        // Seed categories for each group
        foreach ($groupCategories as $groupName => $categories) {
            // Find the group by name
            $group = ItemGroup::where('name', $groupName)->first();

            if ($group) {
                foreach ($categories as $categoryName) {
                    $existingCategory = Category::where('name', $categoryName)
                        ->where('item_group_id', $group->id)
                        ->first();

                    if (!$existingCategory) {
                        Category::create([
                            'name' => $categoryName,
                            'item_group_id' => $group->id,
                        ]);
                        $this->command->info("Category '{$categoryName}' seeded for group '{$groupName}'.");
                    } else {
                        $this->command->info("Category '{$categoryName}' already exists for group '{$groupName}'. Skipping.");
                    }
                }
            } else {
                $this->command->warn("Group '{$groupName}' not found. Skipping its categories.");
            }
        }
    }
}