<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login'])->name('api.customer.login');
Route::post('/register', [AuthController::class, 'register'])->name('api.customer.register');
Route::get('/user', [AuthController::class, 'getUser'])->name('api.customer.user');
Route::get('/order', [OrderController::class, 'index'])->name('admin.order.index');
Route::get('/order/{snap_token?}', [OrderController::class, 'show'])->name('admin.order.show');
