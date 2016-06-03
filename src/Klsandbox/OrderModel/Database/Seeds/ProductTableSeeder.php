<?php

namespace Klsandbox\OrderModel\Database\Seeds;

use App\Models\BonusCategory;
use App\Models\Group;
use Illuminate\Database\Seeder;
use Klsandbox\OrderModel\Models\Product;
use Klsandbox\SiteModel\Site;

class ProductTableSeeder extends Seeder
{
    public function run()
    {
        foreach (Site::all() as $site) {
            Site::setSite($site);
            $this->runForSite($site->id);
        }
    }

    public function runForSite($siteId)
    {
        $this->addProduct($siteId, 'Restock', 'Restock', BonusCategory::gStar()->id, false, false, null, true);
        $stockistGroup = Group::StockistGStarGroup();
        assert($stockistGroup, '$stockistGroup');
        $this->addProduct($siteId, 'Stockist Membership', 'Stockist Membership', BonusCategory::gStar()->id, true, false, $stockistGroup, true);
        $this->addProduct($siteId, 'Dropship Order', 'Dropship Order', BonusCategory::bioKare()->id, false, true, null, false);
    }

    public function addProduct($siteId, $name, $description, $bonusCategoryId, $newUser, $forCustomer, $membershipGroup, $isHq)
    {
        $match = Product::forSite()->where('name', '=', $name)->get();
        if (count($match) > 0) {
            return;
        }

        return Product::create(array(
            'name' => $name,
            'description' => $description,
            'image' => null,
            'is_available' => true,
            'hidden_from_ordering' => false,
            'bonus_category_id' => $bonusCategoryId,
            'new_user' => $newUser,
            'for_customer' => $forCustomer,
            'is_membership' => (bool) $membershipGroup,
            'membership_group_id' => $membershipGroup ? $membershipGroup->id : null,
            'is_hq' => $isHq,
        ));
    }
}
