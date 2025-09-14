<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AskChatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only authenticated users can make requests.
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'max:2000'],
            'business_id' => ['required', 'integer', 'exists:businesses,id'],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * This method is called before the validation rules are applied.
     * We can use it to merge additional data into the request.
     */
    protected function prepareForValidation()
    {
        // We ensure the business_id from the authenticated user's business
        // is part of the request data to be validated. This prevents a user
        // from asking questions to another user's assistant.
        if (Auth::user() && Auth::user()->business) {
            $this->merge([
                'business_id' => Auth::user()->business->id,
            ]);
        }
    }
}
