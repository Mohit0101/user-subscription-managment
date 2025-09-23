<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SubscriptionService;
use App\Http\Requests\Subscription\SubscribeRequest;
use App\Helper\ApiResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class SubscriptionController extends Controller
{
    public function __construct(private SubscriptionService $subs) {}

    public function subscribe(SubscribeRequest $req) {
        try {
            $s = $this->subs->subscribe($req->user()->id, $req->plan_id);
            if ($s) {
                return ApiResponse::success(status: self::SUCCESS_STATUS, message: self::SUCCESS_MESSAGE, data: ['subscription'=>$s], statusCode: self::SUCCESS);
            } else {
                return ApiResponse::error(status: self::ERROR_STATUS, message: self::FAILED_MESSAGE, statusCode: self::ERROR);
            }
        } catch (Throwable $e) {
            Log::error('Exception occured'. $e->getMessage());
            return ApiResponse::error(status: self::ERROR_STATUS, message: self::EXCEPTION_MESSAGE, statusCode: self::ERROR);
        }
    }

    public function cancel(Request $req) {
        try {
            $s = $this->subs->cancel($req->user()->id);
            if ($s) {
                return ApiResponse::success(status: self::SUCCESS_STATUS, message: self::SUBSCRIPTION_CANCELLED_MESSAGE, data: ['subscription'=>$s], statusCode: self::SUCCESS);
            } else {
                return ApiResponse::error(status: self::ERROR_STATUS, message: self::NO_ACTIVE_SUBSCRIPTION_MESSAGE, statusCode: self::ERROR);
            }
        } catch (Throwable $e) {
            Log::error('Exception occured'. $e->getMessage());
            return ApiResponse::error(status: self::ERROR_STATUS, message: self::EXCEPTION_MESSAGE, statusCode: self::ERROR);
        }
    }

    public function active(Request $req) {
        try {
            $sub = $req->user()->activeSubscription()->with('plan')->first();

            if ($sub) {
                return ApiResponse::success(status: self::SUCCESS_STATUS, message: self::SUBSCRIPTION_CANCELLED_MESSAGE, data: ['subscription'=>$sub], statusCode: self::SUCCESS);
            } else {
                return ApiResponse::error(status: self::ERROR_STATUS, message: self::NO_ACTIVE_SUBSCRIPTION_MESSAGE, statusCode: self::ERROR);
            }
        } catch (Throwable $e) {
            Log::error('Exception occured'. $e->getMessage());
            return ApiResponse::error(status: self::ERROR_STATUS, message: self::EXCEPTION_MESSAGE, statusCode: self::ERROR);
        }
    }
}
