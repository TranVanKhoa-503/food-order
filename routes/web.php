<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminFoodController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Web Public Routes
Route::get('/', [FoodController::class, 'index'])->name('home');
Route::get('/foods/{food}', [FoodController::class, 'show'])->name('foods.show');

// Web Auth Routes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Web Auth Routes (Authenticated & Active)
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/profile', [UserController::class, 'show'])->name('profile');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [UserController::class, 'updatePassword'])->name('profile.password');
});

// API v1 Routes (Session + Cookie + CSRF per ARCHITECTURE.md)
Route::prefix('api/v1')->group(function () {
    // Public Catalog API
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category:slug}', [CategoryController::class, 'show']);
    Route::get('/foods', [FoodController::class, 'index']);
    Route::get('/foods/{food}', [FoodController::class, 'show']);

    // API Guest Routes
    Route::middleware('guest')->group(function () {
        Route::post('/auth/register', [RegisterController::class, 'register']);
        Route::post('/auth/login', [LoginController::class, 'login']);
        Route::post('/auth/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
        Route::post('/auth/reset-password', [ResetPasswordController::class, 'reset']);
    });

    // API Authenticated User Routes
    Route::middleware(['auth', 'active'])->group(function () {
        Route::post('/auth/logout', [LoginController::class, 'logout']);
        Route::get('/auth/me', [LoginController::class, 'me']);

        Route::get('/user/profile', [UserController::class, 'show']);
        Route::put('/user/profile', [UserController::class, 'update']);
        Route::put('/user/password', [UserController::class, 'updatePassword']);

        Route::post('/orders', [OrderController::class, 'store']);
    });

    // API Admin Routes
    Route::middleware(['auth', 'active', 'admin'])->prefix('admin')->group(function () {
        // Categories
        Route::get('/categories', [AdminCategoryController::class, 'index']);
        Route::post('/categories', [AdminCategoryController::class, 'store']);
        Route::get('/categories/{category}', [AdminCategoryController::class, 'show']);
        Route::put('/categories/{category}', [AdminCategoryController::class, 'update']);
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy']);

        // Foods
        Route::get('/foods', [AdminFoodController::class, 'index']);
        Route::post('/foods', [AdminFoodController::class, 'store']);
        Route::get('/foods/{food}', [AdminFoodController::class, 'show']);
        Route::put('/foods/{food}', [AdminFoodController::class, 'update']);
        Route::patch('/foods/{food}/availability', [AdminFoodController::class, 'toggleAvailability']);
    });
});
