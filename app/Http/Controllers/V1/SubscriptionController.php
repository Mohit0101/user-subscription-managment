<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SubscriptionService;
use App\Http\Requests\Subscription\SubscribeRequest;

class SubscriptionController extends Controller
{
    public function __construct(private SubscriptionService $subs) {}

    public function subscribe(SubscribeRequest $req) {
        $s = $this->subs->subscribe($req->user()->id, $req->plan_id);
        return response()->json(['subscription'=>$s], 201);
    }

    public function cancel(Request $req) {
        $s = $this->subs->cancel($req->user()->id);
        return $s ? response()->json(['message'=>'Canceled','subscription'=>$s])
                : response()->json(['message'=>'No active subscription'], 404);
    }

    public function active(Request $req) {
        return response()->json(['subscription'=> $req->user()->activeSubscription()->with('plan')->first()]);
    }
}
