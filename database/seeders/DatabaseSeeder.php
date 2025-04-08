<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Employee, Position, Gender, Country, City, Genre, Category, Currency, Account, Unit, PaymentType, DiscountType, ReasonType};  // Import the models for all seeders

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(CountrySeeder::class); 
        $this->call(CurrencySeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(PaymentTypeSeeder::class);
        $this->call(DiscountTypeSeeder::class);
        $this->call(ReasonTypeSeeder::class);
        $this->call(PositionSeeder::class);
        $this->call(TanzaniaCitySeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ItemTypeSeeder::class); 
        $this->call(ItemGroupSeeder::class); 
        $this->call(ItemCategorySeeder::class); 
        $this->call(CustomerTypeSeeder::class); 
        // $this->call(ReasonSeeder::class);
    }
}