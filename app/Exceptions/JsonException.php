<?php

namespace App\Exceptions;

use Exception;

class JsonException extends Exception
{
    protected array $data;
    protected int $status;

    public function __construct(array $data, int $status = 500)
    {
        parent::__construct($data['metadata']['message'] ?? 'API Error', $status);
        $this->data = $data;
        $this->status = $status;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }
}
