<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrsistassessmentScoreTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('prsistassessment_scores', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer("game_id");
            $table->string("item_no");
            $table->integer("item_score");
            $table->string("item_type");
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
		Schema::table('users', function(Blueprint $table)
        {
            $table->dropColumn("prsistassessment")->default(0);
        });
        
        if (Schema::hasTable('prsistassessment_scores')) {
            Schema::drop('prsistassessment_scores');
        }
	}

}
