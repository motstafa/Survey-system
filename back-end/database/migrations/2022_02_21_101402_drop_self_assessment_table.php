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
        Schema::table('self_assessment_users', function (Blueprint $table) {
            $table->dropForeign('self_assessment_users_user_id_foreign');
        });
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropForeign('surveys_user_id_foreign');
        });

        Schema::dropIfExists('self_assessment_users');
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
