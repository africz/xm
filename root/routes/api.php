<?php

use App\Http\Controllers\TestController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Route::get('profile', [UserController::class, 'show'])->middleware('auth'); 

Route::get('/test/symbols', [TestController::class, 'getSymbols']);
Route::post('/reports/stockreport', [ReportsController::class, 'stockreport']);


Route::controller(RegisterController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});




