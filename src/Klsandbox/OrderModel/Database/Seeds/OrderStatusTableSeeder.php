<?php

namespace Klsandbox\OrderModel\Database\Seeds;


use Illuminate\Database\Seeder;
use Klsandbox\OrderModel\Models\OrderStatus;

class OrderStatusTableSeeder extends Seeder {

    public function run() {
        if (OrderStatus::all()->count() > 0) {
            return;
        }

        //DB::table('order_statuses')->delete();
        // StatusSeed
        OrderStatus::create(array(
            'name' => 'FirstOrder'
        ));

        // StatusSeed
        OrderStatus::create(array(
            'name' => 'New'
        ));

        // StatusSeed
        OrderStatus::create(array(
            'name' => 'Payment_Uploaded'
        ));

        // StatusSeed
        OrderStatus::create(array(
            'name' => 'Approved'
        ));

        // StatusSeed
        OrderStatus::create(array(
            'name' => 'Shipped'
        ));

        // StatusSeed
        OrderStatus::create(array(
            'name' => 'Rejected'
        ));

        // StatusSeed
        OrderStatus::create(array(
            'name' => 'Received'
        ));
    }

}
