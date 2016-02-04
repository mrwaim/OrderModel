<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrdersForShippedDetails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('orders', function(Blueprint $table)
		{
			$table->timestamp('shipped_at')->nullable();
			$table->integer('shipped_by_id')->unsigned()->nullable();
			$table->foreign('shipped_by_id')->references('id')->on('users');
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
			$table->dropColumn('shipped_at');
		});

		Schema::table('orders', function(Blueprint $table) {
			$table->dropForeign('orders_shipped_by_id_foreign');
			$table->dropColumn('shipped_by_id');
		});
	}

}
