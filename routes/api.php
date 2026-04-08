<?php

declare(strict_types=1);

use App\Http\Controllers\Api\CompanyController;
use Illuminate\Support\Facades\Route;

Route::post('/company', [CompanyController::class, 'upsert']);
Route::get('/company/{edrpou}/versions', [CompanyController::class, 'versions']);
