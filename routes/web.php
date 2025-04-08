<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\CostStockController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExtraChargeController;
use App\Http\Controllers\GeneralReportController;
use App\Http\Controllers\GoodReceiveNoteController;
use App\Http\Controllers\GoodReturnController;
use App\Http\Controllers\GoodsIssuedNoteController;
use App\Http\Controllers\ItemBrandController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemGroupController;
use App\Http\Controllers\ItemTypeController;
use App\Http\Controllers\OrderSummaryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PeripheralController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PointOfSaleController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReasonController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ExpensesCategoryController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\ItemOrderCartController;
use App\Http\Controllers\LeaveManagementController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PerformanceReviewController;
use App\Http\Controllers\POSLocationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\VirtualDeviceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {

    Route::group(['prefix' => 'roles'], function () {
        Route::get('', [\App\Http\Controllers\RoleController::class, 'indexRoles'])->name('list_roles');
        Route::get('create', [\App\Http\Controllers\RoleController::class, 'createRoles']);
        Route::post('store', [\App\Http\Controllers\RoleController::class, 'storeRoles'])->name('store_route');
        Route::get('edit', [\App\Http\Controllers\RoleController::class, 'editRoles']);
        Route::put('update/{id}', [\App\Http\Controllers\RoleController::class, 'updateRoles']);
        Route::delete('delete/{id}', [\App\Http\Controllers\RoleController::class, 'deleteRoles'])->name('role_delete');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('', [UserController::class, 'indexUsers'])->name('list_users');
        Route::post('store', [UserController::class, 'storeUsers'])->name('store_users');
        Route::get('edit/{user}', [UserController::class, 'editUsersWithPermission'])->name('edit_users_with_permission');
        Route::put('/users/{user}', [UserController::class, 'updateUsersWithPermission'])->name('update_user');
        Route::delete('/users/{user}', [UserController::class, 'deleteUsers'])->name('delete_user');
    });

    Route::group(['prefix' => 'permissions'], function () {
        Route::get('', [PermissionController::class, 'indexPermissions'])->name('list_permissions');
        // Route::post('store', [UserController::class, 'storeUsers'])->name('store_users');
        // Route::get('edit/{user}', [UserController::class, 'editUsersWithPermission'])->name('edit_users_with_permission');
        // Route::put('/users/{user}', [UserController::class, 'updateUsersWithPermission'])->name('update_user');
        // Route::delete('/users/{user}', [UserController::class, 'deleteUsers'])->name('delete_user');
    });

    // defaults route 
    Route::get('/payment-options/{paymentTypeId}', [PointOfSaleController::class, 'getPaymentOptions']);



    Route::group(['prefix' => 'stores'], function () {
        Route::get('list_option', [StoreController::class, 'listStore'])->name('list_option');
        Route::get('', [StoreController::class, 'indexStore'])->name('list_stores');
        Route::post('store', [StoreController::class, 'storeStore'])->name('store');
        Route::get('edit/{id}', [StoreController::class, 'editStore'])->name('edit_store');
        Route::put('update/{id}', [StoreController::class, 'updateStore'])->name('update_store');
        Route::delete('delete/{id}', [StoreController::class, 'deleteStore'])->name('delete_store');

    });
    Route::group(['prefix' => 'vendors_finance'], function () {

        Route::group(['prefix' => 'currencies'], function () {
            Route::get('', [CurrencyController::class, 'indexCurrency'])->name('list_currency');
            Route::post('store', [CurrencyController::class, 'storeCurrency'])->name('store_currency');
            Route::put('update/{id}', [CurrencyController::class, 'updateCurrency'])->name('update_currency');
            Route::delete('{id}', [CurrencyController::class, 'deleteCurrency'])->name('delete_currency');
        });

        Route::group(['prefix' => 'vendors'], function () {
            Route::get('', [VendorController::class, 'indexVendor'])->name('list_vendors');
            Route::post('store', [VendorController::class, 'storeVendor'])->name('store_vendors');
            Route::get('edit/{id}', [VendorController::class, 'editVendor'])->name('edit_vendor');
            Route::put('update/{id}', [VendorController::class, 'updateVendor'])->name('update_vendor');
            Route::delete('delete/{id}', [VendorController::class, 'deleteVendor'])->name('delete_vendor');
        });

        Route::group(['prefix' => 'payments'], function () {
            Route::get('', [PaymentController::class, 'indexPayment'])->name('list_payment');
            Route::post('store', [PaymentController::class, 'storePayment'])->name('store_payment');
            Route::get('edit/{id}', [PaymentController::class, 'editPayment'])->name('edit_payments');
            Route::put('{id}', [PaymentController::class, 'updatePayment'])->name('update_payment');
            Route::delete('delete/{id}', [PaymentController::class, 'deletePayment'])->name('delete_payment');
            // default
            Route::get('details/{id}', [PaymentController::class, 'getPaymentDetails'])->name('payment_details');

        });


    });

    Route::group(['prefix' => 'inventory'], function () {

        Route::group(['prefix' => 'categories'], function () {
            Route::get('', [CategoryController::class, 'indexCategory'])->name('list_category');
            Route::post('store', [CategoryController::class, 'storeCategory'])->name('store_category');
            Route::put('update/{id}', [CategoryController::class, 'updateCategory'])->name('update_category');
            Route::delete('delete/{id}', [CategoryController::class, 'deleteCategory'])->name('delete_category');
        });
        Route::group(['prefix' => 'unit'], function () {
            Route::get('', [UnitController::class, 'indexUnit'])->name('list_unit');
            Route::post('store', [UnitController::class, 'storeUnit'])->name('store_unit');
            Route::get('edit/{id}', [UnitController::class, 'editUnit'])->name('edit_unit');
            Route::put('update/{id}', [UnitController::class, 'updateUnit'])->name('update_unit');
            Route::delete('delete/{id}', [UnitController::class, 'deleteUnit'])->name('delete_unit');
        });

        Route::group(['prefix' => 'item_type'], function () {
            Route::get('', [ItemTypeController::class, 'indexItemType'])->name('list_item_type');
            Route::post('store', [ItemTypeController::class, 'storeItemType'])->name('store_item_type');
            Route::put('update/{id}', [ItemTypeController::class, 'updateItemType'])->name('update_item_type');
            Route::delete('{id}', [ItemTypeController::class, 'deleteItemType'])->name('delete_item_type');
        });

        Route::group(['prefix' => 'item_group'], function () {
            Route::get('', [ItemGroupController::class, 'indexItemGroup'])->name('list_item_group');
            Route::post('store', [ItemGroupController::class, 'storeItemGroup'])->name('store_item_group');
            Route::get('edit/{id}', [ItemGroupController::class, 'editItemGroup'])->name('edit_item_group');
            Route::put('update/{id}', [ItemGroupController::class, 'updateItemGroup'])->name('update_item_group');
            Route::delete('delete/{id}', [ItemGroupController::class, 'deleteItemGroup'])->name('delete_item_group');
        });
        Route::group(['prefix' => 'items'], function () {
            Route::get('', [ItemController::class, 'indexItem'])->name('list_item');
            Route::get('create', [ItemController::class, 'createItem'])->name('create_item');
            Route::post('store', [ItemController::class, 'storeItem'])->name('store_item');
            Route::get('edit/{id}', [ItemController::class, 'editItem'])->name('edit_item');
            Route::put('update/{id}', [ItemController::class, 'updateItem'])->name('update_item');
            Route::delete('delete/{id}', [ItemController::class, 'deleteItem'])->name('delete_item');
        });

        Route::group(['prefix' => 'item_brand'], function () {
            Route::get('', [ItemBrandController::class, 'indexItemBrand'])->name('list_item_brand');
            Route::get('create', [ItemBrandController::class, 'createItemBrand'])->name('create_item_brand');
            Route::post('store', [ItemBrandController::class, 'storeItemBrand'])->name('store_item_brand');
            Route::get('edit/{id}', [ItemBrandController::class, 'editItemBrand'])->name('edit_item_brand');
            Route::put('update/{id}', [ItemBrandController::class, 'updateItemBrand'])->name('update_item_brand');
            Route::delete('delete/{id}', [ItemBrandController::class, 'deleteItemBrand'])->name('delete_item_brand');
        });

        Route::group(['prefix' => 'cost_stock'], function () {
            Route::get('', [CostStockController::class, 'indexCostStock'])->name('list_cost_stock');
            Route::get('fetch', [CostStockController::class, 'fetchCostStock'])->name('fetch_cost_stock');
            Route::post('update', [CostStockController::class, 'updateCostStock'])->name('update_cost_stock');
        });
    });

    Route::group(['prefix' => 'financial_settings'], function () {
        Route::group(['prefix' => 'tax'], function () {
            Route::get('list_option', [TaxController::class, 'listTax'])->name('list_option');
            Route::get('', [TaxController::class, 'indexTax'])->name('list_tax');
            Route::post('store', [TaxController::class, 'storeTax'])->name('store_tax');
            Route::put('{id}', [TaxController::class, 'updateTax'])->name('update_tax');
            Route::delete('{id}', [TaxController::class, 'deleteTax'])->name('delete_tax');
        });

        Route::group(['prefix' => 'extra_charge'], function () {
            Route::get('', [ExtraChargeController::class, 'indexExtraCharge'])->name('list_extra_charge');
            Route::post('store', [ExtraChargeController::class, 'storeExtraCharge'])->name('store_extra_charge');
            Route::put('{id}', [ExtraChargeController::class, 'updateExtraCharge'])->name('update_extra_charge');
            Route::delete('{id}', [ExtraChargeController::class, 'deleteExtraCharge'])->name('delete_extra_charge');
        });

        Route::group(['prefix' => 'discount'], function () {
            Route::get('', [DiscountController::class, 'indexDiscount'])->name('list_discount');
            Route::post('store', [DiscountController::class, 'storeDiscount'])->name('store_discount');
            Route::put('{id}', [DiscountController::class, 'updateDiscount'])->name('update_discount');
            Route::delete('{id}', [DiscountController::class, 'deleteDiscount'])->name('delete_discount');
        });

        Route::group(['prefix' => 'reason'], function () {
            Route::get('', [ReasonController::class, 'indexReason'])->name('list_reason');
            Route::post('store', [ReasonController::class, 'storeReason'])->name('store_reason');
            Route::put('{id}', [ReasonController::class, 'updateReason'])->name('update_reason');
            Route::delete('{id}', [ReasonController::class, 'deleteReason'])->name('delete_reason');
        });
    });
    Route::group(['prefix' => 'purchases'], function () {

        Route::group(['prefix' => 'po'], function () {
            Route::get('', [PurchaseOrderController::class, 'indexPurchaseOrder'])->name('list_po');
            Route::get('create', [PurchaseOrderController::class, 'createPurchaseOrder'])->name('create_po');
            Route::post('store', [PurchaseOrderController::class, 'storePurchaseOrder'])->name('store_po');
            Route::get('preview/{id}', [PurchaseOrderController::class, 'previewPurchaseOrder'])->name('preview_po');
            Route::get('status/{id}', [PurchaseOrderController::class, 'updatePoStatus'])->name('status_po');
            Route::get('edit/{id}', [PurchaseOrderController::class, 'editPurchaseOrder'])->name('edit_po');
            Route::post('store/{id}', [PurchaseOrderController::class, 'storePurchaseOrderItems'])->name('store_po_item');
            Route::put('item/{id}', [PurchaseOrderController::class, 'updatePurchaseOrderItem'])->name('update_po_item');
            Route::delete('{id}', [PurchaseOrderController::class, 'deletePurchaseOrderItem'])->name('delete_po_item');
            Route::post('new_item', [PurchaseOrderController::class, 'storeNewPurchaseOrderItem'])->name('store_po_new_item');
        });

        Route::group(['prefix' => 'grn'], function () {
            Route::get('', [GoodReceiveNoteController::class, 'indexGoodReceiveNote'])->name('list_grn');
            Route::get('create', [GoodReceiveNoteController::class, 'createGoodReceiveNote'])->name('create_grn');
            Route::post('store', [GoodReceiveNoteController::class, 'storeGoodReceiveNote'])->name('store_grn');
            Route::get('status/{id}', [GoodReceiveNoteController::class, 'updateGrnStatus'])->name('status_grn');
            Route::get('edit/{id}', [GoodReceiveNoteController::class, 'editGoodReceiveNote'])->name('edit_grn');
            Route::get('preview/{id}', [GoodReceiveNoteController::class, 'previewGoodReceiveNote'])->name('preview_grn');
            Route::put('{id}', [GoodReceiveNoteController::class, 'updateGoodReceiveNote'])->name('update_grn');
            Route::delete('{id}', [GoodReceiveNoteController::class, 'deleteGoodReceiveNote'])->name('delete_grn');

            // grn status update 
            Route::post('/{id}/verify', [GoodReceiveNoteController::class, 'verifyGRN'])->name('grn_verify');
            Route::post('/{id}/accept', [GoodReceiveNoteController::class, 'acceptGRN'])->name('grn_accept');
            Route::post('/{id}/reject', [GoodReceiveNoteController::class, 'rejectGRN'])->name('grn_reject');
            Route::post('/{id}/complete', [GoodReceiveNoteController::class, 'completeGRN'])->name('grn_complete');
            Route::post('/{id}/reopen', [GoodReceiveNoteController::class, 'reopenGRN'])->name('grn_reopen');
            Route::post('/{id}/cancel', [GoodReceiveNoteController::class, 'cancelGRN'])->name('grn_cancel');

        });

        Route::group(['prefix' => 'gr'], function () {
            Route::get('', [GoodReturnController::class, 'indexGoodReturn'])->name('list_gr');
            Route::post('store', [GoodReturnController::class, 'storeGoodReturn'])->name('store_gr');
            Route::get('edit/{id}', [GoodReturnController::class, 'editGoodReturn'])->name('edit_gr');
            Route::put('{id}', [GoodReturnController::class, 'updateGoodReturn'])->name('update_gr');
            Route::delete('{id}', [GoodReturnController::class, 'deleteGoodReturn'])->name('delete_gr');
        });

        // Goods Issued Note Routes
        Route::group(['prefix' => 'gin'], function () {
            Route::get('', [GoodsIssuedNoteController::class, 'indexGoodsIssuedNote'])->name('list_gin');
            Route::post('store', [GoodsIssuedNoteController::class, 'storeGoodsIssuedNote'])->name('store_gin');
            Route::get('edit/{id}', [GoodsIssuedNoteController::class, 'editGoodsIssuedNote'])->name('edit_gin');
            Route::put('{id}', [GoodsIssuedNoteController::class, 'updateGoodsIssuedNote'])->name('update_gin');
            Route::delete('{id}', [GoodsIssuedNoteController::class, 'deleteGoodsIssuedNote'])->name('delete_gin');
        });
    });


    Route::group(['prefix' => 'point_of_sale'], function () {

        Route::group(['prefix' => 'pos'], function () {
            Route::get('index', [PointOfSaleController::class, 'salePOS'])->name('main_pos');
            Route::post('store', [PointOfSaleController::class, 'storePointOfSales']);
            Route::get('items', [PointOfSaleController::class, 'getItemsByCategory'])->name('get_items_by_group');
            Route::get('item_groups', [PointOfSaleController::class, 'getItemGroups']);
            Route::get('item_categories', [PointOfSaleController::class, 'getItemCategories'])->name('item_categories');
            Route::get('extra_charge', [PointOfSaleController::class, 'extraCharge']);
            Route::get('company_details', [PointOfSaleController::class, 'receiptCompanyDetails']);
            Route::post('print_receipt', [PointOfSaleController::class, 'printReceipt']);

        });

        Route::group(['prefix' => 'order_summary'], function () {
            Route::get('index', [OrderSummaryController::class, 'indexOrderSummary'])->name('order_summary');
            Route::post('get-items', [OrderSummaryController::class, 'getItems'])->name('get.items');
            Route::post('update-items', [OrderSummaryController::class, 'updateItems'])->name('update.items');
            Route::post('settle-cart', [OrderSummaryController::class, 'settleCart'])->name('settle.cart');
            Route::post('void-order', [OrderSummaryController::class, 'voidOrder'])->name('order_summary.void');
        });

        Route::group(['prefix' => 'pos_location'], function () {
            Route::get('index', [POSLocationController::class, 'indexPOSLocation'])->name('list_location');
            Route::post('store', [POSLocationController::class, 'storePOSLocation'])->name('store_location');

        });

        Route::group(['prefix' =>  'cart'], function () {
            Route::post('store', [CartController::class, 'store'])->name('cart_store');
        });

    });

    Route::group(['prefix' => 'expenses'], function () {
        Route::get('', [ExpensesController::class, 'indexExpenses'])->name('list_expenses');
        Route::get('create', [ExpensesController::class, 'createExpenses'])->name('create_expenses');
        Route::post('store', [ExpensesController::class, 'storeExpenses'])->name('store_expenses');

        Route::group(['prefix' => 'category'], function () {
            Route::get('', [ExpensesCategoryController::class, 'indexExpensesCategory'])->name('list_category_expenses');
            Route::get('create', [ExpensesCategoryController::class, 'indexExpensesCategory'])->name('list_category_expenses');
            Route::post('store', [ExpensesCategoryController::class, 'storeExpensesCategory'])->name('store_category_expenses');
            Route::get('update', [ExpensesCategoryController::class, 'indexExpensesCategory'])->name('list_category_expenses');
            Route::get('delete', [ExpensesCategoryController::class, 'indexExpensesCategory'])->name('list_category_expenses');
        });


    });

    Route::group(['prefix' => 'expenses'], function () {
        Route::get('', [ExpensesController::class, 'indexExpenses'])->name('list_expenses');
        Route::get('create', [ExpensesController::class, 'createExpenses'])->name('create_expenses');
        Route::post('store', [ExpensesController::class, 'storeExpenses'])->name('store_expenses');
        Route::get('edit/{id}', [ExpensesController::class, 'editExpenses'])->name('edit_expenses');
        Route::put('update{id}', [ExpensesController::class, 'updateExpenses'])->name('update_expenses');
        Route::delete('delete/{id}', [ExpensesController::class, 'deleteExpenses'])->name('delete_expenses');

        Route::group(['prefix' => 'category'], function () {
            Route::get('', [ExpensesCategoryController::class, 'indexExpensesCategory'])->name('list_category_expenses');
            Route::get('create', [ExpensesCategoryController::class, 'indexExpensesCategory'])->name('list_category_expenses');
            Route::post('store', [ExpensesCategoryController::class, 'storeExpensesCategory'])->name('store_category_expenses');
            Route::get('update', [ExpensesCategoryController::class, 'indexExpensesCategory'])->name('list_category_expenses');
            Route::get('delete', [ExpensesCategoryController::class, 'indexExpensesCategory'])->name('list_category_expenses');
        });


    });

    Route::group(['prefix' => 'hr'], function () {
        Route::group(['prefix' => 'employee'], function () {
            Route::get('', [EmployeeController::class, 'indexEmployee'])->name('list_employee');
            Route::get('create', [EmployeeController::class, 'createEmployee'])->name('create_employee');
            Route::post('store', [EmployeeController::class, 'storeEmployee'])->name('store_employee');
            Route::get('edit/{id}', [EmployeeController::class, 'editEmployee'])->name('edit_employee');
            Route::put('update{id}', [EmployeeController::class, 'updateEmployee'])->name('update_employee');
            Route::delete('delete/{id}', [EmployeeController::class, 'deleteEmployee'])->name('delete_employee');
        });
        Route::group(['prefix' => 'designation'], function () {
            Route::get('', [DesignationController::class, 'indexDesignation'])->name('list_designation');
            Route::get('create', [DesignationController::class, 'createDesignation'])->name('create_designation');
            Route::post('store', [DesignationController::class, 'storeDesignation'])->name('store_designation');
            Route::get('edit/{id}', [DesignationController::class, 'editDesignation'])->name('edit_designation');
            Route::put('update{id}', [DesignationController::class, 'updateDesignation'])->name('update_designation');
            Route::delete('delete/{id}', [DesignationController::class, 'deleteDesignation'])->name('delete_designation');
        });

        Route::group(['prefix' => 'payroll'], function () {
            Route::get('month-target', [PayrollController::class, 'monthTarget'])->name('month_target');
            Route::get('recover-target', [PayrollController::class, 'recoverTarget'])->name('recover_target');
            Route::get('sales-commission', [PayrollController::class, 'salesCommission'])->name('sales_commission');
            Route::get('month-salary-generated', [PayrollController::class, 'monthSalaryGenerated'])->name('month_salary_generated');
        });

        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance'); // Assumed AttendanceController
        Route::get('leave-management', [LeaveManagementController::class, 'index'])->name('leave_management'); // Assumed LeaveManagementController
        Route::get('performance-review', [PerformanceReviewController::class, 'index'])->name('performance_review'); // Assumed PerformanceReviewController
    });

    Route::group(['prefix' => 'customers'], function () {
        Route::get('', [CustomerController::class, 'indexCustomer'])->name('list_customer');
        Route::get('create', [CustomerController::class, 'createCustomer'])->name('create_customer');
        Route::post('store', [CustomerController::class, 'storeCustomer'])->name('store_customer');
        Route::get('edit/{id}', [CustomerController::class, 'editCustomer'])->name('edit_customer');
        Route::put('update{id}', [CustomerController::class, 'updateCustomer'])->name('update_customer');
        Route::delete('delete/{id}', [CustomerController::class, 'deleteCustomer'])->name('delete_customer');

    });


    Route::group(['prefix' => 'dashboard'], function () {

        Route::get('sale_counter', [DashboardController::class, 'saleCounter']);
        Route::get('purchase_counter', [DashboardController::class, 'purchaseCounter']);
        Route::get('expenses', [DashboardController::class, 'expensesCounter']);
        Route::get('profit', [DashboardController::class, 'profitCounter']);
        Route::get('stock_value', [DashboardController::class, 'stockValueCounter']);
        Route::get('cash_on_hand', [DashboardController::class, 'cashOnHandCounter']);
        Route::post('employee', [DashboardController::class, 'employeeCounter']);
        Route::get('loss', [DashboardController::class, 'lossCounter']);
        Route::get('top_selling_item', [DashboardController::class, 'topSellingItem']);
        Route::get('sales_and_purchases_chart', [DashboardController::class, 'salesAndPurchasesChart']);

    });

    Route::group(['prefix' => 'settings'], function () {

        Route::group(['prefix' => 'company_settings'], function () {
            Route::get('index', [CompanySettingController::class, 'indexCompanySetting'])->name('list_company_details');
            Route::get('create', [CompanySettingController::class, 'createCompanySetting'])->name('create_company_details');
            Route::post('store', [CompanySettingController::class, 'storeCompanySetting'])->name('store_company_details');
            Route::get('edit/{id}', [CompanySettingController::class, 'editCompanySetting'])->name('edit_company_details');
            Route::put('update/{id}', [CompanySettingController::class, 'updateCompanySetting'])->name('update_company_details');
            Route::delete('company/{id}', [CompanySettingController::class, 'deleteCompanySetting'])->name('delete_company_details');

        });

        Route::group(['prefix' => 'virtual_devices'], function () {
            Route::get('index', [VirtualDeviceController::class, 'indexVirtualDevice'])->name('list_virtual_devices');
            Route::post('store', [VirtualDeviceController::class, 'storeVirtualDevice'])->name('store_virtual_devices');
            Route::put('update/{id}', [VirtualDeviceController::class, 'updateVirtualDevice'])->name('update_virtual_devices');
            Route::delete('delete/{id}', [VirtualDeviceController::class, 'deleteVirtualDevice'])->name('delete_virtual_devices');

        });


        Route::group(['prefix' => 'peripheral_setting'], function () {
            Route::get('', [PeripheralController::class, 'indexPeripheralSetting'])->name('list_peripheral_setting');
            Route::post('store', [PeripheralController::class, 'storePeripheralSetting'])->name('store_peripheral_setting');
            Route::put('{id}', [PeripheralController::class, 'updatePeripheralSetting'])->name('update_peripheral_setting');
            Route::delete('delete/{id}', [PeripheralController::class, 'deletePeripheralSetting'])->name('delete_peripheral_setting');
            Route::get('/{id}/settings', [PeripheralController::class, 'getSettings']);


        });

    });

    Route::group(['prefix' => 'reports'], function () {
        // Main reports page
        Route::get('index', [GeneralReportController::class, 'indexGeneralReports'])->name('list_report');

        // Inventory Reports (These routes were already present)
        Route::group(['prefix' => 'inventory'], function () {
            Route::get('stock-level/preview', [GeneralReportController::class, 'previewStockLevel'])->name('preview_stock_level_report');
            Route::get('stock-level/generate', [GeneralReportController::class, 'generateStockLevel'])->name('generate_stock_level_report');

            Route::get('stock-movement/preview', [GeneralReportController::class, 'previewStockMovement'])->name('preview_stock_movement_report');
            Route::get('stock-movement/generate', [GeneralReportController::class, 'generateStockMovement'])->name('generate_stock_movement_report');

            Route::get('low-stock/preview', [GeneralReportController::class, 'previewLowStock'])->name('preview_low_stock_report');
            Route::get('low-stock/generate', [GeneralReportController::class, 'generateLowStock'])->name('generate_low_stock_report');

            Route::get('dead-stock/preview', [GeneralReportController::class, 'previewDeadStock'])->name('preview_dead_stock_report');
            Route::get('dead-stock/generate', [GeneralReportController::class, 'generateDeadStock'])->name('generate_dead_stock_report');
            // Inventory Reports 
            Route::group(['prefix' => 'inventory'], function () {
                Route::get('stock-level/preview', [InventoryReportController::class, 'previewStockLevel'])->name('preview_stock_level_report');
                Route::post('stock-level/generate', [InventoryReportController::class, 'generateStockLevel'])->name('generate_stock_level_report');

                Route::get('stock-movement/preview', [InventoryReportController::class, 'previewStockMovement'])->name('preview_stock_movement_report');
                Route::get('stock-movement/generate', [InventoryReportController::class, 'generateStockMovement'])->name('generate_stock_movement_report');

                Route::get('low-stock/preview', [InventoryReportController::class, 'previewLowStock'])->name('preview_low_stock_report');
                Route::get('low-stock/generate', [InventoryReportController::class, 'generateLowStock'])->name('generate_low_stock_report');

                Route::get('dead-stock/preview', [InventoryReportController::class, 'previewDeadStock'])->name('preview_dead_stock_report');
                Route::get('dead-stock/generate', [InventoryReportController::class, 'generateDeadStock'])->name('generate_dead_stock_report');
            });

            // Purchase Reports
            Route::group(['prefix' => 'purchase'], function () {
                Route::get('po-history/preview', [GeneralReportController::class, 'previewPOHistory'])->name('preview_po_history_report');
                Route::get('po-history/generate', [GeneralReportController::class, 'generatePOHistory'])->name('generate_po_history_report');

                Route::get('pending-po/preview', [GeneralReportController::class, 'previewPendingPO'])->name('preview_pending_po_report');
                Route::get('pending-po/generate', [GeneralReportController::class, 'generatePendingPO'])->name('generate_pending_po_report');

                Route::get('supplier-performance/preview', [GeneralReportController::class, 'previewSupplierPerformance'])->name('preview_supplier_performance_report');
                Route::get('supplier-performance/generate', [GeneralReportController::class, 'generateSupplierPerformance'])->name('generate_supplier_performance_report');

                // **New Route: Supplier Payments**
                Route::get('supplier-payment/preview', [GeneralReportController::class, 'previewSupplierPayment'])->name('preview_supplier_payment_report');
                Route::get('supplier-payment/generate', [GeneralReportController::class, 'generateSupplierPayment'])->name('generate_supplier_payment_report');
            });

            // Sales Reports
            Route::group(['prefix' => 'sales'], function () {
                Route::get('summary/preview', [GeneralReportController::class, 'previewSalesSummary'])->name('preview_sales_summary_report');
                Route::get('summary/generate', [GeneralReportController::class, 'generateSalesSummary'])->name('generate_sales_summary_report');

                Route::get('top-products/preview', [GeneralReportController::class, 'previewTopProducts'])->name('preview_top_products_report');
                Route::get('top-products/generate', [GeneralReportController::class, 'generateTopProducts'])->name('generate_top_products_report');

                Route::get('customer-analysis/preview', [GeneralReportController::class, 'previewCustomerAnalysis'])->name('preview_customer_analysis_report');
                Route::get('customer-analysis/generate', [GeneralReportController::class, 'generateCustomerAnalysis'])->name('generate_customer_analysis_report');

                // **New Route: Payment Methods Analysis**
                Route::get('payment-methods/preview', [GeneralReportController::class, 'previewPaymentMethods'])->name('preview_payment_methods_report');
                Route::get('payment-methods/generate', [GeneralReportController::class, 'generatePaymentMethods'])->name('generate_payment_methods_report');
            });

            // Financial Reports
            Route::group(['prefix' => 'financial'], function () {
                Route::get('cogs/preview', [GeneralReportController::class, 'previewCOGS'])->name('preview_cogs_report');
                Route::get('cogs/generate', [GeneralReportController::class, 'generateCOGS'])->name('generate_cogs_report');

                Route::get('profit-margins/preview', [GeneralReportController::class, 'previewProfitMargins'])->name('preview_profit_margins_report');
                Route::get('profit-margins/generate', [GeneralReportController::class, 'generateProfitMargins'])->name('generate_profit_margins_report');

                Route::get('expenses/preview', [GeneralReportController::class, 'previewExpenses'])->name('preview_expenses_report');
                Route::get('expenses/generate', [GeneralReportController::class, 'generateExpenses'])->name('generate_expenses_report');

                // **New Route: Payment Aging**
                Route::get('payment-aging/preview', [GeneralReportController::class, 'previewPaymentAging'])->name('preview_payment_aging_report');
                Route::get('payment-aging/generate', [GeneralReportController::class, 'generatePaymentAging'])->name('generate_payment_aging_report');
            });

            // Tax Reports
            Route::group(['prefix' => 'tax'], function () {
                Route::get('sales-tax/preview', [GeneralReportController::class, 'previewSalesTax'])->name('preview_sales_tax_report');
                Route::get('sales-tax/generate', [GeneralReportController::class, 'generateSalesTax'])->name('generate_sales_tax_report');

                Route::get('tax-collected/preview', [GeneralReportController::class, 'previewTaxCollected'])->name('preview_tax_collected_report');
                Route::get('tax-collected/generate', [GeneralReportController::class, 'generateTaxCollected'])->name('generate_tax_collected_report');

                // **New Route: Tax Payments Due**
                Route::get('tax-payments/preview', [GeneralReportController::class, 'previewTaxPayments'])->name('preview_tax_payments_report');
                Route::get('tax-payments/generate', [GeneralReportController::class, 'generateTaxPayments'])->name('generate_tax_payments_report');
            });

            // Audit Reports
            Route::group(['prefix' => 'audit'], function () {
                // **New Route: Price Change History**
                Route::get('price-history/preview', [GeneralReportController::class, 'previewPriceHistory'])->name('preview_price_history_report');
                Route::get('price-history/generate', [GeneralReportController::class, 'generatePriceHistory'])->name('generate_price_history_report');

                Route::get('user-activity/preview', [GeneralReportController::class, 'previewUserActivity'])->name('preview_user_activity_report');
                Route::get('user-activity/generate', [GeneralReportController::class, 'generateUserActivity'])->name('generate_user_activity_report');

                Route::get('system-access/preview', [GeneralReportController::class, 'previewSystemAccess'])->name('preview_system_access_report');
                Route::get('system-access/generate', [GeneralReportController::class, 'generateSystemAccess'])->name('generate_system_access_report');
            });
        });


    });

    Route::group(['prefix' => 'item_cart'], function () {
        Route::get('index', [ItemOrderCartController::class, 'indexCart'])->name('item_preview');
    });

});



