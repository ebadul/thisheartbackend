<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class PaymentSession extends Model
{
    //

    public function savePaymentSession($rs){
        $user = Auth::user();
        $rs = (object) $rs;
        $payment_session = new PaymentSession;
        $payment_session->user_id = $user->id;
        $payment_session->package_id = $rs->package_id;
        $payment_session->payment_session_id = $rs->payment_session_id;
        $payment_session->paid = $rs->paid;
        $payment_session->amount = $rs->amount/100;
        $payment_session->user_package_id = $rs->user_package_id;
        $payment_session->payment_details_id = $rs->payment_details_id;
        $payment_session->mode = $rs->session->mode;
        $payment_session->customer = $rs->session->customer;
        $payment_session->setup_intent = $rs->session->setup_intent;
        $payment_session->save();

       
    }
}
