<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class PaymentDetails extends Model
{
    public function savePaymentDetails($rs){
        $user = Auth::user();
        $payment_details = new PaymentDetails;
        $payment_details->user_id=$user->id;
        $payment_details->package_id=$user->user_package->last()->package_id;
        $payment_details->cost=$user->user_package->last()->package_info->price;
        $payment_details->card_type=$rs->card_type;
        $payment_details->card_number=$rs->card_number;
        $payment_details->card_expire=$rs->card_expire;
        $payment_details->card_security_code=$rs->card_security_code;
        if($payment_details->save()){
            return $payment_details;
        }else{
            return false;
        }
    }
}
