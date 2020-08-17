<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentChargingFailMail extends Mailable
{
    use Queueable, SerializesModels;
    private $user,$payment_session;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$payment_session)
    {
        $this->user = $user;
        $this->payment_session = $payment_session;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('info@thisheart.com','ThisHeart')->
        view('emails.payment-charging-fail',[
                'user'=>$this->user,
                'payment_session'=>$this->payment_session
            ]);
    }
}
