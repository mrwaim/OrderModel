<?php

namespace Klsandbox\OrderModel\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Klsandbox\OrderModel\Models\OrderStatusEvent
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $order_id
 * @property integer $order_status_id
 * @property integer $user_id
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderStatusEvent whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderStatusEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderStatusEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderStatusEvent whereOrderId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderStatusEvent whereOrderStatusId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderStatusEvent whereUserId($value)
 * @mixin \Eloquent
 */
class OrderStatusEvent extends Model
{
    protected $table = 'order_status_events';
    public $timestamps = true;
}
