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
use App\Http\Controllers\ServiceReportController;
use App\Http\Controllers\ServiceReportRecordController;
use App\Http\Controllers\ServiceReportConfigurationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserProfileController;

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
Route::middleware('auth:api')->group(function () {
    Route::post('newuser', [AuthController::class, 'add_user']);
    Route::get('get_user/{id}', [AuthController::class, 'get_user']);
    Route::put('edit_user/{user_id}/{profile_id}', [AuthController::class, 'update_user']);
    Route::get('dashboard_stats', [DashboardController::class, 'dashboard_stats']);
    Route::post('signout', [AuthController::class, 'signout']);
    Route::get('users_list', [AuthController::class, 'get_all']);
    Route::get('all_users', [AuthController::class, 'get_all_users']);
    Route::post('validate', [AuthController::class, 'validate_token']);
    Route::post('search_client', [ClientController::class, 'search_client']);
    Route::resource('service_report_config', ServiceReportConfigurationController::class);
    Route::resource('service_report', ServiceReportController::class);
    Route::resource('client', ClientController::class);
    Route::resource('nurse_attendant', NurseAttController::class);
    Route::resource('discount', DiscountController::class);
    Route::resource('service', ServiceController::class);
    Route::resource('visit_type', VisitTypeController::class);
    Route::resource('visit', VisitController::class);
    Route::resource('user_profile', UserProfileController::class);
    Route::get('service_report_records/{visit_id}/{report_id}/{client_id}', [ServiceReportRecordController::class, 'show']);
    Route::post('service_report_records', [ServiceReportRecordController::class, 'store']);
    Route::put('service_report_records/{id}', [ServiceReportRecordController::class, 'update']);
    Route::delete('service_report_records/{id}', [ServiceReportRecordController::class, 'destroy']);
    Route::get('my_account', [AuthController::class, 'get_my_account']);
    Route::put('update_my_account', [AuthController::class, 'update_my_account']);
});


//invalid access
Route::get('invalid', function () {
    return response()->json([
        'success' => false,
        'message' => "Invalid access"
    ], 403);
})->name('invalid_access');
