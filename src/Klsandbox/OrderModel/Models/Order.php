<?php

namespace Klsandbox\OrderModel\Models;

use App;
use Auth;
use Illuminate\Database\Eloquent\Model;

/**
 * Klsandbox\OrderModel\Models\Order
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bonus[] $bonuses
 * @property-read \App\Models\User $user
 * @property-read \Klsandbox\OrderModel\Models\ProductPricing $productPricing
 * @property-read \Klsandbox\OrderModel\Models\OrderStatus $orderStatus
 * @property-read \Klsandbox\OrderModel\Models\ProofOfTransfer $proofOfTransfer
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
 * @property string $shipped_at
 * @property integer $shipped_by_id
 * @property string $rejected_at
 * @property integer $rejected_by_id
 * @property integer $approved_by_id
 * @property integer $customer_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\Klsandbox\OrderModel\Models\OrderItem[] $orderItems
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereShippedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereShippedById($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereRejectedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereRejectedById($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereApprovedById($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereCustomerId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Customer $customer
 * @property integer $organization_id
 * @property-read \App\Models\Organization $organization
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereOrganizationId($value)
 * @property boolean $is_hq
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereIsHq($value)
 * @property string $bill_url
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereBillUrl($value)
 * @property boolean $is_pickup
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereIsPickup($value)
 * @property integer $created_by_id
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereCreatedById($value)
 * @property string $ip_address
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Order whereIpAddress($value)
 */
class Order extends Model
{
    use App\Models\Traits\OrganizationTrait;

    public static function boot()
    {
        parent::boot();

        self::creating(function (Order $item) {

            if (!$item->order_status_id) {
                App::abort(500, 'invalid order_status_id');
            }

            return true;
        });
    }

    protected $table = 'orders';
    public $timestamps = true;
    protected $fillable = [
        'created_at',
        'updated_at',
        'tracking_id',
        'order_status_id',
        'product_id',
        'proof_of_transfer_id',
        'customer_id',
        'organization_id',
        'user_id',
        'is_hq',
        'is_pickup',
        'created_by_id',
        'ip_address'
    ];

    public function info()
    {
        $products = implode(',', $this->orderItems->map(function ($e) {
            return $e->product->name;
        })->toArray());

        $bonusCategory = implode(',', $this->orderItems->map(function ($e) {
            return $e->product->bonusCategory->name;
        })->toArray());

        return "id:$this->id status:{$this->orderStatus->name} product:$products bonusCategory:$bonusCategory";
    }

    public static function infoMap($orders)
    {
        return $orders->map(function ($e) {
            return $e->info();
        });
    }

    public function bonuses()
    {
        return $this->hasManyThrough(config('bonus.bonus_model'), OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(config('auth.model'));
    }

    public function customer()
    {
        return $this->belongsTo(App\Models\Customer::class);
    }

    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function proofOfTransfer()
    {
        return $this->belongsTo(ProofOfTransfer::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function organization()
    {
        return $this->belongsTo(App\Models\Organization::class);
    }

    public function isApproved()
    {
        return $this->order_status_id == OrderStatus::Approved()->id
        || $this->order_status_id == OrderStatus::Shipped()->id
        || $this->order_status_id == OrderStatus::Received()->id
        || $this->order_status_id == OrderStatus::Printed()->id;
    }

    public static function whereApproved($query)
    {
        return $query->where(function ($q) {
            $q->where('order_status_id', '=', OrderStatus::Approved()->id)
                ->orWhere('order_status_id', '=', OrderStatus::Shipped()->id)
                ->orWhere('order_status_id', '=', OrderStatus::Received()->id);
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

    /**
     * Returns the order that has status = approved.
     *
     * @param $query
     *
     * @return mixed
     */
    public static function whereNotFulfilled($query)
    {
        $ret = $query->where(function ($q) {
            $q->where('order_status_id', '<>', OrderStatus::Shipped()->id)
                ->Where('order_status_id', '<>', OrderStatus::FirstOrder()->id)
                ->Where('order_status_id', '<>', OrderStatus::Rejected()->id)
                ->Where('order_status_id', '<>', OrderStatus::PaymentUploaded()->id)
                ->Where('order_status_id', '<>', OrderStatus::Cancelled()->id)
                ->Where('order_status_id', '<>', OrderStatus::NewOrderStatus()->id)
                ->Where('order_status_id', '<>', OrderStatus::Printed()->id);
        });

        return $ret;
    }

    /**
     * Returns the order that has status = Printed.
     *
     * @param $query
     *
     * @return mixed
     */
    public static function whereFulfilled($query)
    {
        return $query->where(function ($q) {
            $q->where('order_status_id', '=', OrderStatus::Printed()->id)
                ->whereNull('tracking_id');
        });
    }

    /**
     * Returns the order that has status = Shipped
     * @param $query
     * @return mixed
     */
    public static function whereShipped($query)
    {
        return $query->where(function ($q) {
            $q->where('order_status_id', '=', OrderStatus::Shipped()->id);
        });
    }

    public function canApprove($auth)
    {
        if ($auth->manager) {
            if ($this->organization_id == $auth->organization_id) {
                return true;
            }
        }

        return false;
    }

    public function canShip($auth)
    {
        if ($auth->manager || $auth->staff) {
            if ($this->organization_id == $auth->organization_id) {
                return true;
            }
        }

        return false;
    }

    public function canPrint($auth)
    {
        if ($auth->manager || $auth->staff) {
            if ($this->organization_id == $auth->organization_id) {
                return true;
            }
        }

        return false;
    }

    public function canApproveState()
    {
        return $this->order_status_id < OrderStatus::Approved()->id;
    }

    public function canShipState()
    {
        return $this->order_status_id == OrderStatus::Printed()->id && $this->tracking_id == '';
    }

    public function canPrintState()
    {
        return $this->order_status_id == OrderStatus::Approved()->id;
    }
}
