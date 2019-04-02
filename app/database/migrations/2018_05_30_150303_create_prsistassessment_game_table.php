<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrsistassessmentGameTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('prsistassessment_games', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string("child_id");
            $table->string("session_id")->nullable();
            $table->string("assessor_name")->nullable();
            $table->string("test_name")->nullable();
            $table->string("room")->nullable();
            $table->date("dob")->nullable();
            $table->string("age")->nullable();
            $table->string("sex")->nullable();
            $table->dateTime("played_at")->nullable();
            $table->string("score")->nullable();
            $table->timestamps();
        });
        
        Schema::table('users', function(Blueprint $table)
        {
            $table->integer("prsistassessment")->default(0);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function(Blueprint $table)
        {
            $table->dropColumn("prsistassessment")->default(0);
        });
        
        if (Schema::hasTable('prsistassessment_games')) {
            Schema::drop('prsistassessment_games');
        }
	}

}
