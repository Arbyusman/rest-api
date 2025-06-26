<?php

namespace App\Exceptions;

use App\Traits\SiteTrait;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class JsonValidationException extends HttpResponseException
{

    use SiteTrait;
    protected int $status;
    protected array $errors;

    public function __construct(Validator $validator, int $status = 422)
    {
        $this->errors = $validator->errors()->toArray();
        $this->status = $status;

        $response = $this->jsonResponse($this->status, 'Validation Error', 
            $this->errors,
        );

        parent::__construct($response);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }
}
