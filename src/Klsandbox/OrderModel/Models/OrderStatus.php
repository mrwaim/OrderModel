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
 *
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
        return self::where(['name' => 'FirstOrder'])->first();
    }

    public static function NewOrderStatus()
    {
        return self::where(['name' => 'New'])->first();
    }

    public static function PaymentUploaded()
    {
        return self::where(['name' => 'Payment_Uploaded'])->first();
    }

    public static function Approved()
    {
        return self::where(['name' => 'Approved'])->first();
    }

    public static function Rejected()
    {
        return self::where(['name' => 'Rejected'])->first();
    }

    public static function Shipped()
    {
        return self::where(['name' => 'Shipped'])->first();
    }

    public static function Received()
    {
        return self::where(['name' => 'Received'])->first();
    }

    public static function Draft()
    {
        return self::where(['name' => 'Draft'])->first();
    }
}
