<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
     Route::prefix('auth')->group(function () {
         Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
     });

     //users
     Route::prefix('users')->group(function () {
         Route::get('', [\App\Http\Controllers\UserController::class, 'index']);

     });

     Route::prefix('master')->group(function () {
        Route::get('cities', [\App\Http\Controllers\MasterController::class, 'indexCity']);
        Route::get('positions', [\App\Http\Controllers\MasterController::class, 'indexPositions']);
        Route::get('genders', [\App\Http\Controllers\MasterController::class, 'indexGenders']);
    });

    Route::prefix('items')->group(function () {
        Route::get('item_group', [\App\Http\Controllers\ItemGroupController::class, 'indexItemGroupLocal']);
        Route::get('item_category', [\App\Http\Controllers\CategoryController::class, 'indexItemCategoryLocal']);
        Route::get('sale_items', [\App\Http\Controllers\PointOfSaleController::class, 'getItemsByCategoryLocal']);
        Route::get('units', [\App\Http\Controllers\PointOfSaleController::class, 'getItemsUnitsList']);

        Route::get('barcodes', [\App\Http\Controllers\PointOfSaleController::class, 'getItemsBarcodeList']);
    });

    Route::prefix('customers')->group(function () {
        Route::get('list', [\App\Http\Controllers\CustomerController::class, 'indexCustomerLocal']);
        Route::get('customer_type', [\App\Http\Controllers\CustomerTypeController::class, 'indexCustomerTypeLocal']);

    });

    Route::prefix('payments')->group(function () {
        Route::get('list', [\App\Http\Controllers\PaymentController::class, 'indexPaymentLocal']);
    });

    Route::prefix('orders')->group(function () {
        Route::get('order_number', [\App\Http\Controllers\PointOfSaleController::class, 'generateOrderNumberLocal']);
        Route::post('store_local_sale', [\App\Http\Controllers\PointOfSaleController::class, 'storePointOfSalesLocal']);
        Route::post('store_local_sale', [\App\Http\Controllers\PointOfSaleController::class, 'updateStock']);
    });

    Route::prefix('stocks')->group(function () {
        Route::post('update', [\App\Http\Controllers\PointOfSaleController::class, 'updateStock']);
    });

    Route::prefix('companies')->group(function () {
        Route::get('company_details', [\App\Http\Controllers\CompanySettingController::class, 'indexCompanySettingLocal']);
    });
    
    Route::prefix('stores')->group(function () {
        Route::get('list', [\App\Http\Controllers\StoreController::class, 'indexStoreLocal']);
    });

    
});
