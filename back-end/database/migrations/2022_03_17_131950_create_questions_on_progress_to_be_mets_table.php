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
        Schema::create('questions_on_progress_to_be_mets', function (Blueprint $table) {
            
            $table->unsignedBigInteger('answer_id');
          
            $table->integer('survey_id');
            
            $table->integer('standard_id');
           
            $table->integer('question_id');
       
            $table->integer("is_deferred")->default(0);        
            $table->date("visit_date")->nullable();
            $table->json("comment");
            $table->enum('answer', ['Met', 'Not Met','Partially Met','Not Applicable']);
            $table->timestamps();

            $table->primary('answer_id');

            $table->foreign('answer_id')->references('id')->on('survey_answers')
            ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('survey_id')->references('id')->on('surveys')
            ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('standard_id')->references('id')->on('standards')
            ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('question_id')->references('id')->on('questions')
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
        Schema::dropIfExists('questions_on_progress_to_be_mets');
    }
};
