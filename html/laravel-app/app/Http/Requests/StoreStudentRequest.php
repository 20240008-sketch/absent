<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
        return [
            'seito_id' => 'required|string|unique:students,seito_id',
            'seito_name' => 'required|string|max:255',
            'seito_number' => 'required|integer|min:1',
            'class_id' => 'required|exists:classes,id',
        ];
    }
}
