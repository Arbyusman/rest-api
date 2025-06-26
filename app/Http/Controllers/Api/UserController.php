<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Services\UserService;
use App\Traits\SiteTrait;

class UserController extends Controller
{
    use SiteTrait;

    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function store(UserRequest $request)
    {
        try {
            $this->service->register($request->validated());

            return $this->jsonResponse(201, 'User created successfully');
        } catch (\Throwable $e) {
            return $this->jsonResponse($e->getCode(), 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }
}
