<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAbsenceRequest extends FormRequest
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
            'seito_id' => 'required|integer|exists:students,seito_id',
            'division' => 'required|string|in:欠席,遅刻',
            'reason' => 'required|string|max:1000',
            'scheduled_time' => 'nullable|date_format:H:i',
            'absence_date' => 'required|date|after_or_equal:today',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'seito_id' => '生徒ID',
            'division' => '区分',
            'reason' => '理由',
            'scheduled_time' => '登校予定時刻',
            'absence_date' => '欠席日',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'division.in' => '区分は「欠席」または「遅刻」を選択してください。',
            'absence_date.after_or_equal' => '欠席日は本日以降を指定してください。',
        ];
    }
}
