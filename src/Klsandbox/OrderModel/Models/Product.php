<?php

namespace Klsandbox\OrderModel\Models;

use App\Models\BonusCategory;
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
    protected $fillable = ['name', 'image', 'description', 'bonus_categories_id'];

    use \Klsandbox\SiteModel\SiteExtensions;

    protected $table = 'products';
    public $timestamps = true;

    // Relation
    public function productPricing()
    {
        if(! config('group.enabled')){
            return $this->hasOne(ProductPricing::class);
        }

        return $this->hasMany(ProductPricing::class);
    }

    public function isOtherProduct()
    {
        return $this->name == 'Other';
    }

    public static function RestockId()
    {
        return Product::forSite()->where('name', '=', 'Restock')->first()->id;
    }

    public function bonusCategory()
    {
        return $this->belongsTo(BonusCategory::class);
    }

    public static function OtherPricingId()
    {
        return self::forSite()
            ->where('name', 'Other')
            ->first()
            ->productPricing
            ->id;
    }

    public static function getList($user = null)
    {
        $q = self::forSite()
            ->where('products.is_available', true)
            ->with('productPricing')
            ->with('bonusCategory');

        $list = $q->get();

        if ($user)
        {
            $userGroups = $user->groups()->get()->pluck('id')->all();

            $list = $list->filter(function (Product $e) use ($userGroups) {
                if ($e->productPricing->groups->count() == 0)
                {
                    return true;
                }

                foreach ($e->productPricing->groups as $group)
                {
                    if (in_array($group->id, $userGroups))
                    {
                        return true;
                    }
                }

                return false;
            });
        }

        return $list;
    }

    // Model

    /**
     * create new product when group is disabled
     *
     * @param array $input
     */
    public function createProductGroupDisabled(array $input)
    {
        $product = new Product();

        $product->name = $input['name'];
        $product->description = $input['description'];
        $product->is_available = true;
        $product->hidden_from_ordering = false;
        $product->site_id = Site::id();
        $product->image = $input['image'];
        $product->bonus_categories_id = $input['bonus_categories_id'] ? $input['bonus_categories_id'] : null;
        $product->save();

        $product_price = new ProductPricing();
        $product_price->role_id = Role::Stockist()->id;
        $product_price->product_id = $product->id;
        $product_price->price = $input['price'];
        $product_price->site_id = Site::id();
        $product_price->save();
    }

    /**
     * create new product when group is disabled
     *
     * @param array $input
     */
    public function createProductGroupEnabled(array $input)
    {

        $product = new Product();

        $product->name = $input['name'];
        $product->description = $input['description'];
        $product->is_available = true;
        $product->hidden_from_ordering = false;
        $product->site_id = Site::id();
        $product->image = $input['image'];
        $product->bonus_categories_id = $input['bonus_categories_id'] ? $input['bonus_categories_id'] : null;
        $product->save();

        foreach($input['groups'] as $group){
            $product_price = new ProductPricing();
            $product_price->role_id = Role::Stockist()->id;
            $product_price->product_id = $product->id;
            $product_price->price = isset($group['price']) ? $group['price'] : 0;
            $product_price->site_id = Site::id();
            $product_price->save();
            $product_price->groups()->attach($group['group_id']);
        }
    }

    /**
     * update existing product when group is disabled
     *
     * @param Product $product
     * @param array $input
     */
    public function updateProductGroupDisabled(Product $product, array $input)
    {

        $product->name = $input['name'];
        $product->description = $input['description'];

        isset($input['image']) ? $product->image = $input['image'] : '';

        $product->bonus_categories_id = $input['bonus_categories_id'] ? $input['bonus_categories_id'] : null;
        $product->save();

        $product->productPricing()->update([
            'price' => $input['price']
        ]);
    }

    /**
     * update existing product when group is disabled
     *
     * @param array $input
     */
    public function updateProductGroupEnabled(Product $product, array $input)
    {
        $product->name = $input['name'];
        $product->description = $input['description'];
        isset($input['image']) ? $product->image = $input['image'] : '';
        $product->bonus_categories_id = $input['bonus_categories_id'] ? $input['bonus_categories_id'] : null;
        $product->save();

        foreach($input['groups'] as $group){
            $productPricing = ProductPricing::find($group['product_pricing_id']);
            $productPricing->update([
                'price' => isset($group['price']) ? $group['price'] : 0,
            ]);
        }
    }

    public function updateProduct($input)
    {

        $productPricing->price = $inputs['price'];
        $productPricing->save();

        if ($inputs['group_id'])
        {
            $productPricing->groups()->sync([$inputs['group_id']]);
        }
        else
        {
            $productPricing->groups()->sync([]);
        }

        $inputs = array_except($inputs, ['group_id', 'price', '_token']);

        $productPricing->product->update($inputs);

    }

    public function setUnavailable($id)
    {
        $product = Product::find($id);
        $product->is_available = false;
        $product->save();
    }
}
