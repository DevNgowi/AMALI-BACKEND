<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define permissions
        $permissions = [
            // Dashboard
            'can view dashboard',

            // Stores
            'can view store',
            'can edit store',
            'can delete store',
            'can create store',

            // Inventory Management
            'can view item category',
            'can edit item category',
            'can delete item category',
            'can create item category',

            'can view item group',
            'can edit item group',
            'can delete item group',
            'can create item group',

            'can view item type',
            'can edit item type',
            'can delete item type',
            'can create item type',

            'can view item brand',
            'can edit item brand',
            'can delete item brand',
            'can create item brand',

            'can view unit',
            'can edit unit',
            'can delete unit',
            'can create unit',

            'can view items',
            'can edit items',
            'can delete items',
            'can create items',

            'can view cost & stock',
            'can update cost & stock',

            // Purchase Management
            'can view purchase order',
            'can edit purchase order',
            'can delete purchase order',
            'can create purchase order',
            'can view purchase order items',
            'can edit purchase order items',
            'can delete purchase order items',
            'can create purchase order items',
            'approve PO',
            'reject PO',
            'receive PO',
            'complete PO',
            'cancel PO',

            'can view good receive note',
            'can edit good receive note',
            'can delete good receive note',
            'can create good receive note',
            'can view good receive note items',
            'can edit good receive note items',
            'can delete good receive note items',
            'can create good receive note items',
            'verify GRN',
            'reject GRN',
            'accept GRN',
            'complete GRN',
            'reopen GRN',
            'cancel GRN',


            'can view goods returns',
            'can edit goods returns',
            'can delete goods returns',
            'can create goods returns',

            'can view goods issued note',
            'can edit goods issued note',
            'can delete goods issued note',
            'can create goods issued note',

            // POS
            'can view pos',
            'can edit pos',
            'can delete pos',
            'can create pos',

            // User Management
            'can view user role',
            'can edit user role',
            'can delete user role',
            'can create user role',

            'can view users',
            'can edit users',
            'can delete users',
            'can create users',

            'can view permissions',
            'can edit permissions',
            'can delete permissions',
            'can create permissions',

            // Vendor & Finance
            'can view vendors',
            'can edit vendors',
            'can delete vendors',
            'can create vendors',

            'can view payments',
            'can edit payments',
            'can delete payments',
            'can create payments',

            'can view currency',
            'can edit currency',
            'can delete currency',
            'can create currency',

            // Financial Settings
            'can view tax',
            'can edit tax',
            'can delete tax',
            'can create tax',

            'can view extra charges',
            'can edit extra charges',
            'can delete extra charges',
            'can create extra charges',

            'can view discounts',
            'can edit discounts',
            'can delete discounts',
            'can create discounts',

            'can view reason',
            'can edit reason',
            'can delete reason',
            'can create reason',


            // Inventory Reports
            'can preview inventory stock level report',
            'can generate inventory stock level report',
            'can preview inventory stock movement report',
            'can generate inventory stock movement report',
            'can preview inventory low stock report',
            'can generate inventory low stock report',
            'can preview inventory dead stock report',
            'can generate inventory dead stock report',

            // Purchase Reports
            'can preview purchase order history report',
            'can generate purchase order history report',
            'can preview pending purchase orders report',
            'can generate pending purchase orders report',
            'can preview supplier performance report',
            'can generate supplier performance report',
            'can preview supplier payments report',
            'can generate supplier payments report',

            // Sales Reports
            'can preview sales summary report',
            'can generate sales summary report',
            'can preview top selling products report',
            'can generate top selling products report',
            'can preview customer analysis report',
            'can generate customer analysis report',
            'can preview payment methods analysis report',
            'can generate payment methods analysis report',

            // Financial Reports
            'can preview cost of goods sold report',
            'can generate cost of goods sold report',
            'can preview profit margins report',
            'can generate profit margins report',
            'can preview operating expenses report',
            'can generate operating expenses report',
            'can preview payment aging report',
            'can generate payment aging report',

            // Tax Reports
            'can preview sales tax summary report',
            'can generate sales tax summary report',
            'can preview tax collected report',
            'can generate tax collected report',
            'can preview tax payments due report',
            'can generate tax payments due report',

            // Audit Reports
            'can preview price change history report',
            'can generate price change history report',
            'can preview user activity logs report',
            'can generate user activity logs report',
            'can preview system access logs report',
            'can generate system access logs report',

            // Settings
            'can view company details',
            'can edit company details',
            'can create company details',
            'can delete company details',

            //virtual devices
            'can view virtual devices',
            'can edit virtual devices',
            'can delete virtual devices',
            'can create virtual devices',

             //expenses 
             'can view expenses',
             'can edit expenses',
             'can delete expenses',
             'can create expenses',

            //  HR
            'can view employee',
            'can view employee list',
            'can view designation',
            'can view payroll',
            'can view month target',
            'can view recover target',
            'can view sales commission',
            'can view month salary generated',
            'can view attendance',
            'can view leave management',
            'can view performance review',
            

            // Customers 
            'can view customer',
            'can create customer',
            'can edit customer',
            'can delete customer',
        ];

        foreach ($permissions as $permission) {
            $existingPermission = Permission::where('name', $permission)->first();

            if (!$existingPermission) {
                Permission::create(['name' => $permission]);
            }
        }

    }
}
