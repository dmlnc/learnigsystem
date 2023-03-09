<?php

namespace App\Http\Requests;

use App\Models\Lesson;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreLessonRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                'required',
            ],
            'short_text' => [
                'string',
                'nullable',
            ],
            'long_text' => [
                'string',
                'nullable',
            ],
            'position' => [
                'integer',
                'min:-2147483648',
                'max:2147483647',
                'nullable',
            ],
            'is_published' => [
                'boolean',
            ],
            'video' => [
                'string',
                'nullable',
            ],
            'images' => [
                'array'
            ],
        ];
    }
}
