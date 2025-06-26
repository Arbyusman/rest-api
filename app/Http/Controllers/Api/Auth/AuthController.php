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

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     summary="Login user",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *
     *             @OA\Property(property="email", type="string", example="manager@email.com"),
     *             @OA\Property(property="password", type="string", example="rahasia")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Login sukses",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="metadata", type="object",
     *                 @OA\Property(property="status", type="integer", example=200),
     *                 @OA\Property(property="message", type="string", example="User login successfully")
     *             ),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="123abc456def789ghi012jkl345mno678pqr901stu234vwx567yz890"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="metadata", type="object",
     *                 @OA\Property(property="status", type="integer", example=500),
     *                 @OA\Property(property="message", type="string", example="Internal Server Error"),
     *                 @OA\Property(property="error", type="string", example="The provided credentials are incorrect.")
     *             )
     *         )
     *     )
     * )
     */
    public function login(AuthRequest $request)
    {
        try {
            $result = $this->service->login($request->validated());

            return $this->jsonResponse(200, 'User login successfully', [
                'token' => $result['token'],
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse(500, 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     summary="Logout user",
     *     tags={"Auth"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Logout sukses",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="metadata", type="object",
     *                 @OA\Property(property="status", type="integer", example=200),
     *                 @OA\Property(property="message", type="string", example="Logout successful")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="metadata", type="object",
     *                 @OA\Property(property="status", type="integer", example=500),
     *                 @OA\Property(property="message", type="string", example="Internal Server Error"),
     *                 @OA\Property(property="error", type="string", example="Some error message")
     *             )
     *         )
     *     )
     * )
     */
    public function logout()
    {
        try {
            $this->service->logout();

            return $this->jsonResponse(200, 'Logout successful');
        } catch (\Throwable $e) {
            return $this->jsonResponse(500, 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }
}
