<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrdersTable extends Migration {

    public function up() {
        Schema::create('orders', function(Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('tracking_id')->nullable();
            $table->integer('user_id')->unsigned();
            $table->integer('order_status_id')->unsigned();
            $table->integer('product_pricing_id')->unsigned();
            $table->integer('proof_of_transfer_id')->unsigned()->nullable();
            $table->enum('payment_mode', array('COD', 'BankTransfer'));
            
            $table->timestamp('approved_at')->nullable();
            
            $table->decimal('price');
        });
    }

    public function down() {
        Schema::drop('orders');
    }

}
