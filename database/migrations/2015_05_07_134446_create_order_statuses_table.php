<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderStatusesTable extends Migration {

    public function up() {
        Schema::create('order_statuses', function(Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name')->unique();
        });
    }

    public function down() {
        Schema::drop('order_statuses');
    }

}
