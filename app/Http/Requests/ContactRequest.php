<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('Please enter your name.'),
            'email.required' => __('Please enter your email address.'),
            'email.email' => __('Please enter a valid email address.'),
            'subject.required' => __('Please enter a subject.'),
            'message.required' => __('Please enter your message.'),
            'message.min' => __('Your message must be at least 10 characters.'),
            'message.max' => __('Your message cannot exceed 5000 characters.'),
        ];
    }
}
