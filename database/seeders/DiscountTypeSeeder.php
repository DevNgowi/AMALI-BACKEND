<?php

namespace Database\Seeders;

use App\Models\DiscountType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiscountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $typesData = [ // Renamed to $typesData to differentiate from the model class name
            ['name' => 'Percentage', 'description' => 'Percentage off the total amount'],
            ['name' => 'Fixed Amount', 'description' => 'Fixed amount off the total'],
            ['name' => 'Buy X Get Y', 'description' => 'Buy X items get Y items free'],
        ];

        foreach ($typesData as $type) {
            // Check if a discount type with the same name already exists
            $existingDiscountType = DiscountType::where('name', $type['name'])->first();

            if (!$existingDiscountType) {
                // If the discount type doesn't exist, create it
                DiscountType::create($type);
                $this->command->info("Discount Type '{$type['name']}' seeded successfully."); // Optional: Informative message
            } else {
                $this->command->info("Discount Type '{$type['name']}' already exists. Skipping."); // Optional: Informative message
            }
        }
    }
}