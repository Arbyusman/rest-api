<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use App\Traits\SiteTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use SiteTrait;

    public function login(AuthRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::where('email', $data['email'])->first();

            if (! $user || ! Hash::check($data['password'], $user->password)) {
                return $this->jsonResponse(401, 'The provided credentials are incorrect.');
            }

            $token = $user->createToken($data['email'])->plainTextToken;

            return $this->jsonResponse(200, 'User login successfully', [
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse(500, 'Failed to login', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function logout()
    {
        try {
            auth('sanctum')->user()->currentAccessToken()->delete();
            return $this->jsonResponse(200, 'Logout successful');
        } catch (\Exception $e) {
            return $this->jsonResponse(500, 'Failed to logout', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
