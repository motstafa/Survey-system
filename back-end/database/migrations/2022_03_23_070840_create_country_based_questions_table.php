<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('country_based_questions', function (Blueprint $table) {
     $table->id();                  
     $table->integer('question_id');
     $table->integer('standard_id');
     $table->string('title');
     $table->text('improvement');
     $table->text('info');
  	 $table->string('title_ar');
  	 $table->text('improvement_ar');
  	 $table->text('info_ar');
  	 $table->tinyInteger('required')->default(1);
  	 $table->tinyInteger('active')->default(1);
  	 $table->string('notpi');
     $table->timestamps();
     $table->unsignedBigInteger('country_id');
     $table->tinyInteger('is_not_applicable')->default(1);
    
     $table->foreign('question_id')->references('id')->on('questions')
     ->onUpdate('cascade')->onDelete('cascade');

     $table->foreign('standard_id')->references('id')->on('standards')
     ->onUpdate('cascade')->onDelete('cascade');

     $table->foreign('country_id')->references('id')->on('countries')
     ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('country_based_questions');
    }
};
