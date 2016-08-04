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
     * @param User $user
     * @param ProofOfTransfer $proofOfTransfer
     * @param array $products
     * @param array $quantityHash
     * @param $isHq
     * @param $isPickup
     * @return Order
     */
    public function createFirstOrder(User $user, ProofOfTransfer $proofOfTransfer, array $products, array $quantityHash, $isHq, $isPickup = false);

    /**
     * @param User $user
     * @param ProofOfTransfer $proofOfTransfer
     * @param $draft
     * @param array $products
     * @param array $quantityHash
     * @param $isHq
     * @param null $customer
     * @param boolean $isPickup
     * @return Order
     */
    public function createRestockOrder(User $user, ProofOfTransfer $proofOfTransfer, $draft, array $products, array $quantityHash, $isHq, $customer = null, $isPickup = false);

    public function setPaymentUploaded($order);

    public function getOrderList(&$filter, $subFilter, $user);
}
