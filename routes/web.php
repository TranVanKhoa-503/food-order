<?php

use App\Http\Controllers\FoodController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FoodController::class, 'index'])->name('home');
