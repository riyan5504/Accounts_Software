<?php

use App\Http\Controllers\Backend\{
    AccountController,
    AdminController,
    AdminLoginController,
    CustomerController,
    CustomerReceivedController,
    ItemController,
    ProductionController,
    PurchaseController,
    ReportController,
    ReturnController,
    SalesController,
    SalesReturnController,
    SearchController,
    VendorController,
    VendorPaymentController
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminLoginController::class, 'adminLogin'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');
    Route::post('/register', [AdminLoginController::class, 'register'])->name('register');
});

Route::get('/admin/logout', [AdminLoginController::class, 'adminLogOut'])
    ->name('admin.logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'adminDashboard'])->name('dashboard');

    // Search Routes
    Route::prefix('search')->name('search.')->group(function () {
        Route::get('/vendor', [SearchController::class, 'vendorSearch'])->name('vendor');
        Route::get('/customer', [SearchController::class, 'customerSearch'])->name('customer');
        Route::get('/item', [SearchController::class, 'itemSearch'])->name('item');
        Route::get('/category', [SearchController::class, 'categorySearch'])->name('category');
        Route::get('/account', [SearchController::class, 'searchAccount'])->name('account');
        Route::get('/user', [SearchController::class, 'searchUser'])->name('user');
        Route::get('/accounts-by-status/{status}', [SearchController::class, 'getByStatus'])->name('accounts.by-status');
    });

    // Account Module
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'accounts'])->name('index');
        Route::get('/entry', [AccountController::class, 'accountEntry'])->name('entry');
        Route::post('/store', [AccountController::class, 'accountStore'])->name('store');
        Route::get('/edit/{id}', [AccountController::class, 'accountEdit'])->name('edit');
        Route::post('/update/{id}', [AccountController::class, 'accountUpdate'])->name('update');
        Route::get('/delete/{id}', [AccountController::class, 'accountDelete'])->name('delete');

        // Trash & Restore
        Route::get('/trash', [AccountController::class, 'accountTrashList'])->name('trash');
        Route::post('/restore/{id}', [AccountController::class, 'restoreAccount'])->name('restore');
        Route::delete('/force-delete/{id}', [AccountController::class, 'forceAccountDelete'])->name('forceDelete');

        // Expense
        Route::get('/expense/entry', [AccountController::class, 'expenseEntry'])->name('expense.entry');
        Route::post('/expense/store', [AccountController::class, 'expenceStore'])->name('expense.store');

        // Partner & Investment
        Route::get('/partner/entry', [AccountController::class, 'partnerEntry'])->name('partner.entry');
        Route::post('/partner/store', [AccountController::class, 'partnerStore'])->name('partner.store');
        Route::get('/partner/edit/{id}', [AccountController::class, 'partnerEdit'])->name('partner.edit');
        Route::get('/investment/entry', [AccountController::class, 'investmentEntry'])->name('investment.entry');
        Route::post('/investment/store', [AccountController::class, 'investmentStore'])->name('investment.store');
        Route::get('/investment/list', [AccountController::class, 'investmentList'])->name('investment.list');
    });

    // Item Module
    Route::prefix('item')->name('item.')->group(function () {
        Route::get('/', [ItemController::class, 'itemModule'])->name('index');
        Route::get('/add', [ItemController::class, 'itemAdd'])->name('add');
        Route::post('/store', [ItemController::class, 'itemStore'])->name('store');
        Route::get('/edit/{id}', [ItemController::class, 'itemEdit'])->name('edit');
        Route::post('/update/{id}', [ItemController::class, 'itemUpdate'])->name('update');
        Route::get('/delete/{id}', [ItemController::class, 'itemDelete'])->name('delete');
        Route::get('/trash', [ItemController::class, 'itemTrashList'])->name('trash');
        Route::post('/restore/{id}', [ItemController::class, 'restoreItem'])->name('restore');
        Route::delete('/force-delete/{id}', [ItemController::class, 'forceItemDelete'])->name('forceDelete');

        // Category
        Route::prefix('category')->name('category.')->group(function () {
            Route::get('/add', [ItemController::class, 'categoryAdd'])->name('add');
            Route::post('/store', [ItemController::class, 'categoryStore'])->name('store');
            Route::get('/edit/{id}', [ItemController::class, 'categoryEdit'])->name('edit');
            Route::post('/update/{id}', [ItemController::class, 'categoryUpdate'])->name('update');
            Route::get('/delete/{id}', [ItemController::class, 'categoryDelete'])->name('delete');
            Route::get('/trash', [ItemController::class, 'categoryTrashList'])->name('trash');
            Route::post('/restore/{id}', [ItemController::class, 'restoreCategory'])->name('restore');
            Route::delete('/force-delete/{id}', [ItemController::class, 'forceCategoryDelete'])->name('forceDelete');
        });
    });

    // Purchase Module
    Route::prefix('purchase')->name('purchase.')->group(function () {
        Route::get('/', [PurchaseController::class, 'purchase'])->name('index');
        Route::get('/entry', [PurchaseController::class, 'purchaseEntry'])->name('entry');
        Route::post('/store', [PurchaseController::class, 'store'])->name('store');
        Route::get('/list', [PurchaseController::class, 'purchaseList'])->name('list');
        Route::get('/edit/{id}', [PurchaseController::class, 'purchaseEdit'])->name('edit');
        Route::post('/update/{id}', [PurchaseController::class, 'purchaseUpdate'])->name('update');
        Route::get('/delete/{id}', [PurchaseController::class, 'purchaseDelete'])->name('delete');
        Route::get('/details/{id}', [PurchaseController::class, 'purchaseDetails'])->name('details');
        Route::get('/pdf/{id}', [PurchaseController::class, 'downloadPdf'])->name('pdf');
        Route::get('/list/pdf', [PurchaseController::class, 'downloadListPdf'])->name('list.pdf');

        // Purchase Returns
        Route::prefix('return')->name('return.')->group(function () {
            Route::get('/entry', [ReturnController::class, 'purchaseReturn'])->name('entry');
            Route::post('/store', [ReturnController::class, 'returnStore'])->name('store');
            Route::get('/list', [ReturnController::class, 'returnList'])->name('list');
            Route::get('/edit/{id}', [ReturnController::class, 'returnEdit'])->name('edit');
            Route::post('/update/{id}', [ReturnController::class, 'returnUpdate'])->name('update');
            Route::get('/delete/{id}', [ReturnController::class, 'returnDelete'])->name('delete');
            Route::get('/details/{id}', [ReturnController::class, 'returnDetails'])->name('details');
            Route::get('/pdf/{id}', [ReturnController::class, 'downloadPdf'])->name('pdf');
            Route::get('/list/pdf', [ReturnController::class, 'downloadListPdf'])->name('list.pdf');
            Route::get('/vendor/{vendor}', [ReturnController::class, 'getVendorData'])->name('vendor.data');
            Route::get('/invoice/{purchase}', [ReturnController::class, 'getInvoiceItems'])->name('invoice.items');
        });

        // Vendors
        Route::prefix('vendor')->name('vendor.')->group(function () {
            Route::get('/add', [VendorController::class, 'vendorAdd'])->name('add');
            Route::post('/store', [VendorController::class, 'vendorStore'])->name('store');
            Route::get('/list', [VendorController::class, 'vendorList'])->name('list');
            Route::get('/edit/{id}', [VendorController::class, 'vendorEdit'])->name('edit');
            Route::post('/update/{id}', [VendorController::class, 'vendorUpdate'])->name('update');
            Route::get('/delete/{id}', [VendorController::class, 'vendorDelete'])->name('delete');
        });
    });
    Route::prefix('vendor-payment')->name('vendor-payment.')->group(function () {
        Route::get('/', [VendorPaymentController::class, 'index'])->name('index');
        Route::get('/create', [VendorPaymentController::class, 'paymentCreate'])->name('create');
        Route::post('/store', [VendorPaymentController::class, 'paymentStore'])->name('store');
        Route::get('/details/{id}', [VendorPaymentController::class, 'paymentDetails'])->name('details');
        Route::get('/edit/{id}', [VendorPaymentController::class, 'paymentEdit'])->name('edit');
        Route::post('/update/{id}', [VendorPaymentController::class, 'paymentUpdate'])->name('update');
        Route::get('/delete/{id}', [VendorPaymentController::class, 'paymentDelete'])->name('delete');
        Route::get('/get-purchase/{vendor}', [VendorPaymentController::class, 'getVendorPurchase'])->name('get.purchase');
        Route::get('/get-purchase-info/{purchase}', [VendorPaymentController::class, 'getPurchaseInfo'])->name('get.purchase.info');
    });

    // Production Module
    Route::prefix('production')->name('production.')->group(function () {
        Route::get('/', [ProductionController::class, 'production'])->name('index');
        Route::get('/add', [ProductionController::class, 'productionAdd'])->name('add');
        Route::post('/store', [ProductionController::class, 'productionStore'])->name('store');
        Route::get('/list', [ProductionController::class, 'productionList'])->name('list');
        Route::get('/edit/{id}', [ProductionController::class, 'productionEdit'])->name('edit');
        Route::post('/update/{id}', [ProductionController::class, 'productionUpdate'])->name('update');
        Route::get('/delete/{id}', [ProductionController::class, 'productionDelete'])->name('delete');
        Route::get('/details/{id}', [ProductionController::class, 'productionDetails'])->name('details');
        Route::get('/pdf/{id}', [ProductionController::class, 'downloadPdf'])->name('pdf');
        Route::get('/list/pdf', [ProductionController::class, 'downloadListPdf'])->name('list.pdf');
    });

    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SalesController::class, 'sales'])->name('index');
        Route::get('/entry', [SalesController::class, 'salesEntry'])->name('entry');
        Route::post('/store', [SalesController::class, 'store'])->name('store');
        Route::get('/list', [SalesController::class, 'salesList'])->name('list');
        Route::get('/edit/{id}', [SalesController::class, 'salesEdit'])->name('edit');
        Route::post('/update/{id}', [SalesController::class, 'salesUpdate'])->name('update');
        Route::get('/delete/{id}', [SalesController::class, 'salesDelete'])->name('delete');
        Route::get('/details/{id}', [SalesController::class, 'salesDetails'])->name('details');
        Route::get('/pdf/{id}', [SalesController::class, 'downloadPdf'])->name('pdf');
        Route::get('/list/pdf', [SalesController::class, 'downloadListPdf'])->name('list.pdf');

        // sales Returns
        Route::prefix('return')->name('return.')->group(function () {
            Route::get('/entry', [SalesReturnController::class, 'salesReturn'])->name('entry');
            Route::post('/store', [SalesReturnController::class, 'returnStore'])->name('store');
            Route::get('/list', [SalesReturnController::class, 'returnList'])->name('list');
            Route::get('/edit/{id}', [SalesReturnController::class, 'returnEdit'])->name('edit');
            Route::post('/update/{id}', [SalesReturnController::class, 'returnUpdate'])->name('update');
            Route::get('/delete/{id}', [SalesReturnController::class, 'returnDelete'])->name('delete');
            Route::get('/details/{id}', [SalesReturnController::class, 'returnDetails'])->name('details');
            Route::get('/pdf/{id}', [SalesReturnController::class, 'downloadPdf'])->name('pdf');
            Route::get('/list/pdf', [SalesReturnController::class, 'downloadListPdf'])->name('list.pdf');
        });

        // Customer.........
        Route::prefix('customer')->name('customer.')->group(function () {
            Route::get('/add', [CustomerController::class, 'customerAdd'])->name('add');
            Route::post('/store', [CustomerController::class, 'customerStore'])->name('store');
            Route::get('/list', [CustomerController::class, 'customerList'])->name('list');
            Route::get('/edit/{id}', [CustomerController::class, 'customerEdit'])->name('edit');
            Route::post('/update/{id}', [CustomerController::class, 'customerUpdate'])->name('update');
            Route::get('/delete/{id}', [CustomerController::class, 'customerDelete'])->name('delete');

            Route::prefix('received')->name('received.')->group(function () {
                Route::get('/create', [CustomerReceivedController::class, 'paymentCreate'])->name('create');
                Route::post('/store', [CustomerReceivedController::class, 'paymentStore'])->name('store');
                Route::get('/details/{id}', [CustomerReceivedController::class, 'paymentDetails'])->name('details');
                Route::get('/edit/{id}', [CustomerReceivedController::class, 'paymentEdit'])->name('edit');
                Route::post('/update/{id}', [CustomerReceivedController::class, 'paymentUpdate'])->name('update');
                Route::get('/delete/{id}', [CustomerReceivedController::class, 'paymentDelete'])->name('delete');
                Route::get('/get-purchase/{vendor}', [CustomerReceivedController::class, 'getVendorPurchase'])->name('get.purchase');
                Route::get('/get-purchase-info/{purchase}', [CustomerReceivedController::class, 'getPurchaseInfo'])->name('get.purchase.info');
            });
        });
    });

    // Reports
    Route::prefix('report')->name('report.')->group(function () {
        Route::get('/', [ReportController::class, 'report'])->name('index');
        Route::get('/stock', [ReportController::class, 'stockReport'])->name('stock');
        Route::get('/stock/pdf', [ReportController::class, 'stockReportPdf'])->name('stock.pdf');
        Route::get('/item-ledger', [ReportController::class, 'itemLedger'])->name('item.ledger');
        Route::get('/item-ledger/pdf', [ReportController::class, 'itemLedgerPdf'])->name('item-ledger.pdf');
        Route::get('/vendor-due', [ReportController::class, 'vendorDue'])->name('vendor-due');
        Route::get('/vendor-ledger', [ReportController::class, 'vendorLedger'])->name('vendor-ledger');
        Route::get('/vendor-ledger/data', [ReportController::class, 'vendorLedgerData'])->name('vendor-ledger.data');
        Route::get('/vendor-due/pdf', [ReportController::class, 'vendorDuePdf'])->name('vendor-due.pdf');
        Route::get('/vendor-ledger/pdf', [ReportController::class, 'vendorLedgerPdf'])->name('vendor-ledger.pdf');
    });
});
