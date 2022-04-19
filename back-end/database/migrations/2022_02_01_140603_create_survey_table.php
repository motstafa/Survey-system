<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')
            ->references('id')->on('users')->onDelete('cascade');
            $table->date('date');
            $table->string('videoGames')->nullable();
            $table->string('TV')->nullable();
            $table->string('Sport')->nullable();
            $table->integer('questionsAnswerd')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survey');
    }
}
