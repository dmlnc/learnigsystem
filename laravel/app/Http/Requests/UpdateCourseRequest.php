<?php

namespace App\Http\Requests;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateCourseRequest extends FormRequest
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
            'description' => [
                'string',
                'required',
            ],
            'price' => [
                'numeric',
                'nullable',
            ],
            'thumbnail' => [
                'array',
                'nullable',
            ],
            'thumbnail.*.id' => [
                'integer',
                'exists:media,id',
            ],
            'is_published' => [
                'boolean',
            ],
            'students' => [
                'array',
            ],
            'students.*.id' => [
                'integer',
                'exists:users,id',
            ],
        ];
    }
}
