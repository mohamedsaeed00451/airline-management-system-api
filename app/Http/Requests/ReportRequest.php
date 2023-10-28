<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
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
            'report_type' => 'bail|required|in:comprehensive,implement,sale',
            'category_id' => 'bail|nullable|exists:categories,id',
            'start_date' => 'bail|nullable|date_format:Y-m-d|required_with:end_date|before:end_date',
            'end_date' => 'bail|nullable|date_format:Y-m-d|required_with:start_date|after:start_date',
            'is_deposit' => 'bail|nullable|in:0,1',
            'is_transfer' => 'bail|nullable|in:0,1',
        ];
    }
}
