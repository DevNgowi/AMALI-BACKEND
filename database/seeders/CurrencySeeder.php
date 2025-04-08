<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Currency; // Assuming you have a Currency model

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currenciesData = [ // Renamed to $currenciesData to differentiate from the model
            [
                'country_id' => 1, // Example country ID - replace with actual country IDs
                'name' => 'US Dollar',
                'sign' => '$',
                'sign_placement' => 'before',
                'currency_name_in_words' => 'United States Dollar',
                'digits_after_decimal' => 2,
            ],
            [
                'country_id' => 2, // Example country ID - replace with actual country IDs
                'name' => 'Euro',
                'sign' => '€',
                'sign_placement' => 'before',
                'currency_name_in_words' => null, // Example of nullable field
                'digits_after_decimal' => 2,
            ],
            [
                'country_id' => 3, // Example country ID - replace with actual country IDs
                'name' => 'Tanzanian Shilling',
                'sign' => 'TZS',
                'sign_placement' => 'before',
                'currency_name_in_words' => 'Tanzanian Shilling',
                'digits_after_decimal' => 0, // Example of different digits after decimal
            ],
            [
                'country_id' => 4, // Example country ID - replace with actual country IDs
                'name' => 'British Pound',
                'sign' => '£',
                'sign_placement' => 'before',
                'currency_name_in_words' => 'Pound Sterling',
                'digits_after_decimal' => 2,
            ],
            [
                'country_id' => 5, // Example country ID - replace with actual country IDs
                'name' => 'Canadian Dollar',
                'sign' => 'CA$',
                'sign_placement' => 'before',
                'currency_name_in_words' => 'Canadian Dollar',
                'digits_after_decimal' => 2,
            ],
            [
                'country_id' => 6, // Example country ID - replace with actual country IDs
                'name' => 'Australian Dollar',
                'sign' => 'AU$',
                'sign_placement' => 'before',
                'currency_name_in_words' => 'Australian Dollar',
                'digits_after_decimal' => 2,
            ],
            [
                'country_id' => 7, // Example country ID - replace with actual country IDs
                'name' => 'Japanese Yen',
                'sign' => '¥',
                'sign_placement' => 'before',
                'currency_name_in_words' => 'Japanese Yen',
                'digits_after_decimal' => 0,
            ],
            [
                'country_id' => 8, // Example country ID - replace with actual country IDs
                'name' => 'Swiss Franc',
                'sign' => 'CHF',
                'sign_placement' => 'before',
                'currency_name_in_words' => 'Swiss Franc',
                'digits_after_decimal' => 2,
            ],
            [
                'country_id' => 9, // Example country ID - replace with actual country IDs
                'name' => 'Indian Rupee',
                'sign' => '₹',
                'sign_placement' => 'before',
                'currency_name_in_words' => 'Indian Rupee',
                'digits_after_decimal' => 2,
            ],
            [
                'country_id' => 10, // Example country ID - replace with actual country IDs
                'name' => 'South African Rand',
                'sign' => 'R',
                'sign_placement' => 'before',
                'currency_name_in_words' => 'South African Rand',
                'digits_after_decimal' => 2,
            ],
            [
                'country_id' => 11, // Example country ID - replace with actual country IDs
                'name' => 'Brazilian Real',
                'sign' => 'R$',
                'sign_placement' => 'before',
                'currency_name_in_words' => 'Brazilian Real',
                'digits_after_decimal' => 2,
            ],
            [
                'country_id' => 12, // Example country ID - replace with actual country IDs
                'name' => 'Chinese Yuan',
                'sign' => '¥', // Or 'CN¥'
                'sign_placement' => 'before',
                'currency_name_in_words' => 'Chinese Yuan',
                'digits_after_decimal' => 2,
            ],
            [
                'country_id' => 13, // Example country ID - replace with actual country IDs
                'name' => 'Russian Ruble',
                'sign' => '₽',
                'sign_placement' => 'before',
                'currency_name_in_words' => 'Russian Ruble',
                'digits_after_decimal' => 2,
            ],
            [
                'country_id' => 14, // Example country ID - replace with actual country IDs
                'name' => 'Mexican Peso',
                'sign' => '$',
                'sign_placement' => 'before',
                'currency_name_in_words' => 'Mexican Peso',
                'digits_after_decimal' => 2,
            ],
            [
                'country_id' => 15, // Example country ID - replace with actual country IDs
                'name' => 'Singapore Dollar',
                'sign' => 'S$',
                'sign_placement' => 'before',
                'currency_name_in_words' => 'Singapore Dollar',
                'digits_after_decimal' => 2,
            ],
            // Add more currencies if needed, following the same structure
        ];

        foreach ($currenciesData as $currency) {
            // Check if a currency with the same name already exists
            $existingCurrency = Currency::where('name', $currency['name'])->first();

            if (!$existingCurrency) {
                // If the currency doesn't exist, create it
                Currency::create($currency);
                $this->command->info("Currency '{$currency['name']}' seeded successfully."); // Optional: Informative message
            } else {
                $this->command->info("Currency '{$currency['name']}' already exists. Skipping."); // Optional: Informative message
            }
        }
    }
}