<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration {

    public function up() {
        Schema::create('products', function(Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->text('description');
            $table->string('image')->nullable();
            $table->boolean('is_available')->nullable();
            $table->boolean('hidden_from_ordering')->nullable();
        });
    }

    public function down() {
        Schema::drop('products');
    }

}
