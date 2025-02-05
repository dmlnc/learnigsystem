<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'email' => [
                'required',
                'unique:users,email,' . request()->route('user')->id,
            ],
            'phone' => [

            ],
            'birthday' => [

            ],
            'password' => [
                'nullable',
            ],
            'roles' => [
                'required',
                'array',
            ],
            'faculties' => [
                'array',
            ],
            // 'roles.*.id' => [
            //     'integer',
            //     'exists:roles,id',
            // ],
        ];
    }
}
