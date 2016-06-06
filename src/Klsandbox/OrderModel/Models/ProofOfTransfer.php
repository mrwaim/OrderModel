<?php

namespace Klsandbox\OrderModel\Models;

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
 */
class ProofOfTransfer extends Model
{
    protected $table = 'proof_of_transfers';
    public $timestamps = true;
    protected $fillable = ['bank_name', 'image', 'amount', 'user_id', 'receiver_user_id', 'notes', 'payment_mode'];

    public static function createFromInput(App\Models\User $user, App\Http\Requests\OrderPostRequest $request)
    {
        $fileName = null;
        $newFileName = null;
        if ($request->file('image')) {
            $originalFileName = $request->file('image')->getClientOriginalName();

            $fileName = 'upload_' . $user->id . mt_rand() . '.' . pathinfo($originalFileName, PATHINFO_EXTENSION);

            $newFileName = $request->file('image')->move(public_path('img/user'), $fileName);
            Log::info('Move ' . $newFileName);
        }

        return self::proofOfTransferFromRequestWithoutImages($user, $request, "img/user/$fileName", $request->totalAmount(), $request->isHq());
    }

    /**
     * @param App\Http\Requests\OrderPostRequest $request
     * @param $fileName
     *
     * @return ProofOfTransfer
     */
    public static function proofOfTransferFromRequestWithoutImages(App\Models\User $user, $request, $fileName, $amount, $isHq)
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

        assert($amount > 0);

        $proofOfTransfers->amount = $amount;

        $proofOfTransfers->user_id = $user->id;
        $proofOfTransfers->notes = $request->notes;
        $proofOfTransfers->order_notes = $request->order_notes;

        if ($isHq) {
            $proofOfTransfers->receiver_user_id = App\Models\User::admin()->id;
        } else {
            assert($user->organization);
            $proofOfTransfers->receiver_user_id = $user->organization->admin->id;
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
}
