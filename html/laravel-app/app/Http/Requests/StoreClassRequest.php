<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassRequest extends FormRequest
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
            'class_id' => 'required|string|unique:classes,class_id',
            'class_name' => 'required|string|max:255',
            'teacher_name' => 'required|string|max:255',
            'teacher_email' => 'required|email|max:255',
            'year_id' => 'required|integer|min:2000|max:2100',
        ];
    }
}
