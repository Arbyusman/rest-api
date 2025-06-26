<?php

namespace App\Repositories;

use App\Models\Status;

class StatusRepository
{
    public function getAll()
    {
        return Status::all();
    }
}
