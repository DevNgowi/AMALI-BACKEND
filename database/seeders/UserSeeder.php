<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ensure the "Admin" role exists
        $adminRole = Role::firstOrCreate(['name' => 'Super Admin']);

        // Assign all permissions to the "Admin" role
        $adminRole->syncPermissions(Permission::all());

        // Create users with the new schema
        $users = [
            [
                'fullname' => 'jackson ngowi',
                'username' => 'ngowi',
                'email' => 'ngowi@japango.co.tz',
                'phone' => '0621672603',
                'password' => Hash::make('12345678'),
                'pin' => 1234,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'fullname' => 'SAMIRA',
                'username' => 'cashier-samira',
                'email' => 'cashier@mohalal.co.tz',
                'phone' => '12345',
                'password' => Hash::make('12345'),
                'pin' => 5678,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Insert users and assign roles/permissions
        foreach ($users as $userData) {
            // Check if user already exists based on email
            if (!User::where('email', $userData['email'])->exists()) {
                $user = User::create($userData);
                
                // Assign the "Admin" role to these users
                $user->assignRole($adminRole);
                
                // Give all permissions
                $permissions = Permission::all();
                $user->givePermissionTo($permissions);
            }
        }
    }
}