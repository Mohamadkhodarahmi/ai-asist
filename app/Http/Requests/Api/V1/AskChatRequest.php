<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class AskChatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * For now, we allow any authenticated user. You can add specific logic later.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // The question must be a string and is required.
            'question' => ['required', 'string', 'max:1000'],

            // We need to know which business's knowledge to use.
            // The 'exists' rule ensures the business_id is valid.
            'business_id' => ['required', 'integer', 'exists:businesses,id'],
        ];
    }
}
