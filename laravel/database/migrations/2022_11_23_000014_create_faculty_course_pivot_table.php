<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacultyCoursePivotTable extends Migration
{
    public function up()
    {
        Schema::create('faculty_course', function (Blueprint $table) {
            $table->unsignedBigInteger('faculty_id');
            $table->foreign('faculty_id', 'faculty_id_fk_7666195')->references('id')->on('faculties')->onDelete('cascade');
            $table->unsignedBigInteger('course_id');
            $table->foreign('course_id', 'course_id_fk_7666195')->references('id')->on('courses')->onDelete('cascade');
        });
    }
}
