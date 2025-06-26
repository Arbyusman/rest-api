<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository
{
    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    public function find(int $id): Task
    {
        return Task::findOrFail($id);
    }

    public function findWithRelations(int $id): Task
    {
        return Task::with(['status:id,name', 'assignee:id,name', 'creator:id,name'])->findOrFail($id);
    }
}
