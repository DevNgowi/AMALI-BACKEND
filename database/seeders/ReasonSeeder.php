<?php

namespace Database\Seeders;

use App\Models\Reason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Reason::create(['name' => 'Customer Cancelled']);
        Reason::create(['name' => 'Out of Stock']);
        Reason::create(['name' => 'Incorrect Order']);
    }
}
