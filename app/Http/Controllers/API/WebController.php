<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Lcobucci\JWT\Parser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\NewsletterSubscription;
use Mail;


class WebController extends BaseController
{
    // protected $access_url = "http://45.35.50.179/";
    protected $access_url = "";
    public function __construct()
    {
        $this->access_url = Request()->headers->get('origin').'/';
    }

    public function newsletter_subscription($email){
        
       
        $news_letter = NewsletterSubscription::where('subscription_email',$email)->first();
        if(!empty($news_letter)){
            return response()->json([
                'status'=>'exists',
                'message' => 'This email is subscribed already',
                 
            ], 200);
        }else{
            $news_letter = new NewsletterSubscription;
            $news_letter->subscription_email = $email;
            $news_insert = $news_letter->save();
        }
       
        if($news_insert){
            return response()->json([
                'status'=>'success',
                'message' => 'News letter subscription is successfully!',
                'data'=>$email
            ], 200);
        }else{
            return response()->json([
                'status'=>'error',
                'message' => 'Sorry, news letter subscription. Try again',
                 
            ], 200);
        }
                
            
    }   
     
}