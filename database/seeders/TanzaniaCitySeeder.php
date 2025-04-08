<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\City; // Assuming you have a City model

class TanzaniaCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the country_id for Tanzania
        $tanzania = DB::table('countries')->where('name', 'Tanzania')->first();

        if ($tanzania) {
            // List of cities from Tanzania
            $citiesData = [ // Renamed to $citiesData for clarity
                ['name' => 'Dar es Salaam', 'country_id' => $tanzania->id],
                ['name' => 'Dodoma', 'country_id' => $tanzania->id],
                ['name' => 'Arusha', 'country_id' => $tanzania->id],
                ['name' => 'Mwanza', 'country_id' => $tanzania->id],
                ['name' => 'Mbeya', 'country_id' => $tanzania->id],
                ['name' => 'Zanzibar City', 'country_id' => $tanzania->id],
                ['name' => 'Moshi', 'country_id' => $tanzania->id],
                ['name' => 'Tanga', 'country_id' => $tanzania->id],
                ['name' => 'Morogoro', 'country_id' => $tanzania->id],
                ['name' => 'Shinyanga', 'country_id' => $tanzania->id],
                ['name' => 'Kigoma', 'country_id' => $tanzania->id],
                ['name' => 'Songea', 'country_id' => $tanzania->id],
                ['name' => 'Tabora', 'country_id' => $tanzania->id],
                ['name' => 'Iringa', 'country_id' => $tanzania->id],
                ['name' => 'Rufiji', 'country_id' => $tanzania->id],
                ['name' => 'Geita', 'country_id' => $tanzania->id],
                ['name' => 'Chato', 'country_id' => $tanzania->id],
                ['name' => 'Bukoba', 'country_id' => $tanzania->id],
                ['name' => 'Njombe', 'country_id' => $tanzania->id],
                ['name' => 'Mpanda', 'country_id' => $tanzania->id],
                ['name' => 'Singida', 'country_id' => $tanzania->id],
                ['name' => 'Bagamoyo', 'country_id' => $tanzania->id],
                ['name' => 'Biharamulo', 'country_id' => $tanzania->id],
                ['name' => 'Kasulu', 'country_id' => $tanzania->id],
                ['name' => 'Makambako', 'country_id' => $tanzania->id],
                ['name' => 'Babati', 'country_id' => $tanzania->id],
                ['name' => 'Sumbawanga', 'country_id' => $tanzania->id],
                ['name' => 'Handeni', 'country_id' => $tanzania->id],
                ['name' => 'Kibaha', 'country_id' => $tanzania->id],
                ['name' => 'Kilwa', 'country_id' => $tanzania->id],
                ['name' => 'Mpwapwa', 'country_id' => $tanzania->id],
                ['name' => 'Kondoa', 'country_id' => $tanzania->id],
                ['name' => 'Lindi', 'country_id' => $tanzania->id],
                ['name' => 'Masasi', 'country_id' => $tanzania->id],
                ['name' => 'Newala', 'country_id' => $tanzania->id],
                ['name' => 'Mtwara', 'country_id' => $tanzania->id],
                ['name' => 'Kibondo', 'country_id' => $tanzania->id],
                ['name' => 'Kahama', 'country_id' => $tanzania->id],
                ['name' => 'Nzega', 'country_id' => $tanzania->id],
                ['name' => 'Urambo', 'country_id' => $tanzania->id],
                ['name' => 'Manyoni', 'country_id' => $tanzania->id],
                ['name' => 'Kongwa', 'country_id' => $tanzania->id],
                ['name' => 'Kilosa', 'country_id' => $tanzania->id],
                ['name' => 'Ifakara', 'country_id' => $tanzania->id],
                ['name' => 'Mufindi', 'country_id' => $tanzania->id],
                ['name' => 'Mbinga', 'country_id' => $tanzania->id],
                ['name' => 'Nachingwea', 'country_id' => $tanzania->id],
                ['name' => 'Liuli', 'country_id' => $tanzania->id],
                ['name' => 'Mikumi', 'country_id' => $tanzania->id],
                ['name' => 'Karatu', 'country_id' => $tanzania->id],
                ['name' => 'Monduli', 'country_id' => $tanzania->id],
                ['name' => 'Tarime', 'country_id' => $tanzania->id],
                ['name' => 'Serengeti', 'country_id' => $tanzania->id],
                ['name' => 'Musoma', 'country_id' => $tanzania->id],
                ['name' => 'Rorya', 'country_id' => $tanzania->id],
                ['name' => 'Ngorongoro', 'country_id' => $tanzania->id],
                ['name' => 'Mara', 'country_id' => $tanzania->id],
                ['name' => 'Kiteto', 'country_id' => $tanzania->id],
                ['name' => 'Simanjiro', 'country_id' => $tanzania->id],
                ['name' => 'Longido', 'country_id' => $tanzania->id],
                ['name' => 'Tarangire', 'country_id' => $tanzania->id],
                ['name' => 'Pangani', 'country_id' => $tanzania->id],
                ['name' => 'Muheza', 'country_id' => $tanzania->id],
                ['name' => 'Korogwe', 'country_id' => $tanzania->id],
            ];

            // Insert cities into the 'cities' table, checking for duplicates
            foreach ($citiesData as $cityData) {
                $cityName = $cityData['name'];
                $countryId = $cityData['country_id'];

                // Check if a city with the same name and country_id already exists
                $existingCity = City::where('name', $cityName)
                                    ->where('country_id', $countryId)
                                    ->first();

                if (!$existingCity) {
                    // If the city doesn't exist, create it
                    City::create($cityData);
                    $this->command->info("City '{$cityName}' (Tanzania) seeded successfully.");
                } else {
                    $this->command->info("City '{$cityName}' (Tanzania) already exists. Skipping.");
                }
            }
        } else {
            // Print message to the console if Tanzania is missing
            $this->command->error('Tanzania not found in countries table. Cities not seeded.');
        }
    }
}