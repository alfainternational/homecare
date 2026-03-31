<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Client\DashboardController as ClientDashboard;
use App\Http\Controllers\Client\ServiceRequestController;
use App\Http\Controllers\Client\SubscriptionController;
use App\Http\Controllers\Client\WalletController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\StoreController;
use App\Http\Controllers\Client\AddressController;
use App\Http\Controllers\Client\JobPostController;
use App\Http\Controllers\Technician\DashboardController as TechDashboard;
use App\Http\Controllers\Technician\TaskController;
use App\Http\Controllers\Technician\BidController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\MarketplaceSettingsController;
use App\Http\Controllers\Payment\WebhookController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/about', [LandingController::class, 'about'])->name('about');
Route::get('/pricing', [LandingController::class, 'pricing'])->name('pricing');
Route::get('/how-it-works', [LandingController::class, 'howItWorks'])->name('how-it-works');

// Auth — throttle: 5 attempts per minute per IP
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:10,1');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Store (public browsing, checkout requires auth)
Route::get('/store', [StoreController::class, 'index'])->name('store.index');
Route::get('/store/{product}', [StoreController::class, 'show'])->name('store.show');
Route::post('/store/{product}/cart', [StoreController::class, 'addToCart'])->name('store.add-cart');
Route::get('/cart', [StoreController::class, 'cart'])->name('store.cart');
Route::delete('/cart/{id}', [StoreController::class, 'removeFromCart'])->name('store.remove-cart');
Route::get('/checkout', [StoreController::class, 'checkoutPage'])->name('store.checkout')->middleware('auth');
Route::post('/checkout', [StoreController::class, 'checkout'])->name('store.checkout.post')->middleware('auth');

// Client Dashboard
Route::middleware(['auth', 'role:client,admin'])->prefix('dashboard')->name('client.')->group(function () {
    Route::get('/', [ClientDashboard::class, 'index'])->name('dashboard');
    Route::get('/requests', [ServiceRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/new', [ServiceRequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [ServiceRequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{serviceRequest}', [ServiceRequestController::class, 'show'])->name('requests.show');
    Route::post('/requests/{serviceRequest}/approve', [ServiceRequestController::class, 'approveReport'])->name('requests.approve');
    Route::post('/requests/{serviceRequest}/reject', [ServiceRequestController::class, 'rejectReport'])->name('requests.reject');
    Route::post('/requests/{serviceRequest}/rate', [ServiceRequestController::class, 'rate'])->name('requests.rate');
    // Aliases used in views
    Route::post('/requests/{serviceRequest}/approve-report', [ServiceRequestController::class, 'approveReport'])->name('requests.approveReport');
    Route::post('/requests/{serviceRequest}/reject-report', [ServiceRequestController::class, 'rejectReport'])->name('requests.rejectReport');
    Route::delete('/requests/{serviceRequest}', [ServiceRequestController::class, 'destroy'])->name('requests.destroy');
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription');
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet');
    Route::post('/wallet/refer', [WalletController::class, 'generateReferralLink'])->name('wallet.refer');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Addresses
    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::post('/addresses/{address}/primary', [AddressController::class, 'setPrimary'])->name('addresses.primary');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');

    // Client Marketplace
    Route::get('/marketplace', [JobPostController::class, 'index'])->name('marketplace.index');
    Route::get('/marketplace/create', [JobPostController::class, 'create'])->name('marketplace.create');
    Route::post('/marketplace', [JobPostController::class, 'store'])->name('marketplace.store');
    Route::get('/marketplace/{jobPost}', [JobPostController::class, 'show'])->name('marketplace.show');
    Route::post('/marketplace/{jobPost}/bids/{bid}/select', [JobPostController::class, 'selectBid'])->name('marketplace.select-bid');
    Route::delete('/marketplace/{jobPost}', [JobPostController::class, 'destroy'])->name('marketplace.destroy');
});

// Technician
Route::middleware(['auth', 'role:technician,admin'])->prefix('tech')->name('tech.')->group(function () {
    Route::get('/dashboard', [TechDashboard::class, 'index'])->name('dashboard');
    Route::post('/status', [TechDashboard::class, 'updateStatus'])->name('status');
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{serviceRequest}', [TaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{serviceRequest}/accept', [TaskController::class, 'accept'])->name('tasks.accept');
    Route::post('/tasks/{serviceRequest}/reject', [TaskController::class, 'reject'])->name('tasks.reject');
    Route::post('/tasks/{serviceRequest}/status', [TaskController::class, 'updateRequestStatus'])->name('tasks.status');
    Route::post('/tasks/{serviceRequest}/initial-report', [TaskController::class, 'submitInitialReport'])->name('tasks.initial-report');
    Route::post('/tasks/{serviceRequest}/final-report', [TaskController::class, 'submitFinalReport'])->name('tasks.final-report');

    // Technician Marketplace
    Route::get('/marketplace', [BidController::class, 'browse'])->name('marketplace.browse');
    Route::get('/marketplace/{jobPost}', [BidController::class, 'show'])->name('marketplace.show');
    Route::post('/marketplace/{jobPost}/bid', [BidController::class, 'store'])->name('marketplace.bid');
    Route::post('/marketplace/bids/{bid}/withdraw', [BidController::class, 'withdraw'])->name('marketplace.bid.withdraw');
    Route::get('/my-bids', [BidController::class, 'myBids'])->name('marketplace.my-bids');
});

// Admin
Route::middleware(['auth', 'role:admin,supervisor'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/requests', [AdminRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{serviceRequest}', [AdminRequestController::class, 'show'])->name('requests.show');
    Route::post('/requests/{serviceRequest}/assign', [AdminRequestController::class, 'assignTechnician'])->name('requests.assign');
    Route::post('/requests/{serviceRequest}/note', [AdminRequestController::class, 'addNote'])->name('requests.note');
    Route::get('/subscriptions', [AdminSubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('/subscriptions/{subscription}/grant-visit', [AdminSubscriptionController::class, 'grantVisit'])->name('subscriptions.grant-visit');
    Route::post('/subscriptions/{subscription}/suspend', [AdminSubscriptionController::class, 'suspend'])->name('subscriptions.suspend');
    Route::post('/subscriptions/{subscription}/activate', [AdminSubscriptionController::class, 'activate'])->name('subscriptions.activate');
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');

    // Payment Gateways
    Route::get('/payment-gateways', [PaymentGatewayController::class, 'index'])->name('payment-gateways.index');
    Route::post('/payment-gateways', [PaymentGatewayController::class, 'store'])->name('payment-gateways.store');
    Route::put('/payment-gateways/{gateway}', [PaymentGatewayController::class, 'update'])->name('payment-gateways.update');
    Route::post('/payment-gateways/{gateway}/toggle', [PaymentGatewayController::class, 'toggle'])->name('payment-gateways.toggle');

    // Service Categories
    Route::get('/service-categories', [ServiceCategoryController::class, 'index'])->name('service-categories.index');
    Route::post('/service-categories', [ServiceCategoryController::class, 'store'])->name('service-categories.store');
    Route::put('/service-categories/{serviceCategory}', [ServiceCategoryController::class, 'update'])->name('service-categories.update');
    Route::delete('/service-categories/{serviceCategory}', [ServiceCategoryController::class, 'destroy'])->name('service-categories.destroy');

    // Marketplace Settings
    Route::get('/marketplace', [MarketplaceSettingsController::class, 'index'])->name('marketplace.index');
    Route::post('/marketplace/settings', [MarketplaceSettingsController::class, 'updateSettings'])->name('marketplace.settings');
    Route::get('/marketplace/subscriptions', [MarketplaceSettingsController::class, 'technicianSubscriptions'])->name('marketplace.subscriptions');
    Route::post('/marketplace/subscriptions/grant', [MarketplaceSettingsController::class, 'grantFreeTechSubscription'])->name('marketplace.subscriptions.grant');
});

// Payment Webhooks (no auth)
Route::post('/webhooks/payment/{gateway}', [WebhookController::class, 'handle'])->name('payment.webhook');

// Payment Callbacks (auth optional — user may not be logged in after redirect)
Route::get('/payment/callback/{reference}', [WebhookController::class, 'callback'])->name('payment.callback');
Route::get('/payment/widget/{reference}', [WebhookController::class, 'checkoutWidget'])->name('payment.widget')->middleware('auth');
