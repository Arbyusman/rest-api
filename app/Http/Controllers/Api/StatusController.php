<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StatusService;
use App\Traits\SiteTrait;

class StatusController extends Controller
{
    use SiteTrait;

    protected $service;

    public function __construct(StatusService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/statuses",
     *     tags={"Statuses"},
     *     summary="Get all statuses",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of statuses",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="metadata", type="object",
     *                 @OA\Property(property="status", type="integer", example=200),
     *                 @OA\Property(property="message", type="string", example="Get statuses successfully"),
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="To Do")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return $this->jsonResponse(200, 'Get statuses successfully', [$this->service->getAll()]);
    }
}
