<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserBlockedMail extends Mailable
{
    use Queueable, SerializesModels;
    private $user, $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user=null, $data=null)
    {
        $this->user = $user;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('info@thisheart.com','ThisHeart')->
        view('emails.user-block',[
                'user'=>$this->user,
                'package_info'=>$this->data
            ]);
    }
}
