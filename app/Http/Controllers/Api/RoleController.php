<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RoleService;
use App\Traits\SiteTrait;

class RoleController extends Controller
{
    use SiteTrait;

    protected $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/roles",
     *     summary="Get all roles",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of roles",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="metadata", type="object",
     *                 @OA\Property(property="status", type="integer", example=200),
     *                 @OA\Property(property="message", type="string", example="Get Roles successfully")
     *             ),
     *             @OA\Property(property="data", type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Manager")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return $this->jsonResponse(200, 'Get Roles successfully', [$this->service->getAll()]);
    }
}
