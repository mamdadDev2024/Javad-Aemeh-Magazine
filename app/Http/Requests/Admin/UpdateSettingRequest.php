<?php

namespace App\Http\Requests\Admin;

use Illuminate\Container\Attributes\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->hasRole('super admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'new_category' => 'nullable|string|max:255',
            'new_scope' => 'nullable|string|max:255',
            'sections.*' => 'nullable|string',
            'linkName' => "nullable|string",
            "link" => "nullable|string",
            'titleHeader' => 'nullable|file|image|max:2048',
            'defaultContentImage' => 'nullable|file|image|max:2048',
            'titleFooter' => 'nullable|file|image|max:2048',

        ];
    }
}
