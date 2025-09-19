<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface {
  public function create(array $data): User {
    $data['password'] = Hash::make($data['password']);
    return User::create($data);
  }
  public function findByEmail(string $email): ?User {
    return User::where('email',$email)->first(); 
  }
  public function find(int $id): ?User { 
    return User::find($id); 
  }
}