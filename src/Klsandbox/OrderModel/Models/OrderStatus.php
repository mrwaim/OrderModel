<?php

namespace Klsandbox\OrderModel\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Klsandbox\OrderModel\Models\OrderStatus
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderStatus whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderStatus whereName($value)
 * @mixin \Eloquent
 */
class OrderStatus extends Model
{

    protected $table = 'order_statuses';
    public $timestamps = true;

    public static function FirstOrder()
    {
        return OrderStatus::where(['name' => 'FirstOrder'])->first();
    }

    public static function NewOrderStatus()
    {
        return OrderStatus::where(['name' => 'New'])->first();
    }

    public static function PaymentUploaded()
    {
        return OrderStatus::where(['name' => 'Payment_Uploaded'])->first();
    }

    public static function Approved()
    {
        return OrderStatus::where(['name' => 'Approved'])->first();
    }

    public static function Rejected()
    {
        return OrderStatus::where(['name' => 'Rejected'])->first();
    }

    public static function Shipped()
    {
        return OrderStatus::where(['name' => 'Shipped'])->first();
    }

    public static function Received()
    {
        return OrderStatus::where(['name' => 'Received'])->first();
    }

    public static function Draft()
    {
        return OrderStatus::where(['name' => 'Draft'])->first();
    }

}
