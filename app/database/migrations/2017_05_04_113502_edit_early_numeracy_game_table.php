<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditEarlyNumeracyGameTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('early_numeracy_games', function(Blueprint $table)
		{
			$table->dropColumn('child_id');
			$table->string("subject_id");
			
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('early_numeracy_games', function(Blueprint $table)
		{
			$table->string("child_id");
			$table->dropColumn('subject_id');
		});
	}

}
