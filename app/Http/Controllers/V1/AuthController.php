<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct(private UserService $users) {}

  public function register(RegisterRequest $req) {
    $user = $this->users->register($req->validated());
    return response()->json(['message'=>'Registered','user'=>$user], 201);
  }

  public function login(LoginRequest $req) {
    if (!auth()->attempt($req->only('email','password'))) {
      return response()->json(['message'=>'Invalid credentials'], 422);
    }
    $token = auth()->user()->createToken('api-token')->accessToken;
    return response()->json(['token'=>$token, 'user'=>auth()->user()]);
  }

  public function logout(Request $req) {
    $token = $req->user()->token();
    $token?->revoke();
    return response()->json(['message'=>'Logged out']);
  }

  public function me(Request $req) {
    return response()->json($req->user());
  }
}
