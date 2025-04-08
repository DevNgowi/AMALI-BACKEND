<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define the positions to be seeded
        $positionsData = [ // Renamed to $positionsData for clarity
            [
                'title' => 'Manager',
                'description' => 'Oversees the operations of the department or company.',
                'base_salary' => 60000.00,
            ],
            [
                'title' => 'Developer',
                'description' => 'Responsible for developing and maintaining software applications.',
                'base_salary' => 50000.00,
            ],
            [
                'title' => 'HR Specialist',
                'description' => 'Handles recruitment, employee relations, and performance management.',
                'base_salary' => 45000.00,
            ],
            [
                'title' => 'Sales Representative',
                'description' => 'Generates sales and builds relationships with clients.',
                'base_salary' => 40000.00,
            ],
            [
                'title' => 'Accountant',
                'description' => 'Manages financial records and ensures compliance with tax laws.',
                'base_salary' => 55000.00,
            ],
            [
                'title' => 'Marketing Executive',
                'description' => 'Develops and executes marketing campaigns to promote the company.',
                'base_salary' => 48000.00,
            ],
            // Add more positions as needed
        ];

        // Insert positions into the 'positions' table, checking for duplicates
        foreach ($positionsData as $positionData) {
            $positionTitle = $positionData['title']; // Extract title for clarity

            // Check if a position with the same title already exists
            $existingPosition = Position::where('title', $positionTitle)->first();

            if (!$existingPosition) {
                // If the position doesn't exist, create it
                Position::create($positionData);
                $this->command->info("Position '{$positionTitle}' seeded successfully."); // Optional: Informative message
            } else {
                $this->command->info("Position '{$positionTitle}' already exists. Skipping."); // Optional: Informative message
            }
        }
    }
}