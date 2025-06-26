<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\JsonValidationException;

abstract class ApiRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new JsonValidationException($validator);
    }
}
