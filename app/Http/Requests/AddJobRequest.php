<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddJobRequest extends FormRequest
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
            //'client_id','Title','Description','Level','Comments_Number','Applications_Number','State','Budget','Location','Application_Dealine'
            'Title' => ['required','string','max:255'],
            'Description'=>['required','string'],
            'Level'=>['required','string'],
            'Budget'=>['required','integer'],
            'Application_Dealine'=>['required','date'],
            'skill_name'=>['required','string','max:255'],
            'tags'=>['required','string','max:255'],
            //'Category'=>['required','string','max:255']
        ];
    }
}
