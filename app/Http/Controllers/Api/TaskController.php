<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Services\TaskService;
use App\Traits\SiteTrait;

class TaskController extends Controller
{
    use SiteTrait;

    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function store(TaskRequest $request)
    {
        try {
            $this->taskService->create($request->validated());

            return $this->jsonResponse(201, 'Task created successfully');
        } catch (\Throwable $e) {
            return $this->jsonResponse(500, 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $task = $this->taskService->findWithRelations($id);

            return $this->jsonResponse(200, 'Task retrieved successfully', [$task]);
        } catch (\Throwable $e) {
            return $this->jsonResponse(500, 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }

    public function update(TaskRequest $request, $id)
    {
        try {
            $this->taskService->update($id, $request->validated());

            return $this->jsonResponse(200, 'Task updated successfully');
        } catch (\Throwable $e) {
            return $this->jsonResponse(500, 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }

    public function status(TaskRequest $request, $id)
    {
        try {
            $this->taskService->updateStatus($id, $request->validated());

            return $this->jsonResponse(200, 'Task status updated successfully');
        } catch (\Throwable $e) {
            return $this->jsonResponse(500, 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }

    public function report(TaskRequest $request, $id)
    {
        try {
            $this->taskService->updateReport($id, $request->validated());

            return $this->jsonResponse(200, 'Task report updated successfully');
        } catch (\Throwable $e) {
            return $this->jsonResponse(500, 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }
}
