<?php

namespace Database\Seeders;

use App\Models\CustomerType;
use Illuminate\Database\Seeder;

class CustomerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            'Walk In',
            'Registered', 
            'Sundry Debtors',
            'Regular', 
            'Corporate',
        ];

        foreach ($customers as $customer) {
            if (!CustomerType::where('name', $customer)->where('is_active', 1)->exists()) {
                CustomerType::create([
                    'name' => $customer,
                    'is_active' => 1
                ]);
            }
        }
    }
}
