<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Services\SubscriptionService;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Repositories\Contracts\PlanRepositoryInterface;
use App\Repositories\Contracts\PromoRepositoryInterface;
use App\Models\Subscription;
use stdClass;

class SubscriptionServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_subscribe_success_without_promo()
    {
        $userId = 1;
        $planId = 10;

        $mockSubs = Mockery::mock(SubscriptionRepositoryInterface::class);
        $mockPlans = Mockery::mock(PlanRepositoryInterface::class);
        $mockPromos = Mockery::mock(PromoRepositoryInterface::class);

        $plan = (object)['id'=>$planId, 'price'=>100];

        // activeForUser returns null
        $mockSubs->shouldReceive('activeForUser')->with($userId)->once()->andReturn(null);
        $mockPlans->shouldReceive('find')->with($planId)->once()->andReturn($plan);
        $mockSubs->shouldReceive('create')
                 ->once()
                 ->with(Mockery::on(function($arg) use ($userId, $planId) {
                     return $arg['user_id']===$userId &&
                            $arg['plan_id']===$planId &&
                            $arg['status']==='active' &&
                            $arg['promo_code']===null &&
                            $arg['promo_discount']===0;
                 }))
                 ->andReturn(new Subscription((object)['id'=>1]));

        $service = new SubscriptionService($mockSubs, $mockPlans, $mockPromos);
        $sub = $service->subscribe($userId, $planId);

        $this->assertInstanceOf(Subscription::class, $sub);
    }

    public function test_subscribe_success_with_valid_promo()
    {
        $userId = 1;
        $planId = 10;
        $promoCode = 'PROMO50';

        $mockSubs = Mockery::mock(SubscriptionRepositoryInterface::class);
        $mockPlans = Mockery::mock(PlanRepositoryInterface::class);
        $mockPromos = Mockery::mock(PromoRepositoryInterface::class);

        $plan = (object)['id'=>$planId, 'price'=>200];
        $promo = (object)['code'=>$promoCode, 'discount_fixed'=>20, 'discount_percent'=>10];

        $mockSubs->shouldReceive('activeForUser')->with($userId)->once()->andReturn(null);
        $mockPlans->shouldReceive('find')->with($planId)->once()->andReturn($plan);
        $mockPromos->shouldReceive('findByCode')->with($promoCode)->once()->andReturn($promo);
        $mockSubs->shouldReceive('create')
                 ->once()
                 ->with(Mockery::on(function($arg) use ($userId, $plan, $promo) {
                     // discount = max of discount_percent and discount_fixed but <= plan price
                     $expectedDiscount = max($promo->discount_fixed, intval(round(($promo->discount_percent/100) * $plan->price)));
                     return $arg['promo_code'] === $promo->code && $arg['promo_discount']===$expectedDiscount;
                 }))
                 ->andReturn(new Subscription((object)['id'=>2]));

        $service = new SubscriptionService($mockSubs, $mockPlans, $mockPromos);
        $sub = $service->subscribe($userId, $planId, $promoCode);

        $this->assertInstanceOf(Subscription::class, $sub);
    }

    public function test_subscribe_throws_exception_if_active_subscription_exists()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('You already have an active subscription.');

        $userId = 1;
        $planId = 10;

        $mockSubs = Mockery::mock(SubscriptionRepositoryInterface::class);
        $mockPlans = Mockery::mock(PlanRepositoryInterface::class);
        $mockPromos = Mockery::mock(PromoRepositoryInterface::class);

        $mockSubs->shouldReceive('activeForUser')->with($userId)->once()->andReturn(new Subscription((object)['id'=>100]));

        $service = new SubscriptionService($mockSubs, $mockPlans, $mockPromos);
        $service->subscribe($userId, $planId);
    }

    public function test_subscribe_throws_exception_if_plan_invalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid plan.');

        $userId = 1;
        $planId = 999;

        $mockSubs = Mockery::mock(SubscriptionRepositoryInterface::class);
        $mockPlans = Mockery::mock(PlanRepositoryInterface::class);
        $mockPromos = Mockery::mock(PromoRepositoryInterface::class);

        $mockSubs->shouldReceive('activeForUser')->with($userId)->once()->andReturn(null);
        $mockPlans->shouldReceive('find')->with($planId)->once()->andReturn(null);

        $service = new SubscriptionService($mockSubs, $mockPlans, $mockPromos);
        $service->subscribe($userId, $planId);
    }

    public function test_subscribe_throws_exception_if_promo_invalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid promo code.');

        $userId = 1;
        $planId = 10;
        $promoCode = 'INVALID';

        $mockSubs = Mockery::mock(SubscriptionRepositoryInterface::class);
        $mockPlans = Mockery::mock(PlanRepositoryInterface::class);
        $mockPromos = Mockery::mock(PromoRepositoryInterface::class);

        $plan = (object)['id'=>$planId, 'price'=>100];

        $mockSubs->shouldReceive('activeForUser')->with($userId)->once()->andReturn(null);
        $mockPlans->shouldReceive('find')->with($planId)->once()->andReturn($plan);
        $mockPromos->shouldReceive('findByCode')->with($promoCode)->once()->andReturn(null);

        $service = new SubscriptionService($mockSubs, $mockPlans, $mockPromos);
        $service->subscribe($userId, $planId, $promoCode);
    }

    public function test_cancel_active_subscription_returns_subscription()
    {
        $userId = 1;

        $mockSubs = Mockery::mock(SubscriptionRepositoryInterface::class);
        $mockPlans = Mockery::mock(PlanRepositoryInterface::class);
        $mockPromos = Mockery::mock(PromoRepositoryInterface::class);

        $subscription = new Subscription((object)['id'=>1]);

        $mockSubs->shouldReceive('cancelActive')->with($userId)->once()->andReturn($subscription);

        $service = new SubscriptionService($mockSubs, $mockPlans, $mockPromos);
        $result = $service->cancel($userId);

        $this->assertInstanceOf(Subscription::class, $result);
    }

    public function test_cancel_active_subscription_returns_null_if_none()
    {
        $userId = 1;

        $mockSubs = Mockery::mock(SubscriptionRepositoryInterface::class);
        $mockPlans = Mockery::mock(PlanRepositoryInterface::class);
        $mockPromos = Mockery::mock(PromoRepositoryInterface::class);

        $mockSubs->shouldReceive('cancelActive')->with($userId)->once()->andReturn(null);

        $service = new SubscriptionService($mockSubs, $mockPlans, $mockPromos);
        $result = $service->cancel($userId);

        $this->assertNull($result);
    }
}
