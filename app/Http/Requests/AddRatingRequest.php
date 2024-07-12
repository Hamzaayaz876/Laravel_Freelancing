<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddRatingRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'Review' => ['required', 'string', 'max:255'],
            'number' => ['required', 'numeric', 'between:0,5'], //I want it greater or equal than 0 and less or equal 5

        ];
    }
}
