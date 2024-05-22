<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StorePostRequest extends FormRequest
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
            'title' => ['required', 'min:3', 'max:100'],
            'body' => ['required', 'min:3', 'max:225'],
            'status' => ['required'],
            'thumbnail' => ['required', File::types(['png', 'jpg', 'jpeg'])->max(10240)],
            'categories' => ['required'],
        ];
    }
}
