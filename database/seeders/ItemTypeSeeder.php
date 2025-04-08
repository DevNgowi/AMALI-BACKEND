<?php

namespace Database\Seeders;

use App\Models\ItemType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $item_types = [
            ['name' => 'Assets'],
            ['name' => 'Liabilities'],
            ['name' => 'Inventory'],
        ];

        foreach ($item_types as $type) {
            $existingType = ItemType::where('name', $type['name'])->first();

            if (!$existingType) {
                ItemType::create($type);
                $this->command->info("Type '{$type['name']}' seeded successfully.");
            } else {
                $this->command->info("Type '{$type['name']}' already exists. Skipping.");
            }
        }
    }
}
