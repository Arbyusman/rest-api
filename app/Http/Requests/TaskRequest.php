<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:50',
            'description' => 'sometimes|required|string',
            'assignee_id' => 'nullable|numeric|exists:users,id',
            'status_id' => 'nullable|numeric|exists:statuses,id',
            'report' => 'sometimes|string|max:255',
        ];
    }
}
