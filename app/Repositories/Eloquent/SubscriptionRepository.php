<?php

namespace App\Repositories\Eloquent;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Subscription;
use App\Models\Plan;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;


class SubscriptionRepository implements SubscriptionRepositoryInterface {

  public function activeForUser(int $userId): ?Subscription {
    return Subscription::where('user_id',$userId)->where('status','active')->first();
  }

  public function create(array $data): Subscription { 
    return Subscription::create($data); 
  }

  public function cancelActive(int $userId): ?Subscription {
    $sub = $this->activeForUser($userId);
    if ($sub) {
      $sub->update(['status'=>'canceled','canceled_at'=>now()]);
    }
    return $sub;
  }

  // Admin reports

  public function totalsByPlan(): Collection {

    $plans = Plan::leftjoin('subscriptions as s', 's.plan_id', '=', 'plans.id')
                  ->select('plans.id', 'plans.name', DB::raw('COUNT(s.id) AS total_users'))
                  ->groupBy('plans.id', 'plans.name')
                  ->orderBy('plans.price', 'asc')
                  ->get();

    return $plans;
  }

  public function countActiveByPlan(): Collection {
    $plans = Plan::leftJoin('subscriptions as s', 's.plan_id', '=', 'plans.id')
                  ->select('plans.id', 'plans.name', DB::raw("SUM(CASE WHEN s.status = 'active' THEN 1 ELSE 0 END) AS active_subscriptions"))
                  ->groupBy('plans.id', 'plans.name')
                  ->orderBy('plans.price', 'asc')
                  ->get();

    return $plans;
  }

  public function monthlyNewSubscriptions(int $months = 6): Collection {
    $months_coll = collect();

    // Generate last 6 months including current month
    for ($i = $months-1; $i >= 0; $i--) {
        $months_coll[] = Carbon::now()->subMonths($i)->startOfMonth()->format('Y-m-01');
    }

    // Get counts from DB grouped by month
    $subscriptions = Subscription::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m-01') as month_start"),
            DB::raw('COUNT(*) as new_subscriptions')
        )
        ->whereIn(DB::raw("DATE_FORMAT(created_at, '%Y-%m-01')"), $months_coll)
        ->groupBy('month_start')
        ->pluck('new_subscriptions', 'month_start')
        ->toArray();

    // Map counts to all months (fill 0 for months with no subscriptions)
    $report = collect($months_coll)->map(function($months_coll) use ($subscriptions) {
        return [
            'ym' => Carbon::parse($months_coll)->format('Y-m'),
            'new_subscriptions' => $subscriptions[$months_coll] ?? 0,
        ];  
    });

    return $report;
  }

  public function monthlyChurn(int $months = 6): array {
    $sql = "
      WITH months AS (
        SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL n MONTH), '%Y-%m-01') AS month_start
        FROM (
          SELECT 0 n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5
        ) n
      )
      SELECT DATE_FORMAT(m.month_start, '%Y-%m') AS ym,
             COALESCE((
               SELECT COUNT(1)
               FROM subscriptions s
               WHERE s.canceled_at IS NOT NULL
                 AND DATE_FORMAT(s.canceled_at, '%Y-%m-01') = m.month_start
             ),0) AS cancellations
      FROM months m
      ORDER BY ym ASC
    ";
    return DB::select($sql);
  }
}