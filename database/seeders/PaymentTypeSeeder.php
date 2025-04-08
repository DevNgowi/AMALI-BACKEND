<?php

namespace Database\Seeders;

use App\Models\PaymentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentTypesData = [ // Renamed to $paymentTypesData and changed to array of associative arrays
            ['name' => 'Cash'],
            ['name' => 'Bank'],
            ['name' => 'Mobile Transaction'],
        ];

        foreach ($paymentTypesData as $paymentTypeData) {
            $paymentTypeName = $paymentTypeData['name']; // Extract the name for easier use

            // Check if a payment type with the same name already exists
            $existingPaymentType = PaymentType::where('name', $paymentTypeName)->first();

            if (!$existingPaymentType) {
                // If the payment type doesn't exist, create it
                PaymentType::create($paymentTypeData); // Use the associative array for create
                $this->command->info("Payment Type '{$paymentTypeName}' seeded successfully."); // Optional: Informative message
            } else {
                $this->command->info("Payment Type '{$paymentTypeName}' already exists. Skipping."); // Optional: Informative message
            }
        }
    }
}