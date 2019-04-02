<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameChangesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('game_changes', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer("user_id");
            $table->integer("game_id");
            $table->string("type");
            $table->text("old");
            $table->text("new");
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
        Schema::drop('game_changes');
	}

}
