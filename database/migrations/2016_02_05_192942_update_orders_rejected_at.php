<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrdersRejectedAt extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('orders', function(Blueprint $table)
		{
			$table->timestamp('rejected_at')->nullable();
			$table->integer('rejected_by_id')->unsigned()->nullable();
			$table->foreign('rejected_by_id')->references('id')->on('users');

			$table->integer('approved_by_id')->unsigned()->nullable();
			$table->foreign('approved_by_id')->references('id')->on('users');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('orders', function(Blueprint $table)
		{
			$table->dropColumn('rejected_at');
			$table->dropForeign('orders_rejected_by_id_foreign');
			$table->dropColumn('rejected_by_id');
			$table->dropForeign('orders_accepted_by_id_foreign');
			$table->dropColumn('accepted_by_id');
		});
	}
}
