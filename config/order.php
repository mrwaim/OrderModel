<?php

return [
    'restock_price_east' => 2000,
    'restock_price_west' => 1000,
    'dropship_price_east' => 200,
    'dropship_price_west' => 100,
    'membership_dropship_price_east' => env('MEMBERSHIP_DROPSHIP_PRICE', 500),
    'membership_dropship_price_west' => env('MEMBERSHIP_DROPSHIP_PRICE', 600),
    'restock_price' => env('RESTOCK_PRICE'),
    'order_model' => 'App\Models\Order',
    'show_products' => true,
    'allow_multiple_products' => true,
    'allow_quantity' => true,
    'allow_other_product' => false,
];
