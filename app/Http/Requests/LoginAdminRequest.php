<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rules;
use Illuminate\Foundation\Http\FormRequest;

class LoginAdminRequest extends FormRequest
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
                'username' => ['required', 'string', 'max:255'],
                'password' => ['required','string','min:12',Rules\Password::defaults()]
        ];
    }
}
