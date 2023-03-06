<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacultiesTable extends Migration
{
    public function up()
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('academy_id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
            

            $table->foreign('academy_id')
                  ->references('id')
                  ->on('academies')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('faculties');
    }
}

