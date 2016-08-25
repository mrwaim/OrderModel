<?php

namespace Klsandbox\OrderModel\Database\Seeds;

use Illuminate\Database\Seeder;
use Klsandbox\OrderModel\Models\OrderStatus;

class OrderStatusTableSeeder extends Seeder
{
    public function createIfNotExists($name)
    {
        $item = OrderStatus::firstOrNew(['name' => $name]);
        if (!$item->id) {
            $item->save();
        }
    }

    public function run()
    {
        $this->createIfNotExists('FirstOrder');
        $this->createIfNotExists('New');
        $this->createIfNotExists('Payment_Uploaded');
        $this->createIfNotExists('Approved');
        $this->createIfNotExists('Shipped');
        $this->createIfNotExists('Rejected');
        $this->createIfNotExists('Received');
        $this->createIfNotExists('Draft');
        $this->createIfNotExists('Cancelled');
        $this->createIfNotExists('Printed');
    }
}
