<?php

namespace App\Repositories\Contracts;

use App\Models\Subscription;

interface SubscriptionRepositoryInterface {
  public function activeForUser(int $userId): ?Subscription;
  public function create(array $data): Subscription;
  public function cancelActive(int $userId): ?Subscription;
  public function countActiveByPlan(): array;
  public function totalsByPlan(): array;
  public function monthlyNewSubscriptions(int $months = 6): array;
  public function monthlyChurn(int $months = 6): array;
}