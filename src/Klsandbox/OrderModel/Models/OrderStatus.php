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
 */
class OrderStatus extends Model
{

    protected $table = 'order_statuses';
    public $timestamps = true;

    public static function FirstOrder()
    {
        return OrderStatus::firstByAttributes(['name' => 'FirstOrder']);
    }

    public static function NewOrderStatus()
    {
        return OrderStatus::firstByAttributes(['name' => 'New']);
    }

    public static function PaymentUploaded()
    {
        return OrderStatus::firstByAttributes(['name' => 'Payment_Uploaded']);
    }

    public static function Approved()
    {
        return OrderStatus::firstByAttributes(['name' => 'Approved']);
    }

    public static function Rejected()
    {
        return OrderStatus::firstByAttributes(['name' => 'Rejected']);
    }

    public static function Shipped()
    {
        return OrderStatus::firstByAttributes(['name' => 'Shipped']);
    }

    public static function Received()
    {
        return OrderStatus::firstByAttributes(['name' => 'Received']);
    }

    public static function Draft()
    {
        return OrderStatus::firstByAttributes(['name' => 'Draft']);
    }

}
