<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Client\DashboardController as ClientDashboard;
use App\Http\Controllers\Client\ServiceRequestController;
use App\Http\Controllers\Client\SubscriptionController;
use App\Http\Controllers\Client\StoreController;
use App\Http\Controllers\Technician\DashboardController as TechDashboard;
use App\Http\Controllers\Technician\TaskController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Admin\InventoryController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/',[LandingController::class,'index'])->name('home');
Route::get('/about',[LandingController::class,'about'])->name('about');
Route::get('/pricing',[LandingController::class,'pricing'])->name('pricing');
Route::get('/how-it-works',[LandingController::class,'howItWorks'])->name('how-it-works');

// Auth
Route::middleware('guest')->group(function(){
    Route::get('/login',[LoginController::class,'showLogin'])->name('login');
    Route::post('/login',[LoginController::class,'login']);
    Route::get('/register',[RegisterController::class,'showRegister'])->name('register');
    Route::post('/register',[RegisterController::class,'register']);
});
Route::post('/logout',[LoginController::class,'logout'])->name('logout')->middleware('auth');

// Store (public browsing)
Route::get('/store',[StoreController::class,'index'])->name('store.index');
Route::get('/store/{product}',[StoreController::class,'show'])->name('store.show');
Route::post('/store/{product}/cart',[StoreController::class,'addToCart'])->name('store.add-cart');
Route::get('/cart',[StoreController::class,'cart'])->name('store.cart');
Route::delete('/cart/{id}',[StoreController::class,'removeFromCart'])->name('store.remove-cart');
Route::get('/checkout',[StoreController::class,'checkout'])->name('store.checkout')->middleware('auth');

// Client Dashboard
Route::middleware(['auth'])->prefix('dashboard')->name('client.')->group(function(){
    Route::get('/',[ClientDashboard::class,'index'])->name('dashboard');
    Route::get('/requests',[ServiceRequestController::class,'index'])->name('requests.index');
    Route::get('/requests/new',[ServiceRequestController::class,'create'])->name('requests.create');
    Route::post('/requests',[ServiceRequestController::class,'store'])->name('requests.store');
    Route::get('/requests/{serviceRequest}',[ServiceRequestController::class,'show'])->name('requests.show');
    Route::post('/requests/{serviceRequest}/approve',[ServiceRequestController::class,'approveReport'])->name('requests.approve');
    Route::get('/subscription',[SubscriptionController::class,'index'])->name('subscription');
});

// Technician
Route::middleware(['auth'])->prefix('tech')->name('tech.')->group(function(){
    Route::get('/dashboard',[TechDashboard::class,'index'])->name('dashboard');
    Route::post('/status',[TechDashboard::class,'updateStatus'])->name('status');
    Route::get('/tasks/{serviceRequest}',[TaskController::class,'show'])->name('tasks.show');
    Route::post('/tasks/{serviceRequest}/status',[TaskController::class,'updateRequestStatus'])->name('tasks.status');
    Route::post('/tasks/{serviceRequest}/initial-report',[TaskController::class,'submitInitialReport'])->name('tasks.initial-report');
    Route::post('/tasks/{serviceRequest}/final-report',[TaskController::class,'submitFinalReport'])->name('tasks.final-report');
});

// Admin
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function(){
    Route::get('/',[AdminDashboard::class,'index'])->name('dashboard');
    Route::get('/requests',[AdminRequestController::class,'index'])->name('requests.index');
    Route::post('/requests/{serviceRequest}/assign',[AdminRequestController::class,'assignTechnician'])->name('requests.assign');
    Route::get('/subscriptions',[AdminSubscriptionController::class,'index'])->name('subscriptions.index');
    Route::post('/subscriptions/{subscription}/grant-visit',[AdminSubscriptionController::class,'grantVisit'])->name('subscriptions.grant-visit');
    Route::get('/inventory',[InventoryController::class,'index'])->name('inventory.index');
});
