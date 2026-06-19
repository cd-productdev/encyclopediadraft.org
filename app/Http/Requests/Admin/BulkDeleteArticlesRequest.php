<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkDeleteArticlesRequest extends FormRequest
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
        $articleRule = $this->boolean('permanent')
            ? Rule::exists('articles', 'id')->whereNotNull('deleted_at')
            : Rule::exists('articles', 'id')->whereNull('deleted_at');

        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', $articleRule],
            'permanent' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ids.required' => 'Select at least one article to delete.',
            'ids.min' => 'Select at least one article to delete.',
            'ids.*.exists' => 'One or more selected articles are invalid for this action.',
        ];
    }
}
