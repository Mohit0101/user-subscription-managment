<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SubscriptionService;
use App\Http\Requests\Subscription\PromoApplyRequest;
use App\Helper\ApiResponse;
use Throwable;

class SubscriptionController extends Controller
{
    public function subscribe(PromoApplyRequest $req) {
      try {
        $s = app(SubscriptionService::class)
          ->subscribe($req->user()->id, $req->plan_id, $req->promo_code);
        if ($s) {
          return ApiResponse::success(status: self::SUCCESS_STATUS, message: self::SUCCESS_MESSAGE, data: ['subscription'=>$s], statusCode: self::SUCCESS);
        } else {
          return ApiResponse::error(status: self::ERROR_STATUS, message: self::FAILED_MESSAGE, statusCode: self::ERROR);
        }
      } catch (Throwable $e) {
        return ApiResponse::error(status: self::ERROR_STATUS, message: self::EXCEPTION_MESSAGE, statusCode: self::ERROR);
      }
  }
}
