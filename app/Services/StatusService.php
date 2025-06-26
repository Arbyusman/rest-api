<?php

namespace App\Services;

use App\Repositories\StatusRepository;

class StatusService
{
    protected $repository;

    public function __construct(StatusRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->getAll();
    }
}
