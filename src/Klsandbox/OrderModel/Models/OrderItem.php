<?php

namespace Klsandbox\OrderModel\Models;

use Illuminate\Database\Eloquent\Model;
use Klsandbox\BonusModel\Models\BonusStatus;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_pricing_id', 'index', 'quantity'];

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

    /**
     * Relationship with `product_pricings` table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productPricing()
    {
        return $this->belongsTo(\Klsandbox\OrderModel\Models\ProductPricing::class);
    }
}
