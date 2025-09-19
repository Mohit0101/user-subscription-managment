<?php

namespace App\Services;

use App\Models\Subscription;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Repositories\Contracts\PlanRepositoryInterface;
use App\Repositories\Contracts\PromoRepositoryInterface;

class SubscriptionService {
  public function __construct(
    private SubscriptionRepositoryInterface $subs,
    private PlanRepositoryInterface $plans,
    private PromoRepositoryInterface $promos
  ) {}

  public function subscribe(int $userId, int $planId, ?string $promoCode = null): Subscription
  {
    // 1 active sub per user
    if ($active = $this->subs->activeForUser($userId)) {
      throw new \DomainException('You already have an active subscription.');
    }
    $plan = $this->plans->find($planId);
    if (!$plan) throw new \InvalidArgumentException('Invalid plan.');

    $discount = 0; $appliedCode = null;
    if ($promoCode) {
      $promo = $this->promos->findByCode($promoCode);
      if (!$promo) throw new \InvalidArgumentException('Invalid promo code.');
      $discount = min(
        $plan->price,
        max($promo->discount_fixed, intval(round(($promo->discount_percent/100) * $plan->price)))
      );
      $appliedCode = $promo->code;
    }

    return $this->subs->create([
      'user_id'=>$userId,
      'plan_id'=>$plan->id,
      'starts_at'=>now(),
      'status'=>'active',
      'promo_code'=>$appliedCode,
      'promo_discount'=>$discount
    ]);
  }

  public function cancel(int $userId): ?Subscription {
    return $this->subs->cancelActive($userId);
  }
}