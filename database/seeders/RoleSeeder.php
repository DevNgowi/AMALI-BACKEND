<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $guard = 'web';

        // Define roles
        $roles = [
            'Super Admin',
            'Admin',
            'Manager',
            'Cashier',
            'Customer Support',
            'Inventory Manager',
            'Accountant',
        ];

        // Define permissions for each role
        $permissionsByRole = [
            'Manager' => [
                'can view store', 'can edit store', 'can create store',
                'can view item category', 'can view item group', 'can view item type', 'can view items',
                'can view purchase order', 'can view good receive note',
                'can view users', 'can view user role',
                'can view vendors', 'can view payments', 'can view tax', 'can view discounts',
                'can view reports'
            ],
            'Cashier' => [
                'can view pos', 'can create pos',
                'can view purchase order', 'can view items'
            ],
            'Customer Support' => [
                'can view users', 'can view vendors', 'can view payments'
            ],
            'Inventory Manager' => [
                'can view item category', 'can edit item category', 'can create item category', 'can delete item category',
                'can view item group', 'can edit item group', 'can create item group', 'can delete item group',
                'can view item type', 'can edit item type', 'can create item type', 'can delete item type',
                'can view unit', 'can edit unit', 'can create unit', 'can delete unit',
                'can view items', 'can edit items', 'can create items', 'can delete items',
                'can view cost & stock',
                'can view purchase order', 'can view good receive note', 'can view goods returns', 'can view goods issued note'
            ],
            'Accountant' => [
                'can view tax', 'can edit tax', 'can view extra charges', 'can edit extra charges',
                'can view discounts', 'can edit discounts', 'can view payments', 'can edit payments',
                'can view vendors', 'can view currency', 'can edit currency',
                'can view reports', 'can view company details'
            ]
        ];

        // Ensure all permissions exist before assigning
        $allPermissions = [
            ...array_merge(...array_values($permissionsByRole))
        ];

        foreach ($allPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => $guard]);
        }

        // Create roles and assign permissions
        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);

            if ($roleName === 'Super Admin') {
                $role->syncPermissions(Permission::all());
            } elseif (isset($permissionsByRole[$roleName])) {
                $role->syncPermissions($permissionsByRole[$roleName]);
            }
        }
    }
}
