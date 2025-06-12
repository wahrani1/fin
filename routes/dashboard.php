<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\EraController;
use App\Http\Controllers\Dashboard\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard',[DashboardController::class,'index'])
    ->middleware(['auth'])
    ->name('dashboard');


Route::resource('dashboard/sites',SiteController::class)->middleware(['auth']);
