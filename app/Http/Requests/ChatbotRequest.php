<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatbotRequest extends FormRequest
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
        // dd($this->all());
        $rules = [
            'description' => 'nullable|string|max:255',
            'platform' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ];

        if ($this->isMethod('post')) {
            // Store
            $rules['name'] = 'required|string|max:100';
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            // Update
            $rules['chatbot_name'] = 'required|string|max:100';
        }

        return $rules;
    }
}
