<?php

namespace Klsandbox\OrderModel\Database\Seeds;


use Klsandbox\OrderModel\Services\OrderManager;
use Illuminate\Database\Seeder;
use App\Models\User;
use Klsandbox\RoleModel\Role;
use Klsandbox\OrderModel\Models\Order;
use Klsandbox\OrderModel\Models\ProofOfTransfer;
use Klsandbox\OrderModel\Models\ProductPricing;
use Klsandbox\SiteModel\Site;
use Klsandbox\RandomHelper\RandomHelper;

// This class will place random orders for stockist in the system
class RandomOrderSeeder extends Seeder {

    protected $orderManager;

    public function __construct(OrderManager $orderManager)
    {
        $this->orderManager = $orderManager;
    }

    public function run() {
        if (Order::all()->count() > 0) {
            return;
        }

        Site::setSite(Site::DevSite());

        $bonusClass = config('bonus.bonus_model');

        $bonusClass::created(function ($item) {
            if ($item->bonusType->bonusTypeBonusPayoutOptions->count() > 1) {
                $choice = RandomHelper::choose_from_collection($item->bonusType->bonusTypeBonusPayoutOptions);

                $item->bonus_payout_id = $choice->payout_id;
                $item->save();
            }
        });

        $months = 12;
        $orderCount = 20;
        $statusChoice = ['New', 'Payment_Uploaded', 'Payment_Uploaded', 'Approved', 'Approved', 'Approved', 'Approved', 'Rejected', 'Shipped', 'Shipped', 'Shipped', 'Shipped', 'Shipped', 'Shipped', 'Shipped', 'Shipped'];
        $statusMap = ['New' => 1, 'Payment_Uploaded' => 2, 'Approved' => 3, 'Rejected' => 5, 'Shipped' => 4];

        $admin = User::admin();
        $stockists = Role::Stockist()->users()->get();

        $stockistRole = Role::Stockist();
        $productPricings = ProductPricing::where('role_id', '=', $stockistRole->id)->get();

        if (!$productPricings) {
            App::abort(500, 'No Pricings');
        }

        //dd($productPricings);

        $dateList = RandomHelper::listOfRandomInOrderEventTimestamps($months * 24, $orderCount);

        for ($orderCtr = 0; $orderCtr < $orderCount; $orderCtr++) {
            $date = $dateList[$orderCtr];
            $filteredStockist = $stockists->filter(function ($e) use ($date) {
                return $e->created_at->lte(new \Carbon\Carbon($date));
            });

            //echo "$date - Count " . $stockists->count() . " - Count in timespan " . $filteredStockist->count() . PHP_EOL;

            $user = RandomHelper::choose_from_collection($filteredStockist);
            // echo($user->toJson() . PHP_EOL);

            $status = RandomHelper::choose_from_array($statusChoice);

            //echo "Status $status\n";
            $status_id = $statusMap[$status];

            $tracking_id = null;
            if ($status == 'Shipped') {
                $tracking_id = $this->getTrackingId($orderCtr);
                //echo "Tracking ID $tracking_id\n";
            }

            $productPricing = RandomHelper::choose_from_collection($productPricings);
            if (!$productPricing) {
                App::abort(500, 'No Pricing');
            }

            if (!$productPricing->price) {
                App::abort(500, 'No Pricing');
            }

            $proofOfTransferId = null;
            if ($status != 'New') {
                $proofOfTransferId = $this->newProofOfTransfer($orderCtr, $productPricing->price, $user->id, $admin->id)->id;
            }

            //echo "proofOfTransferId $proofOfTransferId\n";

            $paymentMode = RandomHelper::choose_from_pair('COD', 'BankTransfer');

            //echo "SORTED DATE" . $date . PHP_EOL;

            Auth::setUser($user);
            $order = Order::create([
                        'created_at' => $date,
                        'updated_at' => $date,
                        'tracking_id' => $tracking_id,
                        'order_status_id' => $statusMap['New'],
                        'product_pricing_id' => $productPricing->id,
                        'proof_of_transfer_id' => $proofOfTransferId,
                        'payment_mode' => $paymentMode,
                        'price' => $productPricing->price,
            ]);

            $this->command->comment("Order $order->id created.");

            Auth::setUser(User::admin());
            $this->orderManager->approveOrder($order, $order->created_at->addDays(1));
        }

        $this->command->comment("Done");
    }

    private function newProofOfTransfer($ctr, $amount, $user_id, $receiver_user_id) {
        $proofOfTransfer = ProofOfTransfer::create([
                    'bank_name' => RandomHelper::getRandomBank(),
                    'image' => 'receipt/receipt_' . $amount . ".png",
                    'amount' => $amount,
                    'user_id' => $user_id,
                    'receiver_user_id' => $receiver_user_id,
        ]);

        return $proofOfTransfer;
    }

    private function getTrackingId($seed) {
        return md5($seed);
    }

}
