<?php

namespace Klsandbox\OrderModel\Models;

use Auth;
use Input;
use Request;
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
 */
class ProofOfTransfer extends Model
{

    protected $table = 'proof_of_transfers';
    public $timestamps = true;
    protected $fillable = ['bank_name', 'image', 'amount', 'user_id', 'receiver_user_id', 'notes'];

    public static function createFromInput()
    {

        $fileName = null;
        $newFileName = null;
        if (Input::file('image')) {
            $originalFileName = Input::file('image')->getClientOriginalName();

            $fileName = "upload_" . Auth::user()->id . mt_rand() . "." . pathinfo($originalFileName, PATHINFO_EXTENSION);

            $newFileName = Request::file('image')->move(public_path("img/user"), $fileName);
            Log::info("Move " . $newFileName);
        }

        if (!Auth::user()->referral_id) {
            App::abort(500, "Invalid user");
        }

        $proofOfTransfers = new ProofOfTransfer();
        $proofOfTransfers->bank_name = Input::get('bank_name');
        $proofOfTransfers->amount = Input::get('amount');
        $proofOfTransfers->user_id = Auth::user()->id;
        $proofOfTransfers->notes = Input::get('notes');
        $proofOfTransfers->receiver_user_id = Auth::user()->referral_id;

        if ($fileName) {
            $proofOfTransfers->image = "img/user/$fileName";
        }

        $proofOfTransfers->save();

        return $proofOfTransfers;
    }

}
