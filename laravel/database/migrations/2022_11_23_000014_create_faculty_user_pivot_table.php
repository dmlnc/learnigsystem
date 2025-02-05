<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacultyUserPivotTable extends Migration
{
    public function up()
    {
        Schema::create('faculty_user', function (Blueprint $table) {
            $table->unsignedBigInteger('faculty_id');
            $table->foreign('faculty_id', 'faculty_id_fk_7666191')->references('id')->on('faculties')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'user_id_fk_7666191')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
