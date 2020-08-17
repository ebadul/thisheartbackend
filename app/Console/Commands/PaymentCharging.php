<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\BillingDetail;
use App\PaymentSession;
use App\UserPackage;
use App\CronPaymentCharging;
use App\Mail\PaymentChargingPaidMail;
use App\Mail\PaymentChargingFailMail;
use Stripe\Stripe;
use Auth;
use Mail;


class PaymentCharging extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:charging';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $user_package = new UserPackage; 
     
        \Stripe\Stripe::setApiKey('sk_test_AVSEgKpyoxellFyvMtrGrIww00nRnsyvaP');
        $billing_details = BillingDetail::where('paid_status','=',0)->get();
        foreach( $billing_details as $billing){
            $this->info("...");
            $payment_charging = $user_package->payment_charging_process($billing);
            if($payment_charging['status']==="success"){
                $this->info("Payment->success");
            }else{
                $this->info("Payment->".$payment_charging['message']);
            }
        }

        $this->info("Payment process is end");
    }
}
