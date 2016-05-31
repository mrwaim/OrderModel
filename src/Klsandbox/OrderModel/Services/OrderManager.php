<?php

namespace Klsandbox\OrderModel\Services;

use App\Models\User;
use Klsandbox\OrderModel\Models\Order;

interface OrderManager
{
    public function approveOrder(Order $order, $approved_at = null);
    public function rejectOrder(Order $order);
    public function shipOrder(Order $order, $trackingId);
    public function orderCreated(Order $order);
    public function cancelOrder(Order $order);
    public function createFirstOrder(User $user, $proofOfTransfer, array $productPricingIdHash, array $quantityHash);
    public function createRestockOrder(User $user, $proofOfTransfer, $draft, array $productPricingIdHash, array $quantityHash, $customer = null);
    public function setPaymentUploaded($order);
    public function getOrderList(&$filter, $user);
}
