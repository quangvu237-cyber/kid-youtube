<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'youtube_url' => ['sometimes', 'url'],
            'youtube_id' => ['sometimes', 'string', 'max:32'],
            'title' => ['sometimes', 'string', 'max:255'],
            'channel' => ['sometimes', 'string', 'max:255'],
            'thumbnail' => ['sometimes', 'string', 'max:2048'],
            'status' => ['sometimes', 'string', 'in:pending,completed,failed'],
        ];
    }
}
