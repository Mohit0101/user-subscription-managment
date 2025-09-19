<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReportService;

class ReportController extends Controller
{
    public function __construct(private ReportService $reports) {}

    public function userSubscriptions() {
        return response()->json($this->reports->userSubscriptionsReport());
    }
}
