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
            'media.*' => [
                'file',
                'mimetypes:image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime,video/x-msvideo,video/ogg,video/x-flv,video/3gpp,video/x-matroska',
                'max:20480'
            ],
        ];
    }
}
