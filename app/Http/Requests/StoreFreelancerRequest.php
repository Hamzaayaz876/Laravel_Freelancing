<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFreelancerRequest extends FormRequest
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
            'firstname' => ['string','max:255'],
            'lastname' => ['string','max:255'],
            'bio' => ['string'],
            'picture' => ['nullable', 'image', 'mimes:png,jpg,jpeg,gif,svg', 'max:2048']
            ,'cv' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
            'skill_name'=>['string','max:255'],
            'tags'=>['string','max:255'],
            'Category'=>['required','string','max:255']

        ];
    }
}
