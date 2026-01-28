<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuoteController;

Route::post('/auth', [QuoteController::class, 'authenticate']);
Route::get('/auto-auth', [QuoteController::class, 'autoAuthenticate']);

Route::post('/offer', [QuoteController::class, 'getOffer']);

Route::post('/quote/save', [QuoteController::class, 'store']);

// Nomenclature endpoints
Route::get('/nomenclature/counties', [QuoteController::class, 'getCounties']);
Route::get('/nomenclature/localities/{countyCode}', [QuoteController::class, 'getLocalities']);
Route::get('/nomenclature/vehicle-types', [QuoteController::class, 'getVehicleTypes']);
Route::get('/vehicle', [QuoteController::class, 'getVehicleInfo']);
Route::get('/company/{taxId}', [QuoteController::class, 'getCompanyInfo']);

