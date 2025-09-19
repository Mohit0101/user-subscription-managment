<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserService {
  public function __construct(private UserRepositoryInterface $users) {}

  public function register(array $data): User {
    $data['role'] = $data['role'] ?? 'user';
    return $this->users->create($data);
  }
}