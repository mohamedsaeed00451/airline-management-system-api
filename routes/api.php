<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeCommissionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\VisaController;
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
|
| Prefix => api
*/

define('PAGINATION_NUMBER', 10);

//*********************** login ***************************//
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('jwt.verify')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']); //Logout

    //*********************** Categories ***************************//
    Route::apiResource('/categories', CategoryController::class);

    //*********************** Visas ***************************//
    Route::apiResource('/visas', VisaController::class);

    //*********************** Get Platform Deposit ***************************//
    Route::get('/get-platform-deposit', [VisaController::class, 'getPlatformAmount']);

    //*********************** Add Platform Deposit ***************************//
    Route::post('/add-platform-deposit', [VisaController::class, 'depositToPlatform']);

    //*********************** Companies ***************************//
    Route::apiResource('/companies', CompanyController::class);

    //*********************** Reports ***************************//
    Route::get('/reports/{id}', [CompanyController::class, 'reports']);

    //*********************** Companies To List ***************************//
    Route::get('/companies-list', [CompanyController::class, 'getCompaniesToList']);

    //*********************** Expenses ***************************//
    Route::apiResource('/expenses', ExpenseController::class);

    //*********************** Employees ***************************//
    Route::apiResource('/employees', EmployeeController::class);

    //*********************** Employee Commission ***************************//
    Route::apiResource('/commissions', EmployeeCommissionController::class);

});
