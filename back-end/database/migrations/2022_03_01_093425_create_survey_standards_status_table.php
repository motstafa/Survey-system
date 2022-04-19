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
        Schema::create('survey_standards_status', function (Blueprint $table) {
            $table->id();
            $table->integer('survey_id');
            $table->integer('standard_id');
            $table->foreign('survey_id')->on('surveys')->references('id')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('standard_id')->on('standards')->references('id')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('status', ['Complete', 'Progress', 'NotStarted']);
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
        Schema::dropIfExists('survey_standards_status');
    }
};
