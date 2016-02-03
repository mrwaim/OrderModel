<?php

namespace Klsandbox\OrderModel\Models;

use Illuminate\Database\Eloquent\Model;
use Klsandbox\OrderModel\Services\OrderManager;
use Auth;
use App;

/**
 * Klsandbox\OrderModel\Models\Order
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bonus[] $bonuses
 * @property-read \App\Models\User $user
 * @property-read \Klsandbox\OrderModel\Models\ProductPricing $productPricing
 * @property-read \Klsandbox\OrderModel\Models\OrderStatus $orderStatus
 * @property-read \Klsandbox\OrderModel\Models\ProofOfTransfer $proofOfTransfer
 * @property integer $site_id
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $tracking_id
 * @property integer $user_id
 * @property integer $order_status_id
 * @property integer $product_pricing_id
 * @property integer $proof_of_transfer_id
 * @property string $payment_mode
 * @property string $approved_at
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereSiteId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereTrackingId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereOrderStatusId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereProductPricingId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereProofOfTransferId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order wherePaymentMode($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereApprovedAt($value)
 * @property float $price
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order wherePrice($value)
 */
class Order extends Model
{

    use \Klsandbox\SiteModel\SiteExtensions;

    public static function boot()
    {
        parent::boot();

        self::creating(function (Order $item) {
            $item->user_id = Auth::user()->id;

            if ($item->price <= 0) {
                App::abort(500, 'invalid price');
            }

            return true;
        });

        Order::created(function (Order $order) {
            App::make(OrderManager::class)->orderCreated($order);
        });
    }

    protected $table = 'orders';
    public $timestamps = true;
    protected $fillable = ['created_at', 'updated_at', 'tracking_id', 'order_status_id', 'product_pricing_id', 'proof_of_transfer_id', 'payment_mode', 'price'];

    public function bonuses()
    {
        return $this->hasMany(config('bonus.bonus_model'));
    }

    public function user()
    {
        return $this->belongsTo(config('auth.model'));
    }

    public function productPricing()
    {
        return $this->belongsTo('App\Models\ProductPricing');
    }

    public function orderStatus()
    {
        return $this->belongsTo('Klsandbox\OrderModel\Models\OrderStatus');
    }

    public function proofOfTransfer()
    {
        return $this->belongsTo('App\Models\ProofOfTransfer');
    }

    public function isApproved()
    {
        return $this->order_status_id == OrderStatus::Approved()->id || $this->order_status_id == OrderStatus::Shipped()->id || $this->order_status_id == OrderStatus::Received()->id;
    }

    public static function whereApproved($query)
    {
        return $query->where(function ($q) {
            $q->where('order_status_id', '=', OrderStatus::Approved()->id)
                ->orWhere('order_status_id', '=', OrderStatus::Shipped()->id)
                ->orWhere('order_status_id', '=', OrderStatus::Received()->id)
                ->orWhere('order_status_id', '=', OrderStatus::Approved()->id);
        });
    }

    public static function whereNotApproved($query)
    {
        return $query->where(function ($q) {
            $q->where('order_status_id', '=', OrderStatus::FirstOrder()->id)
                ->orWhere('order_status_id', '=', OrderStatus::NewOrderStatus()->id)
                ->orWhere('order_status_id', '=', OrderStatus::PaymentUploaded()->id);
        });
    }
}
