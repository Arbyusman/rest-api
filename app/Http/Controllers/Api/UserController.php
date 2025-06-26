<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Traits\SiteTrait;

class UserController extends Controller
{
    use SiteTrait;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['store']);
    }

    public function store(UserRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['email_verified_at'] = now();
            $user = User::create($validated);

            return $this->jsonResponse(201, 'User created successfully', [$user]);
        } catch (\Exception $e) {
            return $this->jsonResponse(500, $e->getMessage());
        }
    }
}
