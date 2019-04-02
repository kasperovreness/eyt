<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGameTypePrsistassement extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('prsistassessment_games', function(Blueprint $table)
        {
            $table->string("game_type");
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('prsistassessment_games', function(Blueprint $table)
        {
            $table->dropColumn("game_type");
        });
	}

}
