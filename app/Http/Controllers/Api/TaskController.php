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

    public function store(TaskRequest $request)
    {
        try {
            $this->service->create($request->validated());

            return $this->jsonResponse(201, 'Task created successfully');
        } catch (\Throwable $e) {
            return $this->jsonResponse(500, 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $task = $this->service->findWithRelations($id);

            return $this->jsonResponse(200, 'Task retrieved successfully', [$task]);
        } catch (\Throwable $e) {
            return $this->jsonResponse(500, 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }

    public function update(TaskRequest $request, $id)
    {
        try {
            $this->service->update($id, $request->validated());

            return $this->jsonResponse(200, 'Task updated successfully');
        } catch (\Throwable $e) {
            return $this->jsonResponse(500, 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }

    public function status(TaskStatusRequest $request, $id)
    {
        try {
            $this->service->updateStatus($id, $request->validated());

            return $this->jsonResponse(200, 'Task status updated successfully');
        } catch (\Throwable $e) {
            return $this->jsonResponse(500, 'Internal Server Error', ['error' => $e->getMessage()]);
        }
    }

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
