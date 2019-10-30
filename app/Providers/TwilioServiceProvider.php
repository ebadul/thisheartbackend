<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Twilio\Rest\Client;


class TwilioServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    protected $accountSid;
    protected $authToken;
    protected $twilioNumber;

    public function boot()
    {
        $this->accountSid = env('TWILIO_SID');
        $this->authToken = env('TWILIO_TOKEN');
        $this->twilioNumber = env('TWILIO_NUMBER');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
       
       
        
        $this->app->bind('twilio', function() {
            // return response()->json([
            //     'acc id'=>$this->accountSid,
            //     'token'=>$this->authToken
            // ]);
            return new Client($this->accountSid, $this->authToken);
        });
    }
}
