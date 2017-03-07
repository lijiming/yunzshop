<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_order', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('订单ID');
			$table->integer('uniacid')->default(0)->comment('公众号ID');
			$table->integer('member_id')->default(0)->comment('微信唯一ID/PC注册用户编写ID');
			$table->string('order_sn', 23)->default('')->comment('订单号');
			$table->integer('price')->default(0)->comment('订单金额');
			$table->integer('goods_price')->default(0)->comment('商品金额');
			$table->boolean('status')->default(0)->comment('-1取消状态，0待支付，1为已付款，2为已发货，3为成功');
			$table->integer('create_time')->default(0)->comment('下单时间');
			$table->boolean('is_deleted')->default(0)->comment('删除');
			$table->boolean('is_member_deleted')->default(0)->comment('用户删除');
			$table->integer('finish_time')->default(0)->comment('交易完成时间');
			$table->integer('pay_time')->default(0)->comment('支付时间');
			$table->integer('send_time')->default(0)->comment('发送时间');
			$table->integer('cancel_time')->default(0)->comment('取消时间');
			$table->integer('created_at')->nullable();
			$table->integer('updated_at')->nullable()->default(0);
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
		Schema::drop('ims_yz_order');
	}

}
