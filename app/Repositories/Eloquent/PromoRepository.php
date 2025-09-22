<?php

namespace App\Repositories\Eloquent;

use App\Models\PromoCode;
use App\Repositories\Contracts\PromoRepositoryInterface;

class PromoRepository implements PromoRepositoryInterface {
  public function findByCode(string $code): ?PromoCode {
    return PromoCode::where('code',$code)
      ->where('active',true)
      ->where(function($q){
        $q->whereNull('starts_at')->orWhere('starts_at','<=',now());
      })
      ->where(function($q){
        $q->whereNull('ends_at')->orWhere('ends_at','>=',now());
      })
      ->first();
  }

  public function incrementRedemption(int $promoId): void{
    PromoCode::where('id', $promoId)->increment('redemption_count');
  }
}