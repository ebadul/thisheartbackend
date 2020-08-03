<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FreeAccountMail extends Mailable
{
    use Queueable, SerializesModels;
    private $user,$user_pkg, $activation_code, $loginurl;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->user = $data['user'];
        $this->user_pkg = $data['user_package'];
        $this->loginurl = $data['login_url'];
        $this->activation_code = $data['activation_code'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('info@thisheart.com','ThisHeart')->
        view('emails.free-account-mail',[
                'user'=>$this->user,
                'user_pkg'=>$this->user_pkg,
                'activation_code'=>$this->activation_code,
                'loginurl'=>$this->loginurl,
            ]);
    }
}
