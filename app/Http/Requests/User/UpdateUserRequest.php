<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'g-recaptcha-response' => 'required|captcha',
            "email" => "nullable|email|unique:users,email," . Auth::id(),
            "age" => "nullable|numeric",
            "name" => "nullable|min:4|max:40",
            "number" => "nullable|numeric|min:9|unique:users,number," . Auth::id(),
            "image" => "nullable|image|mimes:jpg,png|max:5120"
        ];
    }

    public function messages(): array
    {
        return [
            'g-recaptcha-response.required' => 'لطفاً تأیید کنید که شما ربات نیستید.',
            'g-recaptcha-response.captcha' => 'اعتبارسنجی reCAPTCHA ناموفق بود. لطفاً دوباره امتحان کنید.',
        ];
    }
}
