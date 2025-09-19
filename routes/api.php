<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\AuthController as AuthV1;
use App\Http\Controllers\V1\SubscriptionController as SubV1;
use App\Http\Controllers\V1\Admin\PlanController as PlanV1;
use App\Http\Controllers\V1\Admin\ReportController as ReportV1;
use App\Http\Controllers\V2\SubscriptionController as SubV2;

Route::prefix('v1')->group(function(){

  // Auth
  Route::prefix('auth')->group(function(){
    Route::post('register', [AuthV1::class,'register']);
    Route::post('login', [AuthV1::class,'login']);
    Route::middleware('auth:api')->group(function(){
      Route::get('me', [AuthV1::class,'me']);
      Route::post('logout',[AuthV1::class,'logout']);
    });
  });

  // Subscriptions (user)
  Route::middleware('auth:api')->prefix('subscriptions')->group(function(){
    Route::post('subscribe', [SubV1::class,'subscribe']);
    Route::post('cancel', [SubV1::class,'cancel']);
    Route::get('active', [SubV1::class,'active']);
  });

  // Admin-only
  Route::middleware(['auth:api','admin'])->prefix('admin')->group(function(){
    Route::get('plans', [PlanV1::class,'index']);
    Route::post('plans', [PlanV1::class,'store']);
    Route::put('plans/{plan}', [PlanV1::class,'update']);
    Route::delete('plans/{plan}', [PlanV1::class,'destroy']);

    Route::get('reports/user-subscriptions', [ReportV1::class,'userSubscriptions']);
  });
});

Route::prefix('v2')->group(function(){
  Route::middleware('auth:api')->prefix('subscriptions')->group(function(){
    Route::post('subscribe', [SubV2::class,'subscribe']); // supports promo_code
  });
});