<?php

namespace app\Services;

use App\Enums\Roles;
use App\Enums\Statuses;
use App\Repositories\TaskRepository;
use Illuminate\Support\Arr;

class TaskService
{
    protected $repository;

    protected $user;

    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
        $this->user = auth('sanctum')->user();
    }

    public function create(array $data): void
    {
        $data = Arr::except($data, ['status_id', 'report']);
        $data['creator_id'] = $this->user->id;
        $data['status_id'] = Statuses::ToDo->value;
        $data['assignee_id'] = $data['assignee_id'] ?? $this->user->id;

        if ($this->user->role_id == Roles::Staff->value && $data['assignee_id'] != $this->user->id) {
            throw new \Exception('You cannot create tasks for others');
        }

        $this->repository->create($data);
    }

    public function update(int $id, array $data): void
    {
        $task = $this->repository->find($id);
        if ($task->creator_id != $this->user->id) {
            throw new \Exception('Only can be update by the creator');
        }

        if (in_array($task->status_id, [Statuses::Doing->value, Statuses::Done->value])) {
            throw new \Exception("Cannot update when it's doing or done");
        }

        $data = Arr::except($data, ['status_id', 'report']);
        $data['assignee_id'] = $data['assignee_id'] ?? $this->user->id;

        $this->repository->update($task, $data);
    }

    public function updateStatus(int $id, array $data): void
    {
        $task = $this->repository->find($id);
        $status = $data['status_id'] ?? null;

        switch ($status) {
            case Statuses::ToDo->value:
                $this->setToDo($task);
                break;

            case Statuses::Doing->value:
                $this->setDoing($task);
                break;

            case Statuses::Done->value:
                $this->setDone($task);
                break;

            case Statuses::Canceled->value:
                $this->setCanceled($task);
                break;

            default:
                throw new \Exception('Invalid status');
        }

        $this->repository->update($task, ['status_id' => $status]);
    }

    public function updateReport(int $id, array $data): void
    {
        $task = $this->repository->find($id);

        if ($task->status_id != Statuses::Doing->value) {
            throw new \Exception('Can only be filled in when Doing status');
        }

        if ($task->creator_id != $this->user->id) {
            throw new \Exception('Can be filled in by the maker or implementer');
        }

        $this->repository->update($task, Arr::only($data, ['report']));
    }

    public function findWithRelations(int $id)
    {
        $task = $this->repository->findWithRelations($id);

        return [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'status_id' => $task?->status_id,
            'status' => [
                'name' => $task?->status->name,
            ],
            'creator_id' => $task?->creator_id,
            'creator' => [
                'name' => $task?->creator->name,
            ],
            'assignee_id' => $task?->assignee_id,
            'assignee' => [
                'name' => $task?->assignee->name,
            ],
            'report' => $task?->report,
        ];
    }

    private function setToDo($task): void
    {
        if (! empty($task->report)) {
            throw new \Exception('can only be reused if the report has not been filled');
        }

        if (! in_array($task->status_id, [Statuses::Doing->value, Statuses::Canceled->value])) {
            throw new \Exception('can only be reused if the previous status was doing or canceled');
        }

    }

    private function setDoing($task): void
    {
        if ($task->status_id !== Statuses::ToDo->value) {
            throw new \Exception('can only be used if the previous status is doing');
        }
    }

    private function setDone($task): void
    {
        if ($task->status_id !== Statuses::Doing->value) {
            throw new \Exception('can only mark as done from doing');
        }
    }

    private function setCanceled($task): void
    {
        if (! in_array($task->status_id, [Statuses::ToDo->value, Statuses::Doing->value])) {
            throw new \Exception('can only be used if the previous status is To Do or doing');
        }

        if (! empty($task->report)) {
            throw new \Exception('can only be used if the report has not been filled in');
        }

        if ($this->user->id !== $task->creator_id) {
            throw new \Exception('can only be used by the task creator');
        }
    }
}
