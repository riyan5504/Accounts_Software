<?php

use App\Http\Controllers\Backend\AccountController;
use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\AdminLoginController;
use App\Http\Controllers\Backend\ItemController;
use App\Http\Controllers\Backend\ProductionController;
use App\Http\Controllers\Backend\PurchaseController;
use App\Http\Controllers\Backend\SearchController;
use App\Http\Controllers\Backend\VendorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/admin/login', [AdminLoginController::class, 'adminLogin'])->name('admin.login');
Route::get('/admin/logout', [AdminLoginController::class, 'adminLogOut'])->name('admin.logout');

Auth::routes();

Route::get('/admin/dashboard', [AdminController::class, 'adminDashboard'])->name('dashboard');

//search route.............
Route::get('/vendor/search', [SearchController::class, 'vendorSearch'])->name('vendor.search');
Route::get('/item/search', [SearchController::class, 'itemSearch'])->name('item.search');
Route::get('/category/search', [SearchController::class, 'categorySearch'])->name('category.search');
Route::get('/account-search', [SearchController::class, 'searchAccount'])->name('account.search');
Route::get('/user-search', [SearchController::class, 'searchUser'])->name('user.search');
Route::get('/get-accounts-by-status/{type}', [SearchController::class, 'getByStatus']);



//Purchase Item.......
Route::get('/purchase', [PurchaseController::class, 'purchase']);
Route::get('/purchase/entry', [PurchaseController::class, 'purchaseEntry']);
Route::post('/purchase/store', [PurchaseController::class, 'store'])->name('purchase.store');
Route::get('/purchase/list', [PurchaseController::class, 'purchaseList']);
Route::get('/purchase/edit/{id}', [PurchaseController::class, 'purchaseEdit']);
Route::post('/purchase/update/{id}', [PurchaseController::class, 'purchaseUpdate']);
Route::get('/purchase/delete/{id}', [PurchaseController::class, 'purchaseDelete'])->name('purchase.delete');
Route::get('/purchase/details/{id}', [PurchaseController::class, 'purchaseDetails']);
Route::get('/get-accounts-by-purchase-type/{type}', [PurchaseController::class, 'getAccountsByPurchaseType']);

//Item Route...........
Route::get('/item', [ItemController::class, 'itemModule']);
Route::get('/item/add', [ItemController::class, 'itemAdd']);
Route::post('/item/store', [ItemController::class, 'itemStore']);
Route::get('/item/list', [ItemController::class, 'itemList']);
Route::get('/item/edit/{id}', [ItemController::class, 'itemEdit']);
Route::post('/item/update/{id}', [ItemController::class, 'itemUpdate']);
Route::get('/item/delete/{id}', [ItemController::class, 'itemDelete']);

//Vendor Route..............
Route::get('/vendor/add', [VendorController::class, 'vendorAdd']);
Route::post('/vendor/store', [VendorController::class, 'vendorStore']);
Route::get('/vendor/list', [VendorController::class, 'vendorList']);
Route::get('/vendor/edit/{id}', [VendorController::class, 'vendorEdit']);
Route::post('/vendor/update/{id}', [VendorController::class, 'vendorUpdate']);
Route::get('/vendor/delete/{id}', [VendorController::class, 'vendorDelete']);

//Account Routes.................
Route::get('/account', [AccountController::class, 'accounts']);
Route::get('/account/entry', [AccountController::class, 'accountEntry']);
Route::post('/account/store', [AccountController::class, 'accountStore']);
Route::get('/account/edit/{id}', [AccountController::class, 'accountEdit']);
Route::post('/account/update/{id}', [AccountController::class, 'accountUpdate']);
Route::get('/account/delete/{id}', [AccountController::class, 'accountDelete']);
Route::get('/expense/entry', [AccountController::class, 'expenseEntry']);
Route::post('/expense/store', [AccountController::class, 'expenceStore']);

//Manufactur............

Route::get('/production', [ProductionController::class, 'production']);
Route::get('/production/product/add', [ProductionController::class, 'productAdd']);
Route::post('/production/product/store', [ProductionController::class, 'productStore']);
Route::get('/production/list', [ProductionController::class, 'productionList']);
Route::get('/production/edit/{id}', [ProductionController::class, 'productionEdit']);
Route::get('/production/delete/{id}', [ProductionController::class, 'productionDelete']);
Route::post('/production/update/{id}', [ProductionController::class, 'productionUpdate']);