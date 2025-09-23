<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Helper\ApiResponse;
use Throwable;

class AuthController extends Controller
{
  public function __construct(private UserService $users) {}

  public function register(RegisterRequest $req) {
    try {
      $user = $this->users->register($req->validated());
      if ($user) {
        return ApiResponse::success(status: self::SUCCESS_STATUS, message: self::SUCCESS_MESSAGE, data: $user, statusCode: self::SUCCESS);
      } else {
          return ApiResponse::error(status: self::ERROR_STATUS, message: self::FAILED_MESSAGE, statusCode: self::ERROR);
        }
    } catch (Throwable $e) {
        Log::error('Exception occured'. $e->getMessage());
        return ApiResponse::error(status: self::ERROR_STATUS, message: self::EXCEPTION_MESSAGE, statusCode: self::ERROR);
    }
  }

  public function login(LoginRequest $req) {
    if (!auth()->attempt($req->only('email','password'))) {
      return ApiResponse::error(status: self::ERROR_STATUS, message: self::INVALID_CREDENTIALS_MESSAGE, statusCode: self::INVALID);
    }
    try {
      $token = auth()->user()->createToken('api-token')->accessToken;
      return ApiResponse::success(status: self::SUCCESS_STATUS, message: self::SUCCESS_MESSAGE, data: ['token'=>$token, 'user'=>auth()->user()], statusCode: self::SUCCESS);
    } catch (Throwable $e) {
      Log::error('Exception occured'. $e->getMessage());
      return ApiResponse::error(status: self::ERROR_STATUS, message: self::EXCEPTION_MESSAGE, statusCode: self::ERROR);
    }
  }

  public function logout(Request $req) {
    try {
      $token = $req->user()->token();
      $token?->revoke();
      return ApiResponse::success(status: self::SUCCESS_STATUS, message: self::LOGOUT_SUCCESS_MESSAGE, statusCode: self::SUCCESS);
    } catch (Throwable $e) {
      Log::error('Exception occured'. $e->getMessage());
      return ApiResponse::error(status: self::ERROR_STATUS, message: self::EXCEPTION_MESSAGE, statusCode: self::ERROR);
    }
  }

  public function me(Request $req) {
    try {
      $user = response()->json($req->user());
      if ($user) {
        return ApiResponse::success(status: self::SUCCESS_STATUS, message: self::SUCCESS_MESSAGE, data: $user, statusCode: self::SUCCESS);
      } else {
        return ApiResponse::error(status: self::ERROR_STATUS, message: self::FAILED_MESSAGE, statusCode: self::ERROR);
      }
    } catch (Throwable $e) {
      Log::error('Exception occured'. $e->getMessage());
      return ApiResponse::error(status: self::ERROR_STATUS, message: self::EXCEPTION_MESSAGE, statusCode: self::ERROR);
    }
  }
}
