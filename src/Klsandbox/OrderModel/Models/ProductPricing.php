<?php

namespace Klsandbox\OrderModel\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Klsandbox\RoleModel\Role;

/**
 * Klsandbox\OrderModel\Models\ProductPricing
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $role_id
 * @property integer $product_id
 * @property float $price
 * @property integer $site_id
 * @property-read \Klsandbox\OrderModel\Models\Product $product
 * @property-read \Klsandbox\RoleModel\Role $role
 *
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProductPricing whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProductPricing whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProductPricing whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProductPricing whereRoleId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProductPricing whereProductId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProductPricing wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProductPricing whereSiteId($value)
 *
 * @property string $sku
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Group[] $groups
 *
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProductPricing whereSku($value)
 * @mixin \Eloquent
 *
 * @property float $price_east
 *
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProductPricing wherePriceEast($value)
 *
 * @property float $delivery
 *
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProductPricing whereDelivery($value)
 *
 * @property float $delivery_east
 *
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProductPricing whereDeliveryEast($value)
 */
class ProductPricing extends Model
{
    public $timestamps = true;

    /**
     * Attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [
        'price',
        'price_east',
        'delivery',
        'delivery_east',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function role()
    {
        return $this->belongsTo(\Klsandbox\RoleModel\Role::class);
    }

    public static function RestockPricingId()
    {
        return self::where('product_id', '=', Product::Restock()->id)->first()->id;
    }

    public function groups()
    {
        return $this->belongsToMany(\App\Models\Group::class, 'group_product_pricing');
    }

    public function getPriceAndDelivery($user, $customer, &$price, &$delivery)
    {
        if ($customer) {
            if ($customer->pricingArea() == 'east') {
                $price = $this->price_east;
                $delivery = $this->delivery_east;
            } else {
                $price = $this->price;
                $delivery = $this->delivery;
            }
        } else {
            $user = auth()->user();
            if ($user->pricingArea() == 'east') {
                $price = $this->price_east;
                $delivery = $this->delivery_east;
            } else {
                $price = $this->price;
                $delivery = $this->delivery;
            }
        }
    }
}
