<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsCouponTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_goods_coupon', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('goods_id')->nullable();
			$table->boolean('is_coupon')->nullable();
			$table->integer('coupon_id')->nullable();
			$table->boolean('send_times')->nullable()->comment('发送时间');
			$table->integer('send_num')->nullable()->comment('发放次数');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_goods_coupon');
	}

}
