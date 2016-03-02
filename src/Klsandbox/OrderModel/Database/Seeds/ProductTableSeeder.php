<?php

namespace Klsandbox\OrderModel\Database\Seeds;


use Illuminate\Database\Seeder;
use Klsandbox\RoleModel\Role;
use Klsandbox\OrderModel\Models\Product;
use Klsandbox\OrderModel\Models\ProductPricing;
use Klsandbox\SiteModel\Site;

class ProductTableSeeder extends Seeder {

    public function run() {
        if (Product::all()->count() > 0) {
            return;
        }

        foreach (Site::all() as $site) {
            Site::setSite($site);
            $this->runForSite($site->id);
        }
    }
    
    public function runForSite($siteId) {
        $product1 = Product::create(array(
                    'name' => 'Restock',
                    'description' => 'Restock',
                    'image' => null,
                    'is_available' => true,
                    'hidden_from_ordering' => false,
        ));

        $productPricing1Stockist = ProductPricing::create([
                    'role_id' => Role::Stockist()->id,
                    'product_id' => $product1->id,
                    'price' => config('order.restock_price'),
        ]);
    }

}
