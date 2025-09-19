<?php

namespace App\Repositories\Contracts;

use App\Models\PromoCode;

interface PromoRepositoryInterface {
  public function findByCode(string $code): ?PromoCode;
  public function incrementRedemption(int $promoId): void; // optional aggregate table
}