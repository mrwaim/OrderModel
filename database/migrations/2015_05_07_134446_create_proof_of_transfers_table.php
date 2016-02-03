<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProofOfTransfersTable extends Migration {

    public function up() {
        Schema::create('proof_of_transfers', function(Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('bank_name');
            $table->string('notes');
            $table->string('image')->nullable();
            $table->decimal('amount');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('receiver_user_id')->unsigned();
        });
    }

    public function down() {
        Schema::drop('proof_of_transfers');
    }

}
