<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserRoleStatusRequest extends FormRequest
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
            'g-recaptcha-response' => "required|captcha",
            'statuses' => 'required|array',
            'statuses.*' => 'required|in:0,1',
            'roles' => 'required|array',
            'roles.*' => 'required|exists:roles,id',
        ];
    }

    public function messages(): array
    {
        return [
            'statuses.*.in' => 'مقادیر وضعیت مورد قبول نیست',
            'roles.*.exists' => 'مقام انتخاب شده در دسترس نیست',
        ];
    }
}
