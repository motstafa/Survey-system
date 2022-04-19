<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('not_applicable_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('answer_id');
            $table->foreign('answer_id')
                ->on('survey_answers')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('comment');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('not_applicable');
    }
};
