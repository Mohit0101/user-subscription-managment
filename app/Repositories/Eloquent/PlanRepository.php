<?php

namespace App\Repositories\Eloquent;

use App\Models\Plan;

class PlanRepository implements PlanRepositoryInterface {
    public function all(){ 
        return Plan::orderBy('price')->get(); 
    }

    public function find(int $id): ?Plan { 
        return Plan::find($id); 
    }

    public function create(array $data): Plan { 
        return Plan::create($data); 
    }

    public function update(Plan $plan, array $data): Plan { 
        $plan->update($data); return $plan;
    }

    public function delete(Plan $plan): void { 
        $plan->delete(); 
    }
}