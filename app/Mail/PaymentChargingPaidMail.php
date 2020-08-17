<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentChargingPaidMail extends Mailable
{
    use Queueable, SerializesModels;
    private $user,$payment_session;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$billing)
    {
        $this->user = $user;
        $this->billing = $billing;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('info@thisheart.com','ThisHeart')->
        view('emails.payment-charging-paid',[
                'user'=>$this->user,
                'billing'=>$this->billing
            ]);
    }
}
