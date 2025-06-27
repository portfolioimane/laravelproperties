<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\PropertiesController as AdminPropertiesController;
use App\Http\Controllers\Api\Admin\PlansController;

use App\Http\Controllers\Api\Owner\OwnerPropertiesController;
use App\Http\Controllers\Api\Owner\OwnerContactCRMController;
use App\Http\Controllers\Api\Owner\SubscriptionController;
use App\Http\Controllers\Api\Owner\PaymentController;


use App\Http\Controllers\Api\Admin\OwnersController;

use App\Http\Controllers\Api\Customer\ContactOwnerController;
use App\Http\Controllers\Api\Customer\SearchPropertyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
use App\Http\Controllers\Api\Customer\PropertiesController;
use App\Http\Controllers\Api\Customer\AuthController;

use App\Http\Controllers\Api\Customer\ChatbotController;

use App\Http\Controllers\Api\DashboardController;


Route::post('/chatbot/message', [ChatbotController::class, 'handleMessage']);


Route::middleware('auth:sanctum')->get('/dashboard', [DashboardController::class, 'index']);





// Your other routes
Route::get('/properties', [PropertiesController::class, 'getAllProperties']);
Route::get('/popular-properties', [PropertiesController::class, 'getPopularProperties']);


Route::get('/property-options', [SearchPropertyController::class, 'getFilterOptions']);
Route::get('/search-properties', [SearchPropertyController::class, 'search']);


Route::get('properties/{property}', [PropertiesController::class, 'show']); // Fetch 

// routes/api.php
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'getUser']);


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
	    Route::apiResource('properties', AdminPropertiesController::class);
	    
	    Route::put('/properties/{propertyId}/toggle-featured', [AdminPropertiesController::class, 'toggleFeatured']);

	      Route::get('/owners', [OwnersController::class, 'index']);        // Get all owners
    Route::post('/owners', [OwnersController::class, 'store']);       // Add new owner
    Route::put('/owners/{id}', [OwnersController::class, 'update']);  

Route::get('/plans', [PlansController::class, 'index']);
Route::put('/plans/{plan}', [PlansController::class, 'update']);

 Route::get('/dashboard/owners', [DashboardController::class, 'owners']);
    Route::get('/dashboard/properties', [DashboardController::class, 'properties']);
        Route::get('/dashboard/subscriptions', [DashboardController::class, 'subscriptions']);



});

Route::prefix('owner')->middleware(['auth:sanctum', 'owner', 'check.subscription'])->group(function () {
	    Route::apiResource('ownerproperties', OwnerPropertiesController::class);

        Route::apiResource('owner_crm_contact', OwnerContactCRMController::class);
	});


Route::middleware('auth:sanctum')->prefix('owner')->group(function () {
   Route::get('/plans', [SubscriptionController::class, 'getPlans']);
    Route::get('/subscription', [SubscriptionController::class, 'getSubscription']);
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::post('/cancel-subscription', [SubscriptionController::class, 'cancelSubscription']);
    Route::get('/properties/count', [SubscriptionController::class, 'getPropertyCount']);
    Route::get('/properties/can-add', [SubscriptionController::class, 'canAddProperty']);


    Route::post('/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);
    Route::post('/paypal/verify', [PaymentController::class, 'verifyPaypalPayment']);


     Route::get('/dashboard/my-properties', [DashboardController::class, 'myProperties']);
    Route::get('/dashboard/my-contacts', [DashboardController::class, 'myContacts']);

});




Route::post('/contact-owner', [ContactOwnerController::class, 'submit']);






