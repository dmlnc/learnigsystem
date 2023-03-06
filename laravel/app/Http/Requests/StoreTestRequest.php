<?php

namespace App\Http\Requests;

use App\Models\Test;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreTestRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'course_id' => [
                'integer',
                'exists:courses,id',
                'nullable',
            ],
            'lesson_id' => [
                'integer',
                'exists:lessons,id',
                'nullable',
            ],
            'title' => [
                'string',
                'nullable',
            ],
            'questions' => [

            ],
            'description' => [
                'string',
                'nullable',
            ],
            'is_published' => [
                'boolean',
            ],
        ];
    }
}
