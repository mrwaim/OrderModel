<?php

namespace Klsandbox\OrderModel\Models;

use App\Models\OrderItemsUnit;
use App\Models\Organization;
use App\Models\User;
use App\Scopes\OrderItemScope;
use Illuminate\Database\Eloquent\Model;
use Klsandbox\BonusModel\Models\BonusStatus;

/**
 * Klsandbox\OrderModel\Models\OrderItem
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $product_pricing_id
 * @property integer $index
 * @property integer $quantity
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property float $product_price
 * @property-read \App\Models\Order $order
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bonus[] $bonuses
 * @property-read \Klsandbox\OrderModel\Models\ProductPricing $productPricing
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderItem whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderItem whereProductPricingId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderItem whereIndex($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderItem whereProductPrice($value)
 * @mixin \Eloquent
 * @property float $delivery
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderItem whereDelivery($value)
 * @property integer $organization_id
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderItem whereOrganizationId($value)
 * @property integer $awarded_user_id
 * @property-read \App\Models\User $awardedUser
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderItem whereAwardedUserId($value)
 * @property-read \App\Models\Organization $organization
 * @property integer $product_id
 * @property-read \Klsandbox\OrderModel\Models\Product $product
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\OrderItem whereProductId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderItemsUnit[] $units
 */
class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'index',
        'quantity',
        'product_price',
        'delivery',
        'organization_id',
        'awarded_user_id',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
//            assert($model->product_price);
        });

        static::addGlobalScope(new OrderItemScope());
    }

    /**
     * Relationship with `orders` table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(config('order.order_model'));
    }

    public function bonuses()
    {
        return $this->hasMany(config('bonus.bonus_model'));
    }

    public function activeBonuses()
    {
        return $this->bonuses()->getQuery()->where('bonus_status_id', '=', BonusStatus::Active()->id);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productPricing()
    {
        return $this->belongsTo(ProductPricing::class);
    }

    public function awardedUser()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function units()
    {
        return $this->hasMany(OrderItemsUnit::class);
    }
}
