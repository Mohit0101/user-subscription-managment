<?php

namespace App\Repositories\Contracts;

use App\Models\Plan;

interface PlanRepositoryInterface {
  public function all();
  public function find(int $id): ?Plan;
  public function create(array $data): Plan;
  public function update(Plan $plan, array $data): Plan;
  public function delete(Plan $plan): void;
}