<?php

namespace App\Services;

use App\Repositories\Contracts\SubscriptionRepositoryInterface;

class ReportService {
  public function __construct(private SubscriptionRepositoryInterface $subs) {}

  public function userSubscriptionsReport(): array {
    return [
      'totals_by_plan'        => $this->subs->totalsByPlan(),
      'active_by_plan'        => $this->subs->countActiveByPlan(),
      'monthly_new_last_6'    => $this->subs->monthlyNewSubscriptions(6),
      'monthly_churn_last_6'  => $this->subs->monthlyChurn(6),
    ];
  }
}