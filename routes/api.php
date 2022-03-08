<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\NurseAttController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('signin', [AuthController::class, 'signin'])->name('login');
Route::post('newuser', [AuthController::class, 'add_user']);
Route::post('signout', [AuthController::class, 'signout']);

Route::middleware('auth:api')->group(function () {
    Route::post('validate', [AuthController::class, 'validate_token']);
    Route::resource('client', ClientController::class);
    Route::resource('nurse_attendant', NurseAttController::class);
});

//invalid access
Route::get('invalid', function() {
    return response()->json([
        'success' => false,
        'message' => "Invalid access"
    ], 403);
})->name('invalid_access');