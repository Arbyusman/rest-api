<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Services\UserService;
use App\Traits\SiteTrait;

class AuthController extends Controller
{
    use SiteTrait;

    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function login(AuthRequest $request)
    {
        try {
            $result = $this->service->login($request->validated());

            return $this->jsonResponse(200, 'User login successfully', [
                'token' => $result['token'],
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse($e->getCode(), 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }

    public function logout()
    {
        try {
            $this->service->logout();

            return $this->jsonResponse(200, 'Logout successful');
        } catch (\Throwable $e) {
            return $this->jsonResponse($e->getCode(), 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }
}
