<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'content' => ['nullable', 'string', 'max:5000', 'required_without:media'],
            'media' => ['nullable', 'array', 'max:5', 'required_without:content'],
            'media.*' => ['file', 'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/webm', 'max:20480'],
        ];
    }
}
