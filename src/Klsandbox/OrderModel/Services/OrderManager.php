<?php

namespace Klsandbox\OrderModel\Services;

use Klsandbox\OrderModel\Models\Order;

interface OrderManager
{
    public function approveOrder(Order $order, $approved_at = null);
    public function rejectOrder(Order $order);
    public function shipOrder(Order $order, $trackingId);
    public function orderCreated(Order $order);
    public function cancelOrder(Order $order);
    public function createFirstOrder($proofOfTransfer, array $productPricingIdHash, array $quantityHash);
    public function createRestockOrder($proofOfTransfer, $draft, array $productPricingIdHash, array $quantityHash, $customer = null);
    public function setPaymentUploaded($order);
    public function getOrderList(&$filter, $user);
}
