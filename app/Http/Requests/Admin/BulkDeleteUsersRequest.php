<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkDeleteUsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'moderator'], true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userRule = $this->boolean('permanent')
            ? Rule::exists('users', 'id')->whereNotNull('deleted_at')
            : Rule::exists('users', 'id')->whereNull('deleted_at');

        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', $userRule],
            'permanent' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ids.required' => 'Select at least one user to delete.',
            'ids.min' => 'Select at least one user to delete.',
            'ids.*.exists' => 'One or more selected users are invalid for this action.',
        ];
    }
}
