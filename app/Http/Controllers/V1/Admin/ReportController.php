<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Helper\ApiResponse;
use Throwable;

class ReportController extends Controller
{
    public function __construct(private ReportService $reports) {}

    public function userSubscriptions() {
        try {
            $report = $this->reports->userSubscriptionsReport();
            if ($report) {
                return ApiResponse::success(status: self::SUCCESS_STATUS, message: self::SUCCESS_MESSAGE, data: $report, statusCode: self::SUCCESS);
            } else {
                return ApiResponse::error(status: self::ERROR_STATUS, message: self::FAILED_MESSAGE, statusCode: self::ERROR);
            }
        } catch (Throwable $e) {
            return ApiResponse::error(status: self::ERROR_STATUS, message: self::EXCEPTION_MESSAGE, statusCode: self::ERROR);
        }
    }
}
