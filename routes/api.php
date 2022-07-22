<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\NurseAttController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\VisitTypeController;

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

Route::post('signin', [AuthController::class, 'signin'])->name('login');
Route::post('newuser', [AuthController::class, 'add_user']);

Route::middleware('auth:api')->group(function () {
    Route::post('signout', [AuthController::class, 'signout']);
    Route::get('users_list', [AuthController::class, 'get_all']);
    Route::post('validate', [AuthController::class, 'validate_token']);
    Route::post('search_client', [ClientController::class, 'search_client']);
    Route::resource('client', ClientController::class);
    Route::resource('nurse_attendant', NurseAttController::class);
    Route::resource('discount', DiscountController::class);
    Route::resource('service', ServiceController::class);
    Route::resource('visit_type', VisitTypeController::class);
    Route::resource('visit', VisitController::class);
});

//invalid access
Route::get('invalid', function () {
    return response()->json([
        'success' => false,
        'message' => "Invalid access"
    ], 403);
})->name('invalid_access');
