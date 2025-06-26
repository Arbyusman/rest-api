<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskReportRequest;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\TaskStatusRequest;
use App\Services\TaskService;
use App\Traits\SiteTrait;

class TaskController extends Controller
{
    use SiteTrait;

    protected $service;

    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/tasks",
     *     summary="Create a new task",
     *     tags={"Tasks"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"title", "description"},
     *
     *             @OA\Property(
     *                 property="title",
     *                 type="string",
     *                 maxLength=50,
     *                 example="Cuci AC ruang kelas",
     *                 description="Required. Max 50 characters."
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10.",
     *                 description="Required."
     *             ),
     *             @OA\Property(
     *                 property="assignee_id",
     *                 type="integer",
     *                 nullable=true,
     *                 example=null,
     *                 description="Optional"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Task created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="metadata", type="object",
     *                 @OA\Property(property="status", type="integer", example=201),
     *                 @OA\Property(property="message", type="string", example="Task created successfully")
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
     *                     @OA\Property(property="title", type="array", @OA\Items(type="string", example="The title field is required.")),
     *                     @OA\Property(property="description", type="array", @OA\Items(type="string", example="The description field is required.")),
     *                     @OA\Property(property="assignee_id", type="array", @OA\Items(type="string", example="The selected assignee_id is invalid."))
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function store(TaskRequest $request)
    {
        try {
            $this->service->create($request->validated());

            return $this->jsonResponse(201, 'Task created successfully');
        } catch (\Throwable $e) {
            return $this->jsonResponse(500, 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tasks/{id}",
     *     summary="Get a task by ID with relations",
     *     tags={"Tasks"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Task retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="metadata", type="object",
     *                 @OA\Property(property="status", type="integer", example=200),
     *                 @OA\Property(property="message", type="string", example="Task retrieved successfully")
     *             ),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Cuci AC ruang kelas"),
     *                 @OA\Property(property="description", type="string", example="Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10."),
     *                 @OA\Property(property="status_id", type="integer", example=2),
     *                 @OA\Property(property="status", type="object",
     *                     @OA\Property(property="name", type="string", example="Doing")
     *                 ),
     *                 @OA\Property(property="creator_id", type="integer", example=1),
     *                 @OA\Property(property="creator", type="object",
     *                     @OA\Property(property="name", type="string", example="Manager")
     *                 ),
     *                 @OA\Property(property="assignee_id", type="integer", example=2),
     *                 @OA\Property(property="assignee", type="object",
     *                     @OA\Property(property="name", type="string", example="Staff")
     *                 ),
     *                 @OA\Property(property="report", type="string", example="Seluruh AC ruang kelas selesai dicuci. Terdapat 1 AC di ruang kelas 6A yang perlu ditindaklanjuti dengan mengisi freon.")
     *             ))
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
    public function show($id)
    {
        try {
            $task = $this->service->findWithRelations($id);

            return $this->jsonResponse(200, 'Task retrieved successfully', [$task]);
        } catch (\Throwable $e) {
            return $this->jsonResponse(500, 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/tasks/{id}",
     *     summary="Update a task",
     *     tags={"Tasks"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="Cuci AC ruang kelas",
     *                 type="string",
     *                 maxLength=50,
     *                 example="Updated Task Title"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10."
     *             ),
     *             @OA\Property(
     *                 property="assignee_id",
     *                 type="integer",
     *                 nullable=true,
     *                 example=1
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Task updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="metadata", type="object",
     *                 @OA\Property(property="status", type="integer", example=200),
     *                 @OA\Property(property="message", type="string", example="Task updated successfully")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=422, description="Validation Error"),
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
    public function update(TaskRequest $request, $id)
    {
        try {
            $this->service->update($id, $request->validated());

            return $this->jsonResponse(200, 'Task updated successfully');
        } catch (\Throwable $e) {
            return $this->jsonResponse(500, 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/tasks/{id}/status",
     *     summary="Update task status",
     *     tags={"Tasks"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"status_id"},
     *
     *             @OA\Property(property="status_id", type="integer", example=2, description="1: To Do, 2: Doing, 3: Done, 4: Canceled")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Task status updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="metadata", type="object",
     *                 @OA\Property(property="status", type="integer", example=200),
     *                 @OA\Property(property="message", type="string", example="Task status updated successfully")
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
     *                         property="status_id",
     *                         type="array",
     *
     *                         @OA\Items(type="string", example="The status_id field is required.")
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
    public function status(TaskStatusRequest $request, $id)
    {
        try {
            $this->service->updateStatus($id, $request->validated());

            return $this->jsonResponse(200, 'Task status updated successfully');
        } catch (\Throwable $e) {
            return $this->jsonResponse(500, 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/tasks/{id}/report",
     *     summary="Update task report",
     *     tags={"Tasks"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"report"},
     *
     *             @OA\Property(
     *                 property="report",
     *                 type="string",
     *                 maxLength=255,
     *                 example="Seluruh AC ruang kelas selesai dicuci. Terdapat 1 AC di ruang kelas 6A yang perlu ditindaklanjuti dengan mengisi freon.",
     *                 description="The report field is required and must be a string"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Task report updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="metadata", type="object",
     *                 @OA\Property(property="status", type="integer", example=200),
     *                 @OA\Property(property="message", type="string", example="Task report updated successfully")
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
     *                         property="report",
     *                         type="array",
     *
     *                         @OA\Items(type="string", example="The report field is required.")
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
    public function report(TaskReportRequest $request, $id)
    {
        try {
            $this->service->updateReport($id, $request->validated());

            return $this->jsonResponse(200, 'Task report updated successfully');
        } catch (\Throwable $e) {
            return $this->jsonResponse(500, 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }
}
