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

    /**
     * @OA\Post(
     *     path="/api/v1/users",
     *     summary="Create a new user",
     *     tags={"Users"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "role_id"},
     *
     *             @OA\Property(property="name", type="string", example="Manager"),
     *             @OA\Property(property="email", type="string", format="email", example="manager@email.com"),
     *             @OA\Property(property="password", type="string", format="password", example="rahasia"),
     *             @OA\Property(property="role_id", type="integer", example=1),
     *             @OA\Property(
     *                 property="manager_id",
     *                 type="integer",
     *                 example=1,
     *                 description="Required if role is Staff (role_id == 2)"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="metadata", type="object",
     *                 @OA\Property(property="status", type="integer", example=201),
     *                 @OA\Property(property="message", type="string", example="User created successfully")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="metadata", type="object",
     *                 @OA\Property(property="status", type="integer", example=422),
     *                 @OA\Property(property="message", type="string", example="Validation Error"),
     *                 @OA\Property(property="error", type="object",
     *                     @OA\Property(
     *                         property="email",
     *                         type="array",
     *
     *                         @OA\Items(type="string", example="The email has already been taken.")
     *                     ),
     *
     *                     @OA\Property(
     *                         property="manager_id",
     *                         type="array",
     *
     *                         @OA\Items(type="string", example="The manager_id field is required.")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="metadata", type="object",
     *                 @OA\Property(property="status", type="integer", example=500),
     *                 @OA\Property(property="message", type="string", example="Internal Server Error"),
     *                 @OA\Property(property="error", type="string", example="Something went wrong.")
     *             )
     *         )
     *     )
     * )
     */
    public function store(UserRequest $request)
    {
        try {
            $this->service->store($request->validated());

            return $this->jsonResponse(201, 'User created successfully');
        } catch (\Throwable $e) {
            return $this->jsonResponse(500, 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }
}
