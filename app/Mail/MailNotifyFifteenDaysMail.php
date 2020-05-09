<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailNotifyFifteenDaysMail extends Mailable
{
    use Queueable, SerializesModels;
    private $user,$user_pkg;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$user_pkg)
    {
        $this->user = $user;
        $this->user_pkg = $user_pkg;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('info@thisheart.com','This Heart')->
        view('emails.mail-notify-fifteen-days-mail',[
                'user'=>$this->user,
                'user_pkg'=>$this->user_pkg
            ]);
    }
}
