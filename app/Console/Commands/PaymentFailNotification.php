<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\PaymentChargingFailMail;
use App\UserPackage;
use App\BillingDetail;
use App\User;
use Mail;

class PaymentFailNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment_fail:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Payment failed notification description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Payment process is starting...");
     
        \Stripe\Stripe::setApiKey('sk_test_AVSEgKpyoxellFyvMtrGrIww00nRnsyvaP');
        $billing_details = BillingDetail::where('paid_status','=',0)->
                                          get();
   
        foreach( $billing_details as $billing){
            $this->info("...");
            var_dump( $billing->user_id);
            $user = User::where('id','=',$billing->user_id)->first();
            Mail::to($user->email)->send(new PaymentChargingFailMail($user, $billing));
        }
        $this->info("Payment process is end");
    }
}
