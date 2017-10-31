<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberRecordTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_record', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid');
			$table->integer('uid');
			$table->integer('parent_id');
			$table->integer('created_at');
			$table->integer('updated_at');
			$table->integer('deleted_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_member_record');
	}

}