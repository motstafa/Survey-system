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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('mobile_phone_number');
            $table->string(str('First Name')->snake()->lower()->toString());
            $table->string(str('Last Name')->snake()->lower()->toString());
            $table->string(str('username')->snake()->lower()->toString());
            $table->string(str('Phone number')->snake()->lower()->toString());
            $table->foreignId('country_id')->default(1);
            $table->foreign('country_id')
                ->on('countries')
                ->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
