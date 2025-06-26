<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function register(array $data)
    {
        $data['email_verified_at'] = now();

        return $this->repository->create($data);
    }

    public function login(array $data): array
    {
        $user = $this->repository->findByEmail($data['email']);

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw new \Exception('The provided credentials are incorrect.', 401);
        }

        $token = $user->createToken($data['email'])->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(): void
    {
        Auth::user()?->currentAccessToken()?->delete();
    }
}
