<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskAssigneesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_assignees', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('task_id')->unsigned();
            $table->foreign('task_id')->references('id')->on('task');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('standardLink_id')->unsigned()->nullable();
            $table->foreign('standardLink_id')->references('id')->on('standards_link');
            $table->boolean('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_assignees');
    }
}