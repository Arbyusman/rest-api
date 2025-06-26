<?php

namespace App\Services;

use App\Repositories\RoleRepository;

class RoleService
{
    protected $repositiry;

    public function __construct(RoleRepository $repositiry)
    {
        $this->repositiry = $repositiry;
    }

    public function getAll()
    {
        return $this->repositiry->getAll();
    }
}
