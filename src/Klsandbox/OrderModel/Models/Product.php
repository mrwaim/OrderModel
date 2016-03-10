<?php

namespace Klsandbox\OrderModel\Models;

use Illuminate\Database\Eloquent\Model;
use Klsandbox\RoleModel\Role;
use Klsandbox\SiteModel\Site;

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
 */
class Product extends Model
{

    use \Klsandbox\SiteModel\SiteExtensions;

    protected $table = 'products';
    public $timestamps = true;

    public static function RestockId()
    {
        return Product::forSite()->where('name', '=', 'Restock')->first()->id;
    }

    public static function OtherPricingId()
    {
        return self::where('name', 'Other')
            ->select('product_pricings.id as product_pricing_id')
            ->leftJoin('product_pricings', 'products.id', '=', 'product_pricings.product_id')
            ->first()->product_pricing_id;
    }

    public static function getList()
    {
        $q = self::where('products.site_id', '=', Site::id())
            ->where('products.is_available', true);

        $q = $q->select(
            'products.id',
            'products.name',
            'products.description',
            'product_pricings.price',
            'product_pricings.id as product_pricing_id'
        )
            ->leftJoin('product_pricings', 'products.id', '=', 'product_pricings.product_id');

        return $q->get();
    }

    public function createNew($input)
    {
        $product = new Product();

        $product->name = $input['name'];
        $product->description = $input['description'];
        $product->is_available = true;
        $product->hidden_from_ordering = false;
        $product->site_id = Site::id();
        $product->save();

        $product_price = new ProductPricing();

        $product_price->role_id = Role::Stockist()->id;
        $product_price->product_id = $product->id;
        $product_price->price = $input['price'];
        $product_price->site_id = Site::id();
        $product_price->save();
    }

    public function setUnavailable($id)
    {
        $product = Product::find($id);
        $product->is_available = false;
        $product->save();
    }
}
