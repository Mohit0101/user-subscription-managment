<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SubscriptionService;
use App\Http\Requests\Subscription\PromoApplyRequest;

class SubscriptionController extends Controller
{
    public function subscribe(\App\Http\Requests\Subscription\PromoApplyRequest $req) {
    $s = app(\App\Services\SubscriptionService::class)
          ->subscribe($req->user()->id, $req->plan_id, $req->promo_code);
    return response()->json(['subscription'=>$s], 201);
  }
}
