<?php

namespace Klsandbox\OrderModel\Models;

use App\Http\Requests\OrderPostRequest;
use App\Models\User;
use App\Services\ProductManager\ProductManagerInterface;
use Klsandbox\BillplzRoute\Models\BillplzResponse;
use Log;
use Illuminate\Database\Eloquent\Model;
use App;

/**
 * Klsandbox\OrderModel\Models\ProofOfTransfer
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $bank_name
 * @property string $notes
 * @property string $image
 * @property float $amount
 * @property integer $user_id
 * @property integer $receiver_user_id
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProofOfTransfer whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProofOfTransfer whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProofOfTransfer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProofOfTransfer whereBankName($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProofOfTransfer whereNotes($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProofOfTransfer whereImage($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProofOfTransfer whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProofOfTransfer whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProofOfTransfer whereReceiverUserId($value)
 * @property integer $site_id
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProofOfTransfer whereSiteId($value)
 * @property string $order_notes
 * @property string $payment_mode
 * @property-read \Illuminate\Database\Eloquent\Collection|\Klsandbox\BillplzRoute\Models\BillplzResponse[] $billplzResponses
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProofOfTransfer whereOrderNotes($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProofOfTransfer wherePaymentMode($value)
 * @mixin \Eloquent
 * @property-read User $receiver
 * @property string $date_transfer
 * @property string $time_transfer
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProofOfTransfer whereDateTransfer($value)
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProofOfTransfer whereTimeTransfer($value)
 * @property boolean $is_public_order
 * @method static \Illuminate\Database\Query\Builder|\Klsandbox\OrderModel\Models\ProofOfTransfer whereIsPublicOrder($value)
 */
class ProofOfTransfer extends Model
{
    protected $table = 'proof_of_transfers';
    public $timestamps = true;
    protected $fillable = ['bank_name', 'image', 'amount', 'user_id', 'receiver_user_id', 'notes', 'payment_mode', 'date_transfer', 'time_transfer'];

    public static function createFromInput(User $user, OrderPostRequest $request, ProductManagerInterface $productManager, $customer = null)
    {
        $fileName = null;
        $newFileName = null;
        if ($request->file('image')) {
            $originalFileName = $request->file('image')->getClientOriginalName();

            $fileName = 'upload_' . $user->id . mt_rand() . '.' . pathinfo($originalFileName, PATHINFO_EXTENSION);

            $newFileName = $request->file('image')->move(public_path('img/user'), $fileName);
            Log::info('Move ' . $newFileName);
        }

        $hasOrganizationMembership = $productManager->hasOrganizationMembership($request->getProductPricing());

        return self::proofOfTransferFromRequestWithoutImages($user, $request, "img/user/$fileName", $request->totalAmount($customer), $request->isHq(), $hasOrganizationMembership);
    }

    /**
     * @param OrderPostRequest $request
     * @param $fileName
     *
     * @return ProofOfTransfer
     */
    public static function proofOfTransferFromRequestWithoutImages(User $user, $request, $fileName, $amount, $isHq, $isMembership)
    {
        if (!$user->isAdmin() && !$user->referral_id && !$user->new_referral_id) {
            App::abort(500, 'Invalid user');
        }

        $proofOfTransfers = new self();

        $proofOfTransfers->payment_mode = $request->payment_mode;

        if ($proofOfTransfers->payment_mode == 'BankTransfer') {
            $proofOfTransfers->bank_name = $request->bank_name;
        } else {
            $proofOfTransfers->bank_name = $proofOfTransfers->payment_mode;
        }

        // date transfer
        if ($request->date_transfer) {
            $proofOfTransfers->date_transfer = $request->date_transfer;
        }

        if ($request->time_transfer) {
            $proofOfTransfers->time_transfer = $request->time_transfer;
        }

        if ($request->is_public_order) {
           $proofOfTransfers->is_public_order = $request->is_public_order;
        }

        assert($amount > 0);

        $proofOfTransfers->amount = $amount;

        $proofOfTransfers->user_id = $user->id;
        $proofOfTransfers->notes = $request->notes;
        $proofOfTransfers->order_notes = $request->order_notes;

        if ($isHq) {
            $proofOfTransfers->receiver_user_id = User::admin()->id;
        } else {
            assert($user->organization || $isMembership);
            if ($isMembership && !$user->organization) {
                // HACKHACK This will set receiver_user_id to admin - in RaniaDropshipMembershipOrderManager
                // We will undo this, and reset receiver_user_id to PL
                // Unit test this!

                $proofOfTransfers->receiver_user_id = User::admin()->id;
            } else {
                assert($user->organization);
                $proofOfTransfers->receiver_user_id = $user->organization->admin_id;
            }
        }

        if ($fileName) {
            $proofOfTransfers->image = $fileName;
        }

        $proofOfTransfers->save();

        return $proofOfTransfers;
    }

    public function billplzResponses()
    {
        return $this->hasMany(BillplzResponse::class, 'metadata_proof_of_transfer_id');
    }

    public function order()
    {
        return $this->hasOne(config('order.order_model'));
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_user_id');
    }

    //Mutator
    public function setDateTransferAttribute($value)
    {
        $this->attributes['date_transfer'] = date('Y-m-d', strtotime(str_replace('/', '-', $value)));
    }
}
