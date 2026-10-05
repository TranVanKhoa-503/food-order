<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDeliveryZoneController;
use App\Http\Controllers\Admin\AdminFoodController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminStoreSettingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminVoucherController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DeliveryZoneController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\Shipper\ShipperOrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherCheckController;
use Illuminate\Support\Facades\Route;

// Web Public Routes
Route::get('/', [FoodController::class, 'index'])->name('home');
Route::get('/foods/{food}', [FoodController::class, 'show'])->name('foods.show');

// Web Auth Routes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:auth-register');

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:auth-login');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email')->middleware('throttle:auth-forgot-password');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Web Auth Routes (Authenticated & Active)
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/profile', [UserController::class, 'show'])->name('profile');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [UserController::class, 'updatePassword'])->name('profile.password');

    // Customer Web Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

// Web Admin Routes
Route::middleware(['auth', 'active', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/export', [AdminOrderController::class, 'export'])->name('orders.export');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('/foods', [AdminFoodController::class, 'index'])->name('foods.index');
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/vouchers', [AdminVoucherController::class, 'index'])->name('vouchers.index');
    Route::get('/settings', [AdminStoreSettingController::class, 'edit'])->name('settings.edit');
});

// Web Shipper Routes
Route::middleware(['auth', 'active', 'shipper'])->prefix('shipper')->name('shipper.')->group(function () {
    Route::get('/orders', [ShipperOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [ShipperOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [ShipperOrderController::class, 'updateStatus'])->name('orders.status');
});

// API v1 Routes (Session + Cookie + CSRF per ARCHITECTURE.md)
Route::prefix('api/v1')->group(function () {
    // Public Catalog API
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category:slug}', [CategoryController::class, 'show']);
    Route::get('/delivery-zones', [DeliveryZoneController::class, 'index']);
    Route::get('/foods', [FoodController::class, 'index']);
    Route::get('/foods/{food}', [FoodController::class, 'show']);
    Route::post('/recommendations', [RecommendationController::class, 'store']);
    Route::post('/vouchers/check', [VoucherCheckController::class, 'check']);

    // API Guest Routes
    Route::middleware('guest')->group(function () {
        Route::post('/auth/register', [RegisterController::class, 'register'])->middleware('throttle:auth-register');
        Route::post('/auth/login', [LoginController::class, 'login'])->middleware('throttle:auth-login');
        Route::post('/auth/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('throttle:auth-forgot-password');
        Route::post('/auth/reset-password', [ResetPasswordController::class, 'reset']);
    });

    // API Authenticated User Routes
    Route::middleware(['auth', 'active'])->group(function () {
        Route::post('/auth/logout', [LoginController::class, 'logout']);
        Route::get('/auth/me', [LoginController::class, 'me']);

        // Customer notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::patch('/notifications/read-all', [NotificationController::class, 'markAllRead']);
        Route::patch('/notifications/{notificationId}/read', [NotificationController::class, 'markRead']);

        Route::get('/user/profile', [UserController::class, 'show']);
        Route::put('/user/profile', [UserController::class, 'update']);
        Route::put('/user/password', [UserController::class, 'updatePassword']);

        // Orders
        Route::get('/orders', [OrderController::class, 'index']);
        Route::post('/orders', [OrderController::class, 'store'])->middleware('throttle:order-checkout');
        Route::get('/orders/{order}', [OrderController::class, 'show']);
        Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel']);
    });

    // API Admin Routes
    Route::middleware(['auth', 'active', 'admin'])->prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

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

        // Orders
        Route::get('/orders', [AdminOrderController::class, 'index']);
        Route::get('/orders/export', [AdminOrderController::class, 'export']);
        Route::get('/orders/{order}', [AdminOrderController::class, 'show']);
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus']);

        // Users
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::get('/users/{user}', [AdminUserController::class, 'show']);
        Route::patch('/users/{user}/status', [AdminUserController::class, 'toggleStatus']);

        // Vouchers
        Route::get('/vouchers', [AdminVoucherController::class, 'index']);
        Route::post('/vouchers', [AdminVoucherController::class, 'store']);
        Route::get('/vouchers/{voucher}', [AdminVoucherController::class, 'show']);
        Route::put('/vouchers/{voucher}', [AdminVoucherController::class, 'update']);
        Route::delete('/vouchers/{voucher}', [AdminVoucherController::class, 'destroy']);

        // Store settings
        Route::get('/settings', [AdminStoreSettingController::class, 'edit']);
        Route::put('/settings', [AdminStoreSettingController::class, 'update']);

        // Delivery zones
        Route::get('/delivery-zones', [AdminDeliveryZoneController::class, 'index']);
        Route::post('/delivery-zones', [AdminDeliveryZoneController::class, 'store']);
        Route::put('/delivery-zones/{deliveryZone}', [AdminDeliveryZoneController::class, 'update']);
        Route::delete('/delivery-zones/{deliveryZone}', [AdminDeliveryZoneController::class, 'destroy']);
    });

    // API Shipper Routes
    Route::middleware(['auth', 'active', 'shipper'])->prefix('shipper')->group(function () {
        Route::get('/orders', [ShipperOrderController::class, 'index']);
        Route::get('/orders/{order}', [ShipperOrderController::class, 'show']);
        Route::patch('/orders/{order}/status', [ShipperOrderController::class, 'updateStatus']);
    });
});
