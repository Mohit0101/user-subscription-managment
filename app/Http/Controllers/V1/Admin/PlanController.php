<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\PlanRepositoryInterface;
use App\Http\Requests\Plan\PlanRequest;
use App\Helper\ApiResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class PlanController extends Controller
{
    public function __construct(private PlanRepositoryInterface $plans) {}

    public function index() { 
        try {
            $plans = $this->plans->all();
            if ($plans) {
                return ApiResponse::success(status: self::SUCCESS_STATUS, message: self::SUCCESS_MESSAGE, data: $plans, statusCode: self::SUCCESS);
            } else {
                return ApiResponse::error(status: self::ERROR_STATUS, message: self::FAILED_MESSAGE, statusCode: self::ERROR);
            }
        } catch (Throwable $e) {
            Log::error('Exception occured'. $e->getMessage());
            return ApiResponse::error(status: self::ERROR_STATUS, message: self::EXCEPTION_MESSAGE, statusCode: self::ERROR);
        }
    }

    public function store(PlanRequest $req) {
        try {
            $plan = $this->plans->create($req->validated());
            if ($plan) {
                return ApiResponse::success(status: self::SUCCESS_STATUS, message: self::SUCCESS_MESSAGE, data: $plan, statusCode: self::SUCCESS);
            } else {
                return ApiResponse::error(status: self::ERROR_STATUS, message: self::FAILED_MESSAGE, statusCode: self::ERROR);
            }
        } catch (Throwable $e) {
            Log::error('Exception occured'. $e->getMessage());
            return ApiResponse::error(status: self::ERROR_STATUS, message: self::EXCEPTION_MESSAGE, statusCode: self::ERROR);
        }
    }

    public function update(PlanRequest $req, Plan $plan) {
        try {
            $plan = $this->plans->update($plan, $req->validated());
            if ($plan) {
                return ApiResponse::success(status: self::SUCCESS_STATUS, message: self::UPDATE_SUCCESS_MESSAGE, data: $plan, statusCode: self::SUCCESS);
            } else {
                return ApiResponse::error(status: self::ERROR_STATUS, message: self::FAILED_MESSAGE, statusCode: self::ERROR);
            }
        } catch (Throwable $e) {
            Log::error('Exception occured'. $e->getMessage());
            return ApiResponse::error(status: self::ERROR_STATUS, message: self::EXCEPTION_MESSAGE, statusCode: self::ERROR);
        }
    }

    public function destroy(Request $req, Plan $plan) {
         try {
            $this->plans->delete($plan);
            return ApiResponse::success(status: self::SUCCESS_STATUS, message: self::DELETE_SUCCESS_MESSAGE, statusCode: self::SUCCESS);
        } catch (Throwable $e) {
            Log::error('Exception occured'. $e->getMessage());
            return ApiResponse::error(status: self::ERROR_STATUS, message: self::EXCEPTION_MESSAGE, statusCode: self::ERROR);
        }
    }
}
