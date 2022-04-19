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
        Schema::create('ngos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string(str('name of the Organization')->lower()->snake()->toString());
            $table->string(str('Countries of Operation')->lower()->snake()->toString());
            $table->string(str('Type of Organization')->lower()->snake()->toString());
            $table->year(str('Year of Establishment')->lower()->snake()->toString());
            $table->string(str('Address')->lower()->snake()->toString());
            $table->string(str('Logo')->lower()->snake()->toString())->nullable();
            $table->boolean(str('logo Disclaimer')->lower()->snake()->toString());
            $table->string(str('articles of  Association')->lower()->snake()->toString());
            $table->string(str('Establishment Notice')->lower()->snake()->toString());
            $table->string(str('bylaws')->lower()->snake()->toString());
            $table->string(str('Role or Position in Organization')->lower()->snake()->toString());
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
        Schema::dropIfExists('ngos');
    }
};
