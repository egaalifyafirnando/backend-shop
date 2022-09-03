<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

/**
 * route for admin
 */

//group route with prefix "admin"
Route::prefix('admin')->group(function () {

    //group route with middleware "auth"
    Route::group(['middleware' => 'auth'], function () {

        // ROUTE DASHBOARD
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard.index');

        // ROUTE CATEGORY
        Route::resource('/category', CategoryController::class, ['as' => 'admin']);

        // ROUTE PRODUCT
        Route::resource('/product', ProductController::class, ['as' => 'admin']);
    });
});
