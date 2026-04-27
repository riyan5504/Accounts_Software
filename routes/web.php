<?php

use App\Http\Controllers\Backend\AccountController;
use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\AdminLoginController;
use App\Http\Controllers\Backend\ItemController;
use App\Http\Controllers\Backend\ProductionController;
use App\Http\Controllers\Backend\PurchaseController;
use App\Http\Controllers\Backend\ReportController;
use App\Http\Controllers\Backend\SearchController;
use App\Http\Controllers\Backend\VendorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/admin/login', [AdminLoginController::class, 'adminLogin'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');
Route::get('/admin/logout', [AdminLoginController::class, 'adminLogOut'])->name('admin.logout');

Route::middleware(['auth'])->group(function () {

    Route::get('/admin/dashboard', [AdminController::class, 'adminDashboard'])->name('admin.dashboard');
});
//search route.............
Route::get('/vendor/search', [SearchController::class, 'vendorSearch'])->name('vendor.search');
Route::get('/item/search', [SearchController::class, 'itemSearch'])->name('item.search');
Route::get('/category/search', [SearchController::class, 'categorySearch'])->name('category.search');
Route::get('/account-search', [SearchController::class, 'searchAccount'])->name('account.search');
Route::get('/user-search', [SearchController::class, 'searchUser'])->name('user.search');
Route::get('/get-accounts-by-status/{type}', [SearchController::class, 'getByStatus']);



//Purchase Item.......
Route::prefix('purchase')->name('purchase.')->group(function () {

    Route::get('/', [PurchaseController::class, 'purchase'])->name('index');
    Route::get('/entry', [PurchaseController::class, 'purchaseEntry'])->name('entry');
    Route::post('/store', [PurchaseController::class, 'store'])->name('store');
    Route::get('/list', [PurchaseController::class, 'purchaseList'])->name('list');
    Route::get('/edit/{id}', [PurchaseController::class, 'purchaseEdit'])->name('edit');
    Route::post('/update/{id}', [PurchaseController::class, 'purchaseUpdate'])->name('update');
    Route::get('/delete/{id}', [PurchaseController::class, 'purchaseDelete'])->name('delete');
    Route::get('/details/{id}', [PurchaseController::class, 'purchaseDetails'])->name('details');
    Route::get('/invoice/pdf/{id}', [PurchaseController::class, 'downloadPdf'])->name('invoice.pdf');
    Route::get('/list/pdf', [PurchaseController::class, 'downloadListPdf'])->name('list.pdf');

    //Vendor Route..............
    Route::get('/vendor/add', [VendorController::class, 'vendorAdd'])->name('vendoradd');
    Route::post('/vendor/store', [VendorController::class, 'vendorStore'])->name('vendorstore');
    Route::get('/vendor/list', [VendorController::class, 'vendorList'])->name('vendorlist');
    Route::get('/vendor/edit/{id}', [VendorController::class, 'vendorEdit'])->name('vendoredit');
    Route::post('/vendor/update/{id}', [VendorController::class, 'vendorUpdate'])->name('vendorupdate');
    Route::get('/vendor/delete/{id}', [VendorController::class, 'vendorDelete'])->name('vendordelete');
});


//Item Route...........
Route::prefix('item')->name('item.')->group(function () {
    Route::get('/', [ItemController::class, 'itemModule'])->name('index');
    Route::get('/add', [ItemController::class, 'itemAdd'])->name('item-add');
    Route::post('/store', [ItemController::class, 'itemStore'])->name('item-store');
    Route::get('/edit/{id}', [ItemController::class, 'itemEdit'])->name('item-edit');
    Route::post('/update/{id}', [ItemController::class, 'itemUpdate'])->name('item-update');
    Route::get('/delete/{id}', [ItemController::class, 'itemDelete'])->name('item-delete');

    //category.............

    Route::get('/category/add', [ItemController::class, 'categoryAdd'])->name('category-add');
    Route::post('/category/store', [ItemController::class, 'categoryStore'])->name('category-store');
    Route::get('/category/edit/{id}', [ItemController::class, 'categoryEdit'])->name('category-edit');
    Route::post('/category/update/{id}', [ItemController::class, 'categoryUpdate'])->name('category-update');
    Route::get('/category/delete/{id}', [ItemController::class, 'categoryDelete'])->name('category-delete');
});

//Account Routes.................
Route::prefix('account')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'accounts'])->name('index');
    Route::get('/entry', [AccountController::class, 'accountEntry'])->name('entry');
    Route::post('/store', [AccountController::class, 'accountStore'])->name('store');
    Route::get('/edit/{id}', [AccountController::class, 'accountEdit'])->name('edit');
    Route::post('/update/{id}', [AccountController::class, 'accountUpdate'])->name('update');
    Route::get('/delete/{id}', [AccountController::class, 'accountDelete'])->name('delete');
    Route::get('/expense/entry', [AccountController::class, 'expenseEntry'])->name('expense.entry');
    Route::post('/expense/store', [AccountController::class, 'expenceStore'])->name('expense.store');
});

//Manufactur............
Route::prefix('production')->name('production.')->group(function () {
    Route::get('/', [ProductionController::class, 'production'])->name('index');
    Route::get('/product/add', [ProductionController::class, 'productAdd'])->name('add');
    Route::post('/product/store', [ProductionController::class, 'productStore'])->name('store');
    Route::get('/list', [ProductionController::class, 'productionList'])->name('list');
    Route::get('/edit/{id}', [ProductionController::class, 'productionEdit'])->name('edit');
    Route::get('/delete/{id}', [ProductionController::class, 'productionDelete'])->name('delete');
    Route::post('/update/{id}', [ProductionController::class, 'productionUpdate'])->name('update');
    Route::get('/details/{id}', [ProductionController::class, 'productionDetails'])->name('details');
});
//Report...........
Route::prefix('report')->name('report.')->group(function () {
    Route::get('/stock/report', [ReportController::class, 'stockReport'])->name('stock.report');
});
