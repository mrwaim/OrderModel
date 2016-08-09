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
        'expiry_date'
    ];

    protected $table = 'products';
    public $timestamps = true;

    // Relation
    public function productPricing()
    {
        if (!config('group.enabled')) {
            return $this->hasOne(ProductPricing::class);
        }

        return $this->hasMany(ProductPricing::class);
    }

    public function units()
    {
        return $this->belongsToMany(ProductUnit::class, 'products_product_units', 'product_id', 'product_unit_id')
            ->withPivot(['quantity', 'quantity_east', 'quantity_pickup']);
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

    public static function OtherPricingId()
    {
        assert(config('order.allow_other_product'));

        return self::where('name', 'Other')
            ->first()
            ->productPricing
            ->id;
    }

    public static function getAvailableProductList()
    {
        return self::where('products.is_available', true)
            ->with('productPricing')
            ->with('bonusCategory')
            ->get();
    }

    public static function getAllProductList()
    {
        return self::with('productPricing')
            ->with('bonusCategory')
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

    /**
     * create new product when group is disabled
     *
     * @param array $input
     */
    public function createProductGroupDisabled(array $input)
    {
        $product = new self();

        $product->name = $input['name'];
        $product->description = $input['description'];
        $product->is_available = true;
        $product->hidden_from_ordering = false;
        $product->image = $input['image'];
        $product->bonus_category_id = $input['bonus_categories_id'] ? $input['bonus_categories_id'] : null;
        $product->is_hq = $input['is_hq'];
        $product->for_customer = $input['for_customer'];
        $product->new_user = $input['new_user'];
        $product->hidden_from_ordering = $input['hidden_from_ordering'];
        $product->save();

        $product_price = new ProductPricing();
        $product_price->role_id = Role::Stockist()->id;
        $product_price->product_id = $product->id;
        $product_price->price = $input['price'];
        $product_price->save();

        return $product;
    }

    /**
     * create new product when group is enabled
     *
     * @param array $input
     * @return Product
     */
    public function createProductGroupEnabled(array $input)
    {
        $product = new self();

        $product->fill($input);
        $product->is_available = true;
        $product->save();

        foreach ($input['groups'] as $group) {
            if (isset($group['price']) && $group['price'] > 0) {
                $product_price = new ProductPricing();
                $product_price->role_id = Role::Stockist()->id;
                $product_price->product_id = $product->id;
                $product_price->price = $group['price'];
                $product_price->price_east = $group['price_east'];
                $product_price->delivery = $group['delivery'];
                $product_price->delivery_east = $group['delivery_east'];
                $product_price->group_id = $group['group_id'];
                $product_price->save();
            }
        }

        return $product;
    }

    /**
     * update existing product when group is disabled
     *
     * @param Product $product
     * @param array $input
     */
    public function updateProductGroupDisabled(Product $product, array $input)
    {
        $product->fill($input);
        $product->save();

        $product->productPricing()->update([
            'price' => $input['price'],
        ]);
    }

    /**
     * update existing product when group is disabled
     *
     * @param Product $product
     * @param array $input
     * @throws \Exception
     */
    public function updateProductGroupEnabled(Product $product, array $input)
    {
        $product->fill($input);
        $product->save();

        foreach ($input['groups'] as $group) {
            $productPricing = null;

            // if price is defined and not 0
            if ((isset($group['price']) && $group['price'] > 0) && (isset($group['price_east']) && $group['price_east'] > 0)) {
                //if not have product pricing then create one
                if (!$group['product_pricing_id']) {
                    $productPricing = new ProductPricing();
                    $productPricing->role_id = Role::Stockist()->id;
                    $productPricing->product_id = $product->id;
                    $productPricing->group_id = $group['group_id'];
                    $productPricing->save();
                } else {
                    $productPricing = ProductPricing::find($group['product_pricing_id']);
                }

                //update price
                $productPricing->update([
                    'price' => $group['price'] ? $group['price'] : null,
                    'price_east' => $group['price_east'] ? $group['price_east'] : null,
                    'delivery' => $group['delivery'] ? $group['delivery'] : null,
                    'delivery_east' => $group['delivery_east'] ? $group['delivery_east'] : null,
                ]);
            } else {
                // if price and price_east is not defined then delete it
                if ((!isset($group['price']) || !$group['price']) && (!isset($group['price_east']) || !$group['price_east'])) {
                    // if product pricing is existing in database, price is not defined or 0 then delete it
                    if (isset($group['product_pricing_id']) && $group['product_pricing_id'] > 0) {
                        $productPricing = ProductPricing::find($group['product_pricing_id']);

                        // check product pricing is exist
                        if ($productPricing) {
                            $productPricing->delete();
                        }
                    }
                }
            }
        }
    }

    public function setUnavailable($id)
    {
        $product = self::find($id);

        assert($product);

        $product->is_available = false;
        $product->save();
    }

    public function pricingForGroup($group)
    {
        return $this->productPricing()->where('group_id', '=', $group->id)->first();
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

    //Accessor
    public function getExpiryDateAttribute()
    {
        if (!$this->attributes['expiry_date'])
        {
            return null;
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
