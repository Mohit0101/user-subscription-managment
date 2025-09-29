<?php

namespace App\Repositories\Contracts;

use App\Models\Subscription;
use Illuminate\Support\Collection;


interface SubscriptionRepositoryInterface {
  public function activeForUser(int $userId): ?Subscription;
  public function create(array $data): Subscription;
  public function cancelActive(int $userId): ?Subscription;
  public function countActiveByPlan(): Collection;
  public function totalsByPlan(): Collection;
  public function monthlyNewSubscriptions(int $months = 6): Collection;
  public function monthlyChurn(int $months = 6): array;
}