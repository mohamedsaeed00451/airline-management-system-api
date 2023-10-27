<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisaRequest extends FormRequest
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
        return request()->isMethod('put') || request()->isMethod('patch') ?
            $this->onUpdate() : $this->onCreate();
    }

    public function onCreate()
    {
        return [
            'selling_price' => 'bail|required|numeric',
            'execution_price' => 'bail|required|numeric',
            'category_id' => 'bail|required|exists:categories,id',
            'from_company_id' => 'bail|required|exists:companies,id',
            'to_company_id' => 'bail|required|exists:companies,id',
            'is_deposit' => 'bail|required|in:0,1',
            'is_transfer' => 'bail|required|in:0,1',
            'notes' => 'bail|nullable|string',
        ];
    }

    public function onUpdate()
    {
        return [
            'selling_price' => 'bail|required|numeric',
            'execution_price' => 'bail|required|numeric',
            'from_company_id' => 'bail|required|exists:companies,id',
            'to_company_id' => 'bail|required|exists:companies,id',
            'is_deposit' => 'bail|required|in:0,1',
            'is_transfer' => 'bail|required|in:0,1',
            'notes' => 'bail|nullable|string',
        ];
    }
}
