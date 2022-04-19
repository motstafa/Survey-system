<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSelfAssessmentUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('self_assessment_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name_of_organization');
            $table->string('email_of_organization');
            $table->string('country_of_registration');
            $table->string('name_of_director');
            $table->string('phone_number_of_organization');
            $table->string('address');
            $table->integer('year_of_establishment');
            $table->string('organization_type');
            $table->string('copy_of_articles_of_association');
            $table->string('copy_of_establishment_notice');
            $table->string('copy_of_bylaws');
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
        Schema::dropIfExists('self_assessment_users');
    }
}
