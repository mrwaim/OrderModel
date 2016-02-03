<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductPricingsTable extends Migration {

    public function up() {
        Schema::create('product_pricings', function(Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('role_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->decimal('price');
        });
    }

    public function down() {
        Schema::drop('product_pricings');
    }

}
