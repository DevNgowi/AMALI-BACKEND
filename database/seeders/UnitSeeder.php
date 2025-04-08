<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $unitsData = [ // Renamed to $unitsData and changed to array of associative arrays
            // Weight/Mass Units
            ['name' => 'Milligram (MG)'],
            ['name' => 'Gram (G)'],
            ['name' => 'Kilogram (KG)'],
            ['name' => 'Tonne (T)'],
            ['name' => 'Ounce (OZ)'],
            ['name' => 'Pound (LB)'],
            ['name' => 'Stone (ST)'],

            // Volume Units (for liquids, medicine, beverages)
            ['name' => 'Milliliter (ML)'],
            ['name' => 'Centiliter (CL)'],
            ['name' => 'Liter (L)'],
            ['name' => 'Gallon (GAL)'],
            ['name' => 'Teaspoon (TSP)'],
            ['name' => 'Tablespoon (TBSP)'],
            ['name' => 'Cup'],
            ['name' => 'Pint (PT)'],
            ['name' => 'Quart (QT)'],

            // Length Units (for packaging, wrapping, rolls)
            ['name' => 'Millimeter (MM)'],
            ['name' => 'Centimeter (CM)'],
            ['name' => 'Meter (M)'],
            ['name' => 'Inch (IN)'],
            ['name' => 'Foot (FT)'],
            ['name' => 'Yard (YD)'],

            // Packaging & Count Units (common in POS)
            ['name' => 'Piece (PCS)'],
            ['name' => 'Pack (PK)'],
            ['name' => 'Box (BX)'],
            ['name' => 'Bottle (BTL)'],
            ['name' => 'Can (CAN)'],
            ['name' => 'Tube (TUBE)'],
            ['name' => 'Vial (VIAL)'],
            ['name' => 'Ampoule (AMP)'],
            ['name' => 'Carton (CTN)'],
            ['name' => 'Sachet (SACHET)'],

            // Pharmacy-Specific Units (used for medicine dosing)
            ['name' => 'Tablet (TAB)'],
            ['name' => 'Capsule (CAP)'],
            ['name' => 'Syringe (SYR)'],
            ['name' => 'Drop (DROP)'],
            ['name' => 'Strip (STRIP)'],
            ['name' => 'Blister Pack (BLISTER)'],

            // Supermarket-Specific (for bulk items, grains, vegetables, etc.)
            ['name' => 'Dozen (DOZ)'],
            ['name' => 'Bundle (BDL)'],
            ['name' => 'Tray (TRY)'],
            ['name' => 'Roll (ROLL)'],
            ['name' => 'Bar (BAR)'],

            // Miscellaneous
            ['name' => 'Pair (PR)'],
            ['name' => 'Set (SET)'],
            ['name' => 'Unit (UNIT)']
        ];


        foreach ($unitsData as $unitData) {
            $unitName = $unitData['name']; // Extract unit name for clarity

            // Check if a unit with the same name already exists
            $existingUnit = Unit::where('name', $unitName)->first();

            if (!$existingUnit) {
                // If the unit doesn't exist, create it
                Unit::create($unitData);
                $this->command->info("Unit '{$unitName}' seeded successfully."); // Optional: Informative message
            } else {
                $this->command->info("Unit '{$unitName}' already exists. Skipping."); // Optional: Informative message
            }
        }
    }
}
