<?php

namespace App\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use App\Models\Subscription;

class SubscriptionRepository implements SubscriptionRepositoryInterface {

  public function activeForUser(int $userId): ?Subscription {
    return Subscription::where('user_id',$userId)->where('status','active')->first();
  }

  public function create(array $data): Subscription { return Subscription::create($data); }

  public function cancelActive(int $userId): ?Subscription {
    $sub = $this->activeForUser($userId);
    if ($sub) {
      $sub->update(['status'=>'canceled','canceled_at'=>now()]);
    }
    return $sub;
  }

  // Admin reports — raw, aggregated, and subqueries

  public function totalsByPlan(): array {
    $sql = "
      SELECT p.id, p.name, COUNT(s.id) AS total_users
      FROM plans p
      LEFT JOIN subscriptions s ON s.plan_id = p.id
      GROUP BY p.id, p.name
      ORDER BY p.price ASC
    ";
    return DB::select($sql);
  }

  public function countActiveByPlan(): array {
    $sql = "
      SELECT p.id, p.name, SUM(CASE WHEN s.status='active' THEN 1 ELSE 0 END) AS active_subscriptions
      FROM plans p
      LEFT JOIN subscriptions s ON s.plan_id = p.id
      GROUP BY p.id, p.name
      ORDER BY p.price ASC
    ";
    return DB::select($sql);
  }

  public function monthlyNewSubscriptions(int $months = 6): array {
    // last N months including current
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
               WHERE DATE_FORMAT(s.created_at, '%Y-%m-01') = m.month_start
             ),0) AS new_subscriptions
      FROM months m
      ORDER BY ym ASC
    ";
    return DB::select($sql);
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