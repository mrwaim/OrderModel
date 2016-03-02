<?php

namespace Klsandbox\OrderModel\Services;

use Klsandbox\OrderModel\Models\Order;

interface OrderManager
{
    function approveOrder(Order $order, $approved_at = null);
    function rejectOrder(Order $order);
    function shipOrder(Order $order, $trackingId);
    function orderCreated(Order $order);
    function cancelOrder(Order $order);
    function createFirstOrder($productPricingId, $proofOfTransfer, $paymentMode, $amount);
    function createRestockOrder($proofOfTransfer, $product_pricing_id, $paymentMode, $amount, $draft);
    function setPaymentUploaded($order);
}
