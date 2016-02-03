<?php

namespace Klsandbox\OrderModel\Services;

use Klsandbox\OrderModel\Models\Order;

interface OrderManager
{
    function approveOrder(Order $order, $approved_at = null);
    function rejectOrder(Order $order);
    function shipOrder(Order $order, $trackingId);
    function orderCreated(Order $order);
}
