<!-- Sidebar -->
<style>
    .sidebar .nav-treeview .nav-item .nav-link {
        margin-left: 18px;
    }
</style>
<div class="sidebar">
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Dashboard -->
            @can('can view dashboard')
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>{{ __('Dashboard') }}</p>
                    </a>
                </li>
            @endcan

            <!-- Stores -->
            @canany(['can view store', 'can edit store', 'can delete store', 'can create store'])
                <li class="nav-item">
                    <a href="{{ route('list_stores') }}" class="nav-link">
                        <i class="nav-icon fas fa-store"></i>
                        <p>Stores</p>
                    </a>
                </li>
            @endcanany

            <!-- Inventory Management -->
            @canany(['can view item category', 'can edit item category', 'can delete item category', 'can create item category',
                      'can view item group', 'can edit item group', 'can delete item group', 'can create item group',
                      'can view item type', 'can edit item type', 'can delete item type', 'can create item type',
                      'can view unit', 'can edit unit', 'can delete unit', 'can create unit',
                      'can view items', 'can edit items', 'can delete items', 'can create items',
                      'can view cost & stock'])
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-warehouse"></i>
                    <p>
                        Inventory
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                    @can('can view item category')
                    <li class="nav-item">
                        <a href="{{ route('list_category') }}" class="nav-link">
                            <i class="fas fa-tags nav-icon"></i>
                            <p>Item Category</p>
                        </a>
                    </li>
                    @endcan
                    @can('can view item group')
                    <li class="nav-item">
                        <a href="{{ route('list_item_group') }}" class="nav-link">
                            <i class="fas fa-layer-group nav-icon"></i>
                            <p>Item Group</p>
                        </a>
                    </li>
                    @endcan
                    @can('can view item type')
                    <li class="nav-item">
                        <a href="{{ route('list_item_type') }}" class="nav-link">
                            <i class="fas fa-boxes nav-icon"></i>
                            <p>Item Type</p>
                        </a>
                    </li>
                    @endcan
                    @can('can view unit')
                    <li class="nav-item">
                        <a href="{{ route('list_unit') }}" class="nav-link">
                            <i class="fas fa-balance-scale nav-icon"></i>
                            <p>Unit</p>
                        </a>
                    </li>
                    @endcan
                    @can('can view items')
                    <li class="nav-item">
                        <a href="{{ route('list_item') }}" class="nav-link">
                            <i class="fas fa-box nav-icon"></i>
                            <p>Items</p>
                        </a>
                    </li>
                    @endcan
                    @can('can view cost & stock')
                    <li class="nav-item">
                        <a href="{{ route('list_cost_stock') }}" class="nav-link">
                            <i class="fas fa-chart-line nav-icon"></i>
                            <p>Cost & Stock</p>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcanany
          
            <!-- Purchase Management -->
            @canany([
                'can view purchase order',
                'can edit purchase order',
                'can delete purchase order',
                'can create
                purchase order',
                'can view good receive note',
                'can edit good receive note',
                'can delete good receive note',
                'can create good receive note',
                'can view goods returns',
                'can edit goods returns',
                'can delete goods
                returns',
                'can create goods returns',
                'can view goods issued note',
                'can edit goods issued note',
                'can
                delete goods issued note',
                'can create goods issued note',
                ])
                <li class="nav-item">
                    <a href="{{ route('list_po') }}" class="nav-link">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Purchases
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        @can('can view purchase order')
                            <li class="nav-item">
                                <a href="{{ route('list_po') }}" class="nav-link">
                                    <i class="fas fa-user-shield nav-icon"></i>
                                    <p>Purchase Order</p>
                                </a>
                            </li>
                        @endcan
                        @can('can view good receive note')
                            <li class="nav-item">
                                <a href="{{ route('list_grn') }}" class="nav-link">
                                    <i class="fas fa-users nav-icon"></i>
                                    <p>Good Receive Note</p>
                                </a>
                            </li>
                        @endcan
                        @can('can view goods returns')
                            <li class="nav-item">
                                <a href="{{ route('list_gr') }}" class="nav-link">
                                    <i class="fas fa-lock nav-icon"></i>
                                    <p>Goods Returns</p>
                                </a>
                            </li>
                        @endcan
                        @can('can view goods issued note')
                            <li class="nav-item">
                                <a href="{{ route('list_gin') }}" class="nav-link">
                                    <i class="fas fa-lock nav-icon"></i>
                                    <p>Goods Issued Note</p>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            <!-- POS -->
            @canany(['can view pos', 'can edit pos', 'can delete pos', 'can create pos'])
                <li class="nav-item">
                    <a href="{{ route('main_pos') }}" class="nav-link">
                        <i class="nav-icon fas fa-cash-register"></i>
                        <p>POS</p>
                    </a>
                </li>
            @endcanany

            <li class="nav-item">
                <a href="{{ route('list_expenses') }}" class="nav-link">
                    <i class="nav-icon fas fa-money-bill-wave"></i>
                    <p>
                        Expenses
                    </p>
                </a>
            </li>

            <!---- HR ----->
            <li class="nav-item">
                <a href="{{ route('list_expenses') }}" class="nav-link">
                    <i class="nav-icon fas fa-university"></i>
                    <p>
                        HR
                    </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">

                    <!-- Employee Section -->
                    @can('can view employee')
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-user-tie nav-icon"></i>
                                <p>
                                    Employee
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" style="display: none;">
                                @can('can view employee list')
                                    <li class="nav-item">
                                        <a href="{{ route('list_employee') }}" class="nav-link">
                                            <i class="fas fa-user-shield nav-icon"></i>
                                            <p>Employee List</p>
                                        </a>
                                    </li>
                                @endcan
                                @can('can view designation')
                                    <li class="nav-item">
                                        <a href="{{ route('list_designation') }}" class="nav-link">
                                            <i class="fas fa-users nav-icon"></i>
                                            <p>Designation</p>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcan

                    <!-- Payroll Section fix -->
                    @can('can view payroll')
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-money-check-alt nav-icon"></i>
                                <p>
                                    Payroll
                                    <i class="fas fa-angle-left right"></i> --
                                </p> 
                            </a>
                            <ul class="nav nav-treeview" style="display: none;">
                                @can('can view month target')
                                    <li class="nav-item">
                                        <a href="{{ route('month_target') }}" class="nav-link">
                                            <i class="fas fa-bullseye nav-icon"></i>
                                            <p>Month Target</p>
                                        </a>
                                    </li>
                                @endcan
                                @can('can view recover target')
                                    <li class="nav-item">
                                        <a href="{{ route('recover_target') }}" class="nav-link">
                                            <i class="fas fa-undo-alt nav-icon"></i>
                                            <p>Recover Target</p>
                                        </a>
                                    </li>
                                @endcan
                                @can('can view sales commission')
                                    <li class="nav-item">
                                        <a href="{{ route('sales_commission') }}" class="nav-link">
                                            <i class="fas fa-percentage nav-icon"></i>
                                            <p>Sales Commission</p>
                                        </a>
                                    </li>
                                @endcan
                                @can('can view month salary generated')
                                    <li class="nav-item">
                                        <a href="{{ route('month_salary_generated') }}" class="nav-link">
                                            <i class="fas fa-dollar-sign nav-icon"></i>
                                            <p>Month Salary Generated</p>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcan

                    @can('can view attendance')
                        <li class="nav-item">
                            <a href="{{ route('attendance') }}" class="nav-link">
                                <i class="fas fa-calendar-check nav-icon"></i>
                                <p>Attendance</p>
                            </a>
                        </li>
                    @endcan

                    @can('can view leave management')
                        <li class="nav-item">
                            <a href="{{ route('leave_management') }}" class="nav-link">
                                <i class="fas fa-plane-departure nav-icon"></i>
                                <p>Leave Management</p>
                            </a>
                        </li>
                    @endcan

                    @can('can view performance review')
                        <li class="nav-item">
                            <a href="{{ route('performance_review') }}" class="nav-link">
                                <i class="fas fa-chart-line nav-icon"></i>
                                <p>Performance Review</p>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>


            <!---- Customer Royality----->
            <li class="nav-item">
                @can('can view customer')
                    <a href="{{ route('list_customer') }}" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            CRM
                        </p>
                    </a>
                @endcan
            </li>

            <!-- User Management -->
            @canany([
                'can view user role',
                'can edit user role',
                'can delete user role',
                'can create user role',
                'can
                view users',
                'can edit users',
                'can delete users',
                'can create users',
                'can view permissions',
                'can edit
                permissions',
                'can delete permissions',
                'can create permissions',
                ])
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>
                            User Management
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        @can('can view user role')
                            <li class="nav-item">
                                <a href="{{ route('list_roles') }}" class="nav-link">
                                    <i class="fas fa-user-shield nav-icon"></i>
                                    <p>User Role</p>
                                </a>
                            </li>
                        @endcan
                        @can('can view users')
                            <li class="nav-item">
                                <a href="{{ route('list_users') }}" class="nav-link">
                                    <i class="fas fa-users nav-icon"></i>
                                    <p>Users</p>
                                </a>
                            </li>
                        @endcan
                        @can('can view permissions')
                            <li class="nav-item">
                                <a href="{{ route('list_permissions') }}" class="nav-link">
                                    <i class="fas fa-lock nav-icon"></i>
                                    <p>Permissions</p>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

         
            <!-- Vendor & Finance -->
            @canany([
                'can view vendors',
                'can edit vendors',
                'can delete vendors',
                'can create vendors',
                'can view
                payments',
                'can edit payments',
                'can delete payments',
                'can create payments',
                'can view currency',
                'can edit
                currency',
                'can delete currency',
                'can create currency',
                ])
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-handshake"></i>
                        <p>
                            Vendor & Finance
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        @can('can view vendors')
                            <li class="nav-item">
                                <a href="{{ route('list_vendors') }}" class="nav-link">
                                    <i class="fas fa-building nav-icon"></i>
                                    <p>Vendors</p>
                                </a>
                            </li>
                        @endcan
                        @can('can view payments')
                            <li class="nav-item">
                                <a href="{{ route('list_payment') }}" class="nav-link">
                                    <i class="fas fa-money-bill-wave nav-icon"></i>
                                    <p>Payments</p>
                                </a>
                            </li>
                        @endcan
                        @can('can view currency')
                            <li class="nav-item">
                                <a href="{{ route('list_currency') }}" class="nav-link">
                                    <i class="fas fa-coins nav-icon"></i>
                                    <p>Currency</p>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            <!-- Financial Settings -->
            @canany([
                'can view tax',
                'can edit tax',
                'can delete tax',
                'can create tax',
                'can view extra charges',
                'can
                edit extra charges',
                'can delete extra charges',
                'can create extra charges',
                'can view discounts',
                'can edit
                discounts',
                'can delete discounts',
                'can create discounts',
                'can view reason',
                'can edit reason',
                'can
                delete reason',
                'can create reason',
                ])
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>
                            Financial Settings
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        @can('can view tax')
                            <li class="nav-item">
                                <a href="{{ route('list_tax') }}" class="nav-link">
                                    <i class="fas fa-percentage nav-icon"></i>
                                    <p>Tax</p>
                                </a>
                            </li>
                        @endcan
                        @can('can view extra charges')
                            <li class="nav-item">
                                <a href="{{ route('list_extra_charge') }}" class="nav-link">
                                    <i class="fas fa-plus-circle nav-icon"></i>
                                    <p>Extra Charges</p>
                                </a>
                            </li>
                        @endcan
                        @can('can view discounts')
                            <li class="nav-item">
                                <a href="{{ route('list_discount') }}" class="nav-link">
                                    <i class="fas fa-tag nav-icon"></i>
                                    <p>Discounts</p>
                                </a>
                            </li>
                        @endcan
                        @can('can view reason')
                            <li class="nav-item">
                                <a href="{{ route('list_reason') }}" class="nav-link">
                                    <i class="fas fa-clipboard-list nav-icon"></i>
                                    <p>Reason</p>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            <!-- Reports -->
            @can('can view reports')
            <li class="nav-item">
                <a href="{{ route('list_report') }}" class="nav-link">
                    <i class="nav-icon fas fa-file"></i>
                    <p>
                        Reports
                    </p>
                </a>
            </li>
            @endcan

            <!-- Settings -->
            @canany([
                'can view company details',
                'can edit company details',
                'can create company details',
                'can view
                virtual devices',
                ])
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            Settings
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        @can('can view company details')
                            <li class="nav-item">
                                <a href="{{ route('list_company_details') }}" class="nav-link">
                                    <i class="fas fa-percentage nav-icon"></i>
                                    <p>Company Details</p>
                                </a>
                            </li>
                        @endcan
                        @can('can view virtual devices')
                            <li class="nav-item">
                                <a href="{{ route('list_virtual_devices') }}" class="nav-link">
                                    <i class="fas fa-percentage nav-icon"></i>
                                    <p>Virtual Devices</p>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany
        </ul>
    </nav>
</div>
