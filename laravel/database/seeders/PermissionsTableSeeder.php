<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_management_access',
            ],
            [
                'id'    => 2,
                'title' => 'permission_create',
            ],
            [
                'id'    => 3,
                'title' => 'permission_edit',
            ],
            [
                'id'    => 4,
                'title' => 'permission_show',
            ],
            [
                'id'    => 5,
                'title' => 'permission_delete',
            ],
            [
                'id'    => 6,
                'title' => 'permission_access',
            ],
            [
                'id'    => 7,
                'title' => 'role_create',
            ],
            [
                'id'    => 8,
                'title' => 'role_edit',
            ],
            [
                'id'    => 9,
                'title' => 'role_show',
            ],
            [
                'id'    => 10,
                'title' => 'role_delete',
            ],
            [
                'id'    => 11,
                'title' => 'role_access',
            ],
            [
                'id'    => 12,
                'title' => 'user_create',
            ],
            [
                'id'    => 13,
                'title' => 'user_edit',
            ],
            [
                'id'    => 14,
                'title' => 'user_show',
            ],
            [
                'id'    => 15,
                'title' => 'user_delete',
            ],
            [
                'id'    => 16,
                'title' => 'user_access',
            ],
            [
                'id'    => 17,
                'title' => 'course_create',
            ],
            [
                'id'    => 18,
                'title' => 'course_edit',
            ],
            [
                'id'    => 19,
                'title' => 'course_show',
            ],
            [
                'id'    => 20,
                'title' => 'course_delete',
            ],
            [
                'id'    => 21,
                'title' => 'course_access',
            ],
            [
                'id'    => 22,
                'title' => 'lesson_create',
            ],
            [
                'id'    => 23,
                'title' => 'lesson_edit',
            ],
            [
                'id'    => 24,
                'title' => 'lesson_show',
            ],
            [
                'id'    => 25,
                'title' => 'lesson_delete',
            ],
            [
                'id'    => 26,
                'title' => 'lesson_access',
            ],
            [
                'id'    => 27,
                'title' => 'test_create',
            ],
            [
                'id'    => 28,
                'title' => 'test_edit',
            ],
            [
                'id'    => 29,
                'title' => 'test_show',
            ],
            [
                'id'    => 30,
                'title' => 'test_delete',
            ],
            [
                'id'    => 31,
                'title' => 'test_access',
            ],
            [
                'id'    => 32,
                'title' => 'question_create',
            ],
            [
                'id'    => 33,
                'title' => 'question_edit',
            ],
            [
                'id'    => 34,
                'title' => 'question_show',
            ],
            [
                'id'    => 35,
                'title' => 'question_delete',
            ],
            [
                'id'    => 36,
                'title' => 'question_access',
            ],
            [
                'id'    => 37,
                'title' => 'question_option_create',
            ],
            [
                'id'    => 38,
                'title' => 'question_option_edit',
            ],
            [
                'id'    => 39,
                'title' => 'question_option_show',
            ],
            [
                'id'    => 40,
                'title' => 'question_option_delete',
            ],
            [
                'id'    => 41,
                'title' => 'question_option_access',
            ],
            [
                'id'    => 42,
                'title' => 'test_result_create',
            ],
            [
                'id'    => 43,
                'title' => 'test_result_edit',
            ],
            [
                'id'    => 44,
                'title' => 'test_result_show',
            ],
            [
                'id'    => 45,
                'title' => 'test_result_delete',
            ],
            [
                'id'    => 46,
                'title' => 'test_result_access',
            ],
            [
                'id'    => 47,
                'title' => 'test_answer_create',
            ],
            [
                'id'    => 48,
                'title' => 'test_answer_edit',
            ],
            [
                'id'    => 49,
                'title' => 'test_answer_show',
            ],
            [
                'id'    => 50,
                'title' => 'test_answer_delete',
            ],
            [
                'id'    => 51,
                'title' => 'test_answer_access',
            ],
            [
                'id'    => 52,
                'title' => 'study_access',
            ],
        ];

        Permission::insert($permissions);
    }
}
