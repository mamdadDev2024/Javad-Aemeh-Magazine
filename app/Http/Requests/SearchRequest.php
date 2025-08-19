<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
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
        // CHANGED: Align allowed types with controllers/views (Khabar instead of New)
        return [
            "search" => "nullable|string",
            "type" => "nullable|string|in:Magazine,Article,Event,Khabar,all"
        ];
    }
}
