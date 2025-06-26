<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Traits\SiteTrait;
use Illuminate\Support\Arr;
use Throwable;

class TaskController extends Controller
{
    use SiteTrait;

    protected $user;

    public function __construct()
    {
        $this->user = auth('sanctum')->user();
    }

    public function store(TaskRequest $request)
    {
        try {
            $data = Arr::except($request->validated(), ['status_id', 'report']);
            $data['assignee_id'] = $data['assignee_id'] ? $data['assignee_id'] : $this->user->id;
            $data['creator_id'] = $this->user->id;
            Task::create($data);
            return $this->jsonResponse(200, 'Task created successfully');
        } catch (Throwable $e) {
            return $this->jsonResponse(500, $e->getMessage());
        }
    }


    public function show($id)
    {
        try {
            $task = Task::findOrFail($id)
            ->load(['status:id,name', 'assignee:id,name', 'creator:id,name']);
            return $this->jsonResponse(200, 'Task retrieved successfully', [$task]);
        } catch (Throwable $e) {
            return $this->jsonResponse(404, 'Task not found');
        }
    }
    public function update(TaskRequest $request, $id)
    {
        try {
            $data = Arr::except($request->validated(), ['status_id', 'report']);
            $data['assignee_id'] = $data['assignee_id'] ? $data['assignee_id'] : $this->user->id;
            $task = Task::findOrFail($id);
            $task->update($data);

            return $this->jsonResponse(200, 'Task updated successfully');
        } catch (Throwable $e) {
            return $this->jsonResponse(500, $e->getMessage());
        }
    }
    public function status(TaskRequest $request, $id)
    {
        try {
            $data = Arr::only($request->validated(), ['status_id']);
            $task = Task::findOrFail($id);
            $task->update($data);

            return $this->jsonResponse(200, 'Task status updated successfully');
        } catch (Throwable $e) {
            return $this->jsonResponse(500, $e->getMessage());
        }
    }
    public function report(TaskRequest $request, $id)
    {
        try {
            $data = Arr::only($request->validated(), ['report']);
            $task = Task::findOrFail($id);
            $task->update($data);

            return $this->jsonResponse(200, 'Task report updated successfully');
        } catch (Throwable $e) {
            return $this->jsonResponse(500, $e->getMessage());
        }
    }
}
