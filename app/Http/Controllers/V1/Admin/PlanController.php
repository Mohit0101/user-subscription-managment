<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\PlanRepositoryInterface;
use App\Http\Requests\Plan\PlanRequest;

class PlanController extends Controller
{
    public function __construct(private PlanRepositoryInterface $plans) {}

    public function index() { 
        return response()->json($this->plans->all());
    }

    public function store(PlanRequest $req) {
        return response()->json($this->plans->create($req->validated()), 201);
    }

    public function update(PlanRequest $req, Plan $plan) {
        return response()->json($this->plans->update($plan, $req->validated()));
    }

    public function destroy(Request $req, Plan $plan) {
        $this->plans->delete($plan);
        return response()->json(['message'=>'Deleted']);
    }
}
