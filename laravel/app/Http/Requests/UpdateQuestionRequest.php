<?php

namespace App\Http\Requests;

use App\Models\Question;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateQuestionRequest extends FormRequest
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
                'exists:tests,id',
                'nullable',
            ],
            'question_text' => [
                'string',
                'required',
            ],
            'question_image' => [
                'array',
                'nullable',
            ],
            'question_image.*.id' => [
                'integer',
                'exists:media,id',
            ],
            'points' => [
                'integer',
                'min:-2147483648',
                'max:2147483647',
                'nullable',
            ],
        ];
    }
}
