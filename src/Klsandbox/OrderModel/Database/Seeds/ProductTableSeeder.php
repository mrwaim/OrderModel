<?php

namespace Klsandbox\OrderModel\Database\Seeds;

use App\Models\BonusCategory;
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
        $this->addProduct($siteId, 'Restock', 'Restock', BonusCategory::Basic()->id);
        $this->addProduct($siteId, 'Dropship Order', 'Dropship Order', null);
    }

    public function addProduct($siteId, $name, $description, $bonusCategoryId)
    {
        $match = Product::forSite()->where('name', '=', $name)->get();
        if (count($match) > 0) {
            return;
        }

        Product::create(array(
            'name' => $name,
            'description' => $description,
            'image' => null,
            'is_available' => true,
            'hidden_from_ordering' => false,
            'bonus_category_id' => $bonusCategoryId,
        ));
    }
}
