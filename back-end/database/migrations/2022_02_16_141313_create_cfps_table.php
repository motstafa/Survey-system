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
        Schema::create('cfps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string(str('Countries of expertise')->lower()->snake()->toString());
            $table->string(str('short Bio')->lower()->snake()->toString());
            $table->string(str('Highest Educational Degree')->lower()->snake()->toString())->nullable();
            $table->string(str('Field of Highest Educational Degree')->lower()->snake()->toString())->nullable();
            $table->string(str('Professional Photo')->lower()->snake()->toString());
            $table->string(str('CV')->lower()->snake()->toString());
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
        Schema::dropIfExists('cfps');
    }
};
