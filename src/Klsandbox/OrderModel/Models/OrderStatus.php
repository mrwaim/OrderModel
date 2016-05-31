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

    private static $cache = null;

    private static function updateCache()
    {
        if (self::$cache) {
            return;
        }

        self::$cache = [];
        foreach (self::select('id', 'name')->get() as $item) {
            self::$cache[$item->name] = $item;
        }
    }

    private static function findByName($name)
    {
        self::updateCache();

        return self::$cache[$name];
    }

    public static function FirstOrder()
    {
        return self::findByName('FirstOrder');
    }

    public static function NewOrderStatus()
    {
        return self::findByName('New');
    }

    public static function PaymentUploaded()
    {
        return self::findByName('Payment_Uploaded');
    }

    public static function Approved()
    {
        return self::findByName('Approved');
    }

    public static function Rejected()
    {
        return self::findByName('Rejected');
    }

    public static function Shipped()
    {
        return self::findByName('Shipped');
    }

    public static function Received()
    {
        return self::findByName('Received');
    }

    public static function Draft()
    {
        return self::findByName('Draft');
    }
}
