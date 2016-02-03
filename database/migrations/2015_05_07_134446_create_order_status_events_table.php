<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderStatusEventsTable extends Migration {

    public function up() {
        Schema::create('order_status_events', function(Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('order_id')->unsigned();
            $table->integer('order_status_id')->unsigned();
            $table->integer('user_id')->unsigned();
        });
    }

    public function down() {
        Schema::drop('order_status_events');
    }

}
