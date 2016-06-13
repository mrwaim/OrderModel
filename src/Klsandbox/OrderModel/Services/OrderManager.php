<?php

namespace Klsandbox\OrderModel\Services;

use App\Models\User;
use Klsandbox\OrderModel\Models\Order;
use Klsandbox\OrderModel\Models\ProofOfTransfer;

interface OrderManager
{
    public function approveOrder(User $user, Order $order, $approved_at = null);

    public function rejectOrder(Order $order);

    public function shipOrder(Order $order, $trackingId);

    public function orderCreated(Order $order);

    public function cancelOrder(Order $order);

    /**
    * @return Order
    */
    public function createFirstOrder(User $user, ProofOfTransfer $proofOfTransfer, array $productPricingIdHash, array $quantityHash, $isHq);

    /**
    * @return Order
    */
    public function createRestockOrder(User $user, ProofOfTransfer $proofOfTransfer, $draft, array $productPricingIdHash, array $quantityHash, $isHq, $customer = null);

    public function setPaymentUploaded($order);

    public function getOrderList(&$filter, $user);
}
