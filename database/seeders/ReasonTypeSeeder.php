<?php

namespace Database\Seeders;

use App\Models\ReasonType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReasonTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reasonTypesData = [
            // General Void Reasons
            ['name' => 'void'], // Generic void - keep it for general voids
            ['name' => 'incorrect_entry'], // Incorrect item or amount entered
            ['name' => 'customer_request_void'], // Customer changed their mind immediately
            ['name' => 'transaction_error'], // Technical issue during transaction

            // Cancel Reasons (Orders or Items)
            ['name' => 'cancel'], // Generic cancel - keep it for general cancellations
            ['name' => 'customer_request_cancel'], // Customer requested cancellation
            ['name' => 'out_of_stock'], // Item(s) no longer available
            ['name' => 'processing_delay'], // Order processing taking too long
            ['name' => 'shipping_issue'], // Problem with shipping address or method
            ['name' => 'pricing_error_cancel'], // Price was incorrectly displayed at point of sale

            // Refund Reasons (After a sale has occurred)
            ['name' => 'refund'], // Generic refund - keep it for general refunds
            ['name' => 'customer_return'], // Item returned by customer
            ['name' => 'damaged_item'], // Item arrived damaged
            ['name' => 'defective_item'], // Item was faulty or defective
            ['name' => 'quality_issue'], // Customer dissatisfied with item quality
            ['name' => 'pricing_error_refund'], // Price was incorrectly charged initially
            ['name' => 'duplicate_transaction'], // Customer was charged twice in error
            ['name' => 'partial_refund'], // For situations where only a portion of the amount is refunded
            ['name' => 'discount_adjustment'], // Refund to apply a missed discount or correct discount error

            // Other possible reasons (you can customize/add more as needed)
            ['name' => 'manager_override'], // Void/Cancel/Refund authorized by a manager
            ['name' => 'training_purpose'], // Transaction was a test or for training purposes
            ['name' => 'other'], // For reasons not specifically listed (use sparingly, encourage more specific reasons)
        ];

        foreach ($reasonTypesData as $reasonTypeData) {
            $reasonTypeName = $reasonTypeData['name'];

            // Check if a reason type with the same name already exists
            $existingReasonType = ReasonType::where('name', $reasonTypeName)->first();

            if (!$existingReasonType) {
                // If the reason type doesn't exist, create it
                ReasonType::create($reasonTypeData);
                $this->command->info("Reason Type '{$reasonTypeName}' seeded successfully.");
            } else {
                $this->command->info("Reason Type '{$reasonTypeName}' already exists. Skipping.");
            }
        }
    }
}