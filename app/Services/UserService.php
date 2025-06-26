<?php

namespace App\Services;

use App\Enums\Roles;
use App\Models\User;
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

    public function store(array $data)
    {
        $data['email_verified_at'] = now();

        if ($data['role_id'] == Roles::Staff->value) {
            $manager = User::where('id', $data['manager_id'])
                ->where('role_id', Roles::Manager->value)
                ->first();

            if (! $manager) {
                throw new \Exception('The selected manager_id must be a user with Manager role.');
            }
        }

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
