<?php

namespace App\Http\Requests;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|numeric|exists:roles,id',
            'manager_id' => 'sometimes|numeric|exists:users,id',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $roleId = $this->input('role_id');
            $managerId = $this->input('manager_id');

            if ($roleId == Roles::Staff->value) {
                if (empty($managerId) || $managerId == null) {
                    $validator->errors()->add('manager_id', 'The manager_id field is required.');
                }
            }
        });
    }
}
