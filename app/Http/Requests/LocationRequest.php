<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocationRequest extends FormRequest
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
        $rules = [
            'location' => 'required|unique:locations,location',
        ];
        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $rules = [
                'location' => 'required',Rule::unique('locations','location')->ignore($this->id),
                'status' => 'required',
            ];
        }
        return $rules;
    }

    public function messages(){
        return [
            'location.required' => 'This Location is already Exists',
            'status.required' => 'This Status is Required',
        ];
    }
}
