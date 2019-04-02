<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditGamesTablesType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	
		//For use with PostgreSQL database installations
		DB::statement('ALTER TABLE "early_numeracy_games" ALTER COLUMN "age" TYPE text ;');
	
		DB::statement('ALTER TABLE "vocab_games_new" ALTER COLUMN "age" TYPE text ;');
	
		DB::statement('ALTER TABLE "notthis_games" ALTER COLUMN "age" TYPE text ;');
		
		//For use with MYSQL database installations
//		DB::statement('ALTER TABLE `early_numeracy_games` MODIFY COLUMN `age` TEXT NULL;');
//		DB::statement('ALTER TABLE `vocab_games_new` MODIFY COLUMN `age` TEXT NULL;');
//		DB::statement('ALTER TABLE `notthis_games` MODIFY COLUMN `age` TEXT NULL;');
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

}
