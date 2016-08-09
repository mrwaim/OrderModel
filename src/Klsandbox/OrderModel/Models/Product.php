<?php

namespace Klsandbox\OrderModel\Models;

use App\Models\BonusCategory;
use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Klsandbox\RoleModel\Role;

/**
 * Klsandbox\OrderModel\Models\Product
 *
 * @property integer $site_id
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @property string $description
 * @property string $image
 * @property boolean $is_available
 * @property boolean $hidden_from_ordering
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereSiteId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereImage($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereIsAvailable($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereHiddenFromOrdering($value)
 * @property integer $bonus_category_id
 * @property-read \Klsandbox\OrderModel\Models\ProductPricing $productPricing
 * @property-read \App\Models\BonusCategory $bonusCategory
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereBonusCategoryId($value)
 * @mixin \Eloquent
 * @property integer $max_quantity
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereMaxQuantity($value)
 * @property integer $min_quantity
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereMinQuantity($value)
 * @property boolean $is_hq
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereIsHq($value)
 * @property boolean $for_customer
 * @property boolean $new_user
 * @property boolean $is_membership
 * @property integer $membership_group_id
 * @property-read \App\Models\Group $MembershipGroup
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereForCustomer($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereNewUser($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereIsMembership($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereMembershipGroupId($value)
 * @property boolean $award_parent
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereAwardParent($value)
 * @property integer $max_purchase_count
 * @property string $expiry_date
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereMaxPurchaseCount($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereExpiryDate($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Klsandbox\OrderModel\Models\ProductUnit[] $units
 * @property integer $role_id
 * @property float $price
 * @property float $price_east
 * @property float $delivery
 * @property float $delivery_east
 * @property integer $group_id
 * @property-read \App\Models\Group $group
 * @property-read \Klsandbox\RoleModel\Role $role
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereRoleId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product wherePriceEast($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereDelivery($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereDeliveryEast($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\Product whereGroupId($value)
 */
class Product extends Model
{
    protected $fillable = [
        'name',
        'image',
        'description',
        'bonus_category_id',
        'min_quantity',
        'max_quantity',
        'is_available',
        'hidden_from_ordering',
        'is_hq',
        'for_customer',
        'new_user',
        'is_membership',
        'membership_group_id',
        'award_parent',
        'max_purchase_count',
        'expiry_date',
        'role_id',
        'price',
        'price_east',
        'delivery',
        'delivery_east',
        'group_id',
    ];

    protected $table = 'products';
    public $timestamps = true;

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function units()
    {
        return $this->belongsToMany(ProductUnit::class, 'products_product_units', 'product_id', 'product_unit_id')
            ->withPivot(['quantity']);
    }

    public function isOtherProduct()
    {
        return $this->name == 'Other';
    }

    public static function DropShipOrder()
    {
        return self::where('name', '=', 'BioKare One (Dropship)')->first();
    }

    public function MembershipGroup()
    {
        return $this->belongsTo(Group::class, 'membership_group_id');
    }

    public function bonusCategory()
    {
        return $this->belongsTo(BonusCategory::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public static function OtherPricingId()
    {
        assert(config('order.allow_other_product'));

        return self::where('name', 'Other')
            ->first()
            ->id;
    }

    public static function getAvailableProductList()
    {
        return self::where('products.is_available', true)
            ->with('bonusCategory')
            ->get();
    }

    public static function getAllProductList()
    {
        return self::with('bonusCategory')
            ->get();
    }

    // Model

    public static function DropshipMembershipForStockist()
    {
        assert(\Auth::user()->access()->stockist);

        // TODO: Deprecate
        return self::where('name', 'BioKare Membership Promo for GSK')
            ->orWhere('name', 'BioKare Membership for GSK')
            ->get();
    }

    public function setUnavailable($id)
    {
        $product = self::find($id);

        assert($product);

        $product->is_available = false;
        $product->save();
    }

    /**
     * @param $name
     * @param bool $strict
     *
     * @return Product
     */
    public static function findByName($name, $strict = true)
    {
        $item = self::where('name', '=', $name)->first();

        if (!$item && $strict) {
            \App::abort(503, 'product not found ' . $name);
        }

        return $item;
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
            if ($user->pricingArea() == 'east') {
                $price = $this->price_east;
                $delivery = $this->delivery_east;
            } else {
                $price = $this->price;
                $delivery = $this->delivery;
            }
        }
    }

    //Accessor
     public function getExpiryDateAttribute()
     {
         if (!$this->attributes['expiry_date']) {
             return;
         }

         $date = Carbon::createFromFormat('Y-m-d', $this->attributes['expiry_date']);

         return $date->format('d/m/Y');
     }

     //Mutator
     public function setExpiryDateAttribute($value)
     {
         $this->attributes['expiry_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $value)));
     }
}
