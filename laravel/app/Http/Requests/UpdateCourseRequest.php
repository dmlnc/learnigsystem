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
            'is_published' => [
                'boolean',
            ],
            'images' => [
                'array'
            ],
            'faculties' => [
                'array',
            ],
            'faculties.*' => [
                'integer',
                'exists:faculties,id',
            ],
        ];
    }
}
