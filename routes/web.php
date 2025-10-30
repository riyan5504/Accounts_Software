<?php

use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\AdminLoginController;
use App\Http\Controllers\Backend\PurchaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/admin/login', [AdminLoginController::class, 'adminLogin'])->name('admin.login');
Route::get('/admin/logout', [AdminLoginController::class, 'adminLogOut'])->name('admin.logout');

Auth::routes();

Route::get('/admin/dashboard', [AdminController::class, 'adminDashboard'])->name('dashboard');

//Purchase Item.......
Route::get('/purchase', [PurchaseController::class, 'purchase']);
Route::get('/purchase/entry', [PurchaseController::class, 'purchaseEntry']);
Route::get('/purchase/list', [PurchaseController::class, 'purchaseList']);
