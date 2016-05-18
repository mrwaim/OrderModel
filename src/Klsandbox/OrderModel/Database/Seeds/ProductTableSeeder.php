<?php

namespace Klsandbox\OrderModel\Database\Seeds;


use App\Models\BonusCategory;
use Illuminate\Database\Seeder;
use Klsandbox\RoleModel\Role;
use Klsandbox\OrderModel\Models\Product;
use Klsandbox\OrderModel\Models\ProductPricing;
use Klsandbox\SiteModel\Site;

class ProductTableSeeder extends Seeder {

    public function run() {
        foreach (Site::all() as $site) {
            Site::setSite($site);
            $this->runForSite($site->id);
        }
    }
    
    public function runForSite($siteId) {
        $match = Product::forSite()->where('name', '=', 'Restock')->get();
        if (count($match) > 0)
        {
            return;
        }

        Product::create(array(
            'name' => 'Restock',
            'description' => 'Restock',
            'image' => null,
            'is_available' => true,
            'hidden_from_ordering' => false,
            'bonus_category_id' => BonusCategory::Basic()->id,
        ));

    }

}
