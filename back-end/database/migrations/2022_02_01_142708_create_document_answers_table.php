<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_answers', function (Blueprint $table) {
            $table->id();
            $table->integer('survey_id');
            $table->foreignId('document_id');
            $table->string('path');
            $table->foreign('survey_id')
                ->on('surveys')
                ->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('document_id')
                ->on('documents')
                ->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
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
        Schema::dropIfExists('document_answers');
    }
}
