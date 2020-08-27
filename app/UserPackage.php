<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Console\Command;
use App\PackageInfo;
use App\PaymentSession;
use App\User;
use App\Beneficiary;
use App\Account;
use App\Letters;
use App\FreeAccount;
use Stripe\Stripe;
use Mail;
use App\Mail\PaymentSuccessMail;
use App\Mail\PaymentChargingMail;
use App\Mail\UnsubscribePackageMail;
use App\Mail\PaymentChargingPaidMail;
use App\Mail\PaymentChargingFailMail;
use Auth;
use File;
use Crypt;


class UserPackage extends Model
{
    //
    protected $access_url = "";
    public function __construct(){
        Stripe::setApiKey(env("STRIPE_API_KEY"));
        $this->access_url = Request()->headers->get('origin').'/';
    }

    protected $fillable = ['id','user_id','package_id','subscription_date','subscription_expire_date','subscription_status'];
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function package_info(){
        return $this->belongsTo(PackageInfo::class,'package_id','id');
    }
    public function free_account(){
        return $this->hasOne(FreeAccount::class,'user_id','user_id');
    }

    public function package_entities(){
        return $this->hasMany(PackageEntity::class,'package_id','package_id')->with('entity_info');
    }

    
    public function checkPkgEntityActionStop($action_type, $file_size = 0 ){
        $user = Auth::user();
        $user_id = $user->id;
        switch($action_type){
            case "images":
                $user_package = $user->user_package;
                $user_package_entities = $user_package->package_entities;
                $entity_info = PackageEntitiesInfo::where('package_entity_title','=','Images')->first();
                $user_entity_details = $user_package_entities->where('package_entities_id','=',$entity_info->id)->first();
                $entity_value = $user_entity_details->entity_value;
                $memories = new Memories;
                $imageCount = $memories->imageCount();
                
                if($imageCount>=$entity_value){
                    return true;
                }else{
                    return false;
                }
                break;
            case "videos":
                $user_package = $user->user_package;
                $user_package_entities = $user_package->package_entities;
                $entity_info = PackageEntitiesInfo::where('package_entity_title','=','Videos')->first();
                $user_entity_details = $user_package_entities->where('package_entities_id','=',$entity_info->id)->first();
                $entity_value = $user_entity_details->entity_value;
                $memories = new Memories;
                $imageCount = $memories->imageCount();
                
                if($imageCount>=$entity_value){
                    return true;
                }else{
                    return false;
                }
                break;
            case "records":
                $user_package = $user->user_package;
                $user_package_entities = $user_package->package_entities;
                $entity_info = PackageEntitiesInfo::where('package_entity_title','=','Audio Recording')->first();
                $user_entity_details = $user_package_entities->where('package_entities_id','=',$entity_info->id)->first();
                $entity_value = $user_entity_details->entity_value;
                $memories = new Memories;
                $imageCount = $memories->imageCount();
                
                if($imageCount>=$entity_value){
                    return true;
                }else{
                    return false;
                }
                break;
            case "storages":
                try{
                        $user_package = $user->user_package;
                        $user_package_entities = $user_package->package_entities;
                        $entity_info = PackageEntitiesInfo::where('package_entity_title','=','File Storage')->first();
                        $user_entity_details = $user_package_entities->where('package_entities_id','=',$entity_info->id)->first();
                        $entity_value = $user_entity_details->entity_value;
                        if($entity_value==="-1"){
                            return false;
                        }
                        $user_path = public_path('uploads/'.$user->id);
                        if (File::exists($user_path)) {
                            $user_storage_size = File::size($user_path);
                            $getAllDirs = File::directories($user_path);
                            $fileSize =[];
                            $totalFileSizeGB=$file_size;
                            foreach( $getAllDirs as $dir ) {
                                $dirNames[] = basename($dir);
                                $fileList=File::files($user_path.'/'.basename($dir));
                                foreach($fileList as $fileTmp){
                                    $fileSize[] = ($fileTmp->getSize()/1024/1024);
                                    $totalFileSizeGB+=(($fileTmp->getSize()/1024)/1024);
                                }
                            }

                            if($totalFileSizeGB>=($entity_value*1024)){
                                return true;
                            }else{
                                return false;
                            }
                        }
                    }catch(Exception $ex){
                        return new Exception($ex->getMessage());
                    }
                break;
            case "beneficiaries":
                try{
                        $user_package = $user->user_package;
                        $user_package_entities = $user_package->package_entities;
                        $entity_info = PackageEntitiesInfo::where('package_entity_title','=','Beneficiaries Saved')->first();
                        $user_entity_details = $user_package_entities->where('package_entities_id','=',$entity_info->id)->first();
                        $entity_value = $user_entity_details->entity_value;
                        $beneficiaries_count = Beneficiary::where('user_id',$user->id)->count();

                        if($entity_value==="-1"){
                            return false;
                        }

                        if($beneficiaries_count>=$entity_value){
                            return true; //generate error 
                        }else{
                            return false;
                        }

                    }catch(Exception $ex){
                        return new Exception($ex->getMessage());
                    }
                break;
            case "accounts":
                try{
                        $user_package = $user->user_package;
                        $user_package_entities = $user_package->package_entities;
                        $entity_info = PackageEntitiesInfo::where('package_entity_title','=','Accounts Saved')->first();
                        $user_entity_details = $user_package_entities->where('package_entities_id','=',$entity_info->id)->first();
                        $entity_value = $user_entity_details->entity_value;
                        $account_count = Account::where('user_id',$user->id)->count();

                        if($entity_value==="-1"){
                            return false;
                        }

                        if($account_count>=$entity_value){
                            return true; //generate error 
                        }else{
                            return false;
                        }

                    }catch(Exception $ex){
                        return new Exception($ex->getMessage());
                    }
                break;
            case "letters":
                try{
                        $user_package = $user->user_package;
                        $user_package_entities = $user_package->package_entities;
                        $entity_info = PackageEntitiesInfo::where('package_entity_title','=','Encrypted Letters/Messages')->first();
                        $user_entity_details = $user_package_entities->where('package_entities_id','=',$entity_info->id)->first();
                        $entity_value = $user_entity_details->entity_value;
                        $letters_count = Letters::where('user_id',$user->id)->count();

                        if($entity_value==="-1"){
                            return false;
                        }

                        if($letters_count>=$entity_value){
                            return true; //generate error 
                        }else{
                            return false;
                        }

                    }catch(Exception $ex){
                        return new Exception($ex->getMessage());
                    }
                break;
            default:
                return response()->json([
                    'status'=>'success',
                    'data'=>'yes'
                ]);
        }
    }

    public function saveUserPackage($rs){
        $user_id = $rs['user_id'];
        $package_id =  $rs['package_id'];
        $paid =  empty($rs['paid'])?'unpaid':$rs['paid'];
        $billing_type =  empty($rs['billing_type'])?"":$rs['billing_type'];
        $payment_type =  empty($rs['payment_type'])?"":$rs['payment_type'];
       
        $date = date('Y-m-d');
        $package_info = PackageInfo::where('id','=',$package_id)->orderBy('id','desc')->first();
        if($paid==="paid"){
            if(!empty($rs['trial_end']) && $rs['trial_end'] === "yes"){
                $expire_date = $date;
            }else{
                if($payment_type==="yearly"){
                    $expire_date = date('Y-m-d', strtotime($date.' + '.$package_info->year_days.' days'));
                }else{
                    $expire_date = date('Y-m-d', strtotime($date.' + '.$package_info->days.' days'));
                }
            }
        }elseif($paid==="unpaid"){
            if(!empty($rs['trial_end']) && $rs['trial_end'] === "yes"){
                $expire_date = $date;
            }else{
                $expire_days = 30;
                $expire_date = date('Y-m-d', strtotime($date.' + '.$expire_days.' days'));
            }
        }

        $user_pkg = UserPackage::where('user_id','=',$user_id)->first();
        if(empty($user_pkg)){
            $user_pkg = new UserPackage;
        }  
        $user_pkg->user_id = $user_id;
        $user_pkg->package_id = $package_id;
        $user_pkg->subscription_date = $date;
        $user_pkg->subscription_expire_date = !empty($rs['trial_end'])?$user_pkg->subscription_expire_date:$expire_date;
        $user_pkg->subscription_status = 1;
        if($user_pkg->save()){
            return $user_pkg;
        }else{
            return false;
        }

    }

    public function paymentCreateSession($rs){
            try{
                $user = Auth::user();
                \Stripe\Stripe::setApiKey(env("STRIPE_API_KEY"));
                $success_url = $this->access_url.'payment-success/'.Crypt::encryptString('payment-success').'?session_id={CHECKOUT_SESSION_ID}';
                $cancel_url = $this->access_url."payment-cancel/".Crypt::encryptString('payment-cancel');
                $billing_type = $rs->billing_type;
                $payment_type = $rs->payment_type;
                $stripe_month_price_plan = $rs->stripe_month_price_plan;
                $stripe_year_price_plan = $rs->stripe_year_price_plan;
                $price = $rs->payment_type==="yearly"?$rs->year_amount:$rs->amount;
                $price_plan = "";
                $line_items= "";
                $mode = "setup";
                if($billing_type && $payment_type==="yearly"){
                    $price_plan = $stripe_year_price_plan;
                }elseif($billing_type && $payment_type==="monthly"){
                    $price_plan = $stripe_month_price_plan;
                }
                $customer = \Stripe\Customer::create();
                
                    $line_items=[ 
                        'name' => $rs->item,
                        'description' => $rs->description,
                        'images' => ['http://thisheart.co:8000/images/package-img.png'],
                        'amount' => $price*100,
                        'currency' => 'usd',
                        'quantity' => 1,
                    ];
                    $mode = "setup";
                    $session_create = [
                        'payment_method_types' => ['card'],
                        'customer'=>$customer->id,
                        'metadata'=>[
                            'user_id' => $user->id,
                            'package_id' => $rs->item_id,
                            'package_name' => $rs->item,
                            'amount' => $price*100,
                            'payment_type' => $payment_type,
                            'billing_type' => $billing_type,
                        ],
                        'mode'=>$mode,
                        'success_url' => $success_url,
                        'cancel_url' =>  $cancel_url,
                    ];
               

                
                $session = \Stripe\Checkout\Session::create($session_create);
            }catch(Exception $ex){
                $exp = $ex->getMessage();
            }
        if(!empty($session)){
            $session_rs = [
                'package_id'=>$rs->item_id,
                'payment_session_id' => $session->id,
                'paid' => 0,
                'amount' => $price*100,
                'user_package_id' => '0',
                'payment_details_id' => '0',
                'session' => $session,
            ];
            $payment_session = new PaymentSession;
            $payment_session->savePaymentSession($session_rs);

            return [
                'status'=>'success',
                'data'=>$session,
            ];
        }else{
            return [
                'status'=>'error',
                'data'=>$exp
            ];
        }
    }


    public function paymentSessionSuccess($rs){
        $session_token = $rs->session_token;
        $token = explode('&*^',$session_token);
        if(empty($token[0]) || empty($token[1])){
            return  [
                'status'=>'error',
                'code'=>'101',
                'message'=> 'Invalid payment request!',
            ];
        }

        $session_id = $rs->id;
        $user = Auth::user();

       

        $payment_session = PaymentSession::where('user_id','=',$user->id)
                            ->where('payment_session_id','=',$session_id)->first();
        if(empty($payment_session ) || $payment_session->paid===1 || 
                $payment_session->validated===1){
            return [
                'status'=>'error',
                'code'=>'102',
                'message'=> 'Invalid payment requests!',
            ];
        } 
       
        $user_package = new UserPackage;
        $session_status = $user_package->retriveSessionInfo($session_id);
        if(empty($session_status )){
            return [
                'status'=>'error',
                'code'=>'104',
                'message'=> 'Invalid payment requests!',
            ];
        } 

        // if($rs['session_type']==='expired'){
        //     $session_type = "expired";
            $next_billing = $this->make_next_billing($session_id,$rs['session_type']);
        // }
        
        $session_status->date = date('Y-m-d');
        $meta_data = $session_status->metadata;
        $user_id = $meta_data->user_id;
        $package_id = $meta_data->package_id;
        $amount = $meta_data->amount;
        $payment_type = $meta_data->payment_type;
        $billing_type = $meta_data->billing_type;
        $paid = "";
        if(!empty($session_status->subscription) && 
                $session_status->amount_total>0 && 
                $session_status->mode==="subscription" && $billing_type ==="yes"){
                    $paid = "unpaid";
        }else{
            $payment_status = $user_package->retrivePaymentInfo($session_status->payment_intent);
            if(!empty( $payment_status) && $payment_status->amount_received>0 && 
                $payment_status->status==="succeeded" &&
                $payment_status->charges->data[0]->amount_refunded === 0
                ){
                    $paid = "paid";
            }else{
                $paid = "unpaid";
            }
        }
       
        $payment_session->validated = 1;
        if($paid==="paid"){
            $payment_session->paid = 1;
            $payment_session->save();
        }elseif($paid==="unpaid"){
            $payment_session->paid = 0;
            $payment_session->save();
        }
        $payment_charging = 'NA';
        $billing_details = BillingDetail::where('stripe_session_id','like',$session_id)->first();
        if($rs['session_type']==='expired'){
            $payment_charging = $this->payment_charging(null,null, $billing_details->id);
        }elseif($rs['session_type']==='profile'){
            $payment_charging = null;
              $package_rs = [
                'user_id'=>$user->id,
                'package_id'=>$package_id,
                'payment_type'=>$payment_type,
                'billing_type'=>$billing_type,
                'paid'=>$paid
            ];
            $user_pkg = $user_package->saveUserPackage($package_rs);

        }
      
        Mail::to($user->email)->send(new PaymentSuccessMail($user, $session_status));
        return [
            'status'=>($payment_charging['status']==="fail")?"fail":'success',
            'data'=> $payment_session,
            'session_status'=> $session_status,
            'session_type'=> $rs['session_type'],
            'payment_status'=> $payment_status,
            //'package_info'=> null,
            //'user_id'=>$user->id,
            //'package_id'=>$package_id,
            //'payment_type'=>$payment_type,
            //'billing_type'=>$billing_type,
            'payment_charging'=>$payment_charging,
        ];
    }




    public function paymentSessionSuccessPayment($rs){
        $session_token = $rs->session_token;
        $token = explode('&*^',$session_token);
        if(empty($token[0]) || empty($token[1])){
            return  [
                'status'=>'error',
                'code'=>'101',
                'message'=> 'Invalid payment request!',
            ];
        }

        $session_id = $rs->id;
        $user = Auth::user();

        $payment_session = PaymentSession::where('user_id','=',$user->id)
                            ->where('payment_session_id','=',$session_id)->first();
        if(empty($payment_session ) || $payment_session->paid===1 || 
                $payment_session->validated===1){
            return [
                'status'=>'error',
                'code'=>'102',
                'message'=> 'Invalid payment requests!',
            ];
        } 

        $user_package = new UserPackage;
        $session_status = $user_package->retriveSessionInfo($session_id);
        if(empty($session_status )){
            return [
                'status'=>'error',
                'code'=>'104',
                'message'=> 'Invalid payment requests!',
            ];
        } 

       
        $next_billing = $this->make_next_billing($session_id,$rs['session_type']);
     
        
        $session_status->date = date('Y-m-d');
        $meta_data = $session_status->metadata;
        $user_id = $meta_data->user_id;
        $package_id = $meta_data->package_id;
        $amount = $meta_data->amount;
        $payment_type = $meta_data->payment_type;
        $billing_type = $meta_data->billing_type;
        $billing_details_id = $meta_data->billing_details_id;
        $paid = "";
        if(!empty($session_status->subscription) && 
                $session_status->amount_total>0 && 
                $session_status->mode==="subscription" && $billing_type ==="yes"){
                    $paid = "unpaid";
        }else{
            $payment_status = $user_package->retrivePaymentInfo($session_status->payment_intent);
            if(!empty( $payment_status) && $payment_status->amount_received>0 && 
                $payment_status->status==="succeeded" &&
                $payment_status->charges->data[0]->amount_refunded === 0
                ){
                    $paid = "paid";
            }else{
                $paid = "unpaid";
            }
        }
    
        $payment_session->validated = 1;
        if($paid==="paid"){
            $payment_session->paid = 1;
            $payment_session->save();
        }elseif($paid==="unpaid"){
            $payment_session->paid = 0;
            $payment_session->save();
        }
        $payment_charging = 'NA';
        if($rs['session_type']==='expired'){
            $payment_charging = $this->payment_charging(null,null, $billing_details_id);
        }elseif($rs['session_type']==='profile'){
            $payment_charging = null;
              $package_rs = [
                'user_id'=>$user->id,
                'package_id'=>$package_id,
                'payment_type'=>$payment_type,
                'billing_type'=>$billing_type,
                'paid'=>$paid
            ];
            $user_pkg = $user_package->saveUserPackage($package_rs);

        }else{
            $billing_details = BillingDetail::where('id','=',$billing_details_id)->first();
            $billing_details->stripe_session_id = $session_id;
            $billing_details->save();
            $payment_charging = $this->payment_charging(null,null, $billing_details_id);
        }
      
        Mail::to($user->email)->send(new PaymentSuccessMail($user, $session_status));
        return [
            'status'=>'success',
            'data'=> $payment_session,
            'session_status'=> $session_status,
            'payment_status'=> $payment_status,
            'package_info'=> null,
            'user_id'=>$user->id,
            'package_id'=>$package_id,
            'payment_type'=>$payment_type,
            'billing_type'=>$billing_type,
            'payment_charging'=>$payment_charging,
            'rs'=>$rs->all(),
        ];
    }



    public function cronPaymentProcessSuccess($rs){
        $session_id = $rs['id'];
        $user = User::where('id','=',$rs['user_id'])->first();

        $payment_session = PaymentSession::where('user_id','=',$user->id)
                            ->where('payment_session_id','=',$session_id)->first();
        if(empty($payment_session ) || $payment_session->paid===1){
            return [
                'data'=>$session_id,
                'payment_session'=>$payment_session,
                'status'=>'error',
                'code'=>'601',
                'message'=> 'Invalid payment request!',
            ];
        }   

        $user_package = new UserPackage;
        $session_status = $user_package->retriveSessionInfo($session_id);
        $payment_status = "";
        if($session_status->mode==='payment'){
            $payment_status = $user_package->retrivePaymentInfo($session_status->payment_intent);
            if($payment_status->amount_received>0 && 
            $payment_status->status==="succeeded" &&
            $payment_status->charges->data[0]->amount_refunded === 0
            ){
               
            }else{
                return [
                    'status'=>'error',
                    'code'=>'602',
                    'message'=> 'Invalid payment requests!',
                ];
            }
        }elseif($session_status->mode==='subscription'){
            if($session_status->amount_total>0 && 
            !empty($session_status->subscription)
            ){
               
            }else{
                return [
                    'status'=>'error',
                    'code'=>'603',
                    'message'=> 'Invalid payment requests!',
                ];
            }
        }
       
        $session_status->date = date('Y-m-d');
        $meta_data = $session_status->metadata;
        $user_id = $meta_data->user_id;
        $package_id = $meta_data->package_id;
        $payment_type = $meta_data->payment_type;
        $billing_type = $meta_data->billing_type;
        $amount = $meta_data->amount;
        $next_billing = "";
        $package_rs = [
            'user_id'=>$user->id,
            'package_id'=>$package_id,
            'payment_type'=>$payment_type,
            'billing_type'=>$billing_type,
            'paid'=>'paid',
        ];
        $user_pkg = $user_package->saveUserPackage($package_rs);
        $payment_session->paid = 1;
        $payment_session->save();
        if($billing_type==="yes" || $payment_type ==="yearly"){
            $session_type = "profile";
            $next_billing = $this->make_next_billing($session_id, $session_type);
        }

        $user_billing = UserBilling::where('user_id','=',$user->id)->first();
        $user_billing->subscribe_status = 1;
        $user_billing->save();

        //Mail::to($user->email)->send(new PaymentSuccessMail($user, $session_status));
        return [
            'status'=>'success',
            'data'=> $payment_session,
            'session_status'=> $session_status,
            'payment_status'=> $payment_status,
            'user_package'=> $user_pkg,
            'metadata'=> $meta_data,
            'next_billing'=> $next_billing,
        ];
    }


    public function retriveSessionInfo($session_id){
        if(!empty($session_id)){
            $session_status = \Stripe\Checkout\Session::retrieve(
                $session_id
              );
            return $session_status;
        }else{
            return null;
        }
        
    }
    
    public function retrivePaymentInfo($payment_intent_id){
        if(!empty($payment_intent_id)){
            $payment_intent = \Stripe\PaymentIntent::retrieve(
                $payment_intent_id
              );
            return $payment_intent;
        }else{
            return null;
        }
    }


    public function paymentCreateSessionProfile($rs){
        try{
                $user = Auth::user();
                \Stripe\Stripe::setApiKey(env("STRIPE_API_KEY"));
                $success_url = $this->access_url.'payment-success-profile/'.Crypt::encryptString('payment-success').'?session_id={CHECKOUT_SESSION_ID}';
                $cancel_url = $this->access_url."payment-cancel-profile/".Crypt::encryptString('payment-cancel');
                $billing_type = $rs->billing_type;
                $payment_type = $rs->payment_type;
                $stripe_month_price_plan = $rs->stripe_month_price_plan;
                $stripe_year_price_plan = $rs->stripe_year_price_plan;
                $price = $rs->payment_type==="yearly"?$rs->year_amount:$rs->amount;
                $price_plan = "";
                $line_items= "";
                $mode = "setup";
                if($billing_type && $payment_type==="yearly"){
                    $price_plan = $stripe_year_price_plan;
                }elseif($billing_type && $payment_type==="monthly"){
                    $price_plan = $stripe_month_price_plan;
                }
                $customer = \Stripe\Customer::create();
              
                    $line_items=[ 
                        'name' => $rs->item,
                        'description' => $rs->description,
                        'images' => ['http://thisheart.co:8000/images/package-img.png'],
                        'amount' => $price*100,
                        'currency' => 'usd',
                        'quantity' => 1,
                    ];
                    $mode = "setup";
                    $session_create = [
                        'payment_method_types' => ['card'],
                        'customer'=>$customer->id,
                        'metadata'=>[
                            'user_id' => $user->id,
                            'package_id' => $rs->item_id,
                            'package_name' => $rs->item,
                            'amount' => $price*100,
                            'payment_type' => $payment_type,
                            'billing_type' => $billing_type,
                        ],
                        'mode'=>$mode,
                        'success_url' => $success_url,
                        'cancel_url' =>  $cancel_url,
                    ];
                
 
                
                $session = \Stripe\Checkout\Session::create($session_create);
            }catch(Exception $ex){
                $exp = $ex->getMessage();
            }
        if(!empty($session)){
            $session_rs = [
                'package_id'=>$rs->item_id,
                'payment_session_id' => $session->id,
                'paid' => 0,
                'amount' => $price*100,
                'user_package_id' => '0',
                'payment_details_id' => '0',
                'session' => $session,
            ];
            $payment_session = new PaymentSession;
            $payment_session->savePaymentSession($session_rs);
            return [
                'status'=>'success',
                'data'=>$session,
            ];
        }else{
            return [
                'status'=>'error',
                'data'=>$exp
            ];
        }
    }

    public function paymentCreateSessionPayment($rs){
        try{
                $user = Auth::user();
                $success_url = $this->access_url.'payment-success-payment/'.Crypt::encryptString('payment-success').'?session_id={CHECKOUT_SESSION_ID}';
                $cancel_url = $this->access_url."payment-cancel/".Crypt::encryptString('payment-cancel');
                $billing_type = $rs->billing_type;
                $payment_type = $rs->payment_type;
                $billing_details_id = $rs->billing_details_id;
                $stripe_month_price_plan = $rs->stripe_month_price_plan;
                $stripe_year_price_plan = $rs->stripe_year_price_plan;
                $price = $rs->payment_type==="yearly"?$rs->year_amount:$rs->amount;
                $price_plan = "";
                $line_items= "";
                $mode = "setup";
                if($billing_type && $payment_type==="yearly"){
                    $price_plan = $stripe_year_price_plan;
                }elseif($billing_type && $payment_type==="monthly"){
                    $price_plan = $stripe_month_price_plan;
                }
                $customer = \Stripe\Customer::create();
              
                    $line_items=[ 
                        'name' => $rs->item,
                        'description' => $rs->description,
                        'images' => ['http://thisheart.co:8000/images/package-img.png'],
                        'amount' => $price*100,
                        'currency' => 'usd',
                        'quantity' => 1,
                    ];
                    $mode = "setup";
                    $session_create = [
                        'payment_method_types' => ['card'],
                        'customer'=>$customer->id,
                        'metadata'=>[
                            'user_id' => $user->id,
                            'package_id' => $rs->item_id,
                            'package_name' => $rs->item,
                            'amount' => $price*100,
                            'payment_type' => $payment_type,
                            'billing_type' => $billing_type,
                            'billing_type' => $billing_type,
                            'billing_details_id' => $billing_details_id,
                        ],
                        'mode'=>$mode,
                        'success_url' => $success_url,
                        'cancel_url' =>  $cancel_url,
                    ];
                
                $session = \Stripe\Checkout\Session::create($session_create);
            }catch(Exception $ex){
                $exp = $ex->getMessage();
            }
        if(!empty($session)){
            $session_rs = [
                'package_id'=>$rs->item_id,
                'payment_session_id' => $session->id,
                'paid' => 0,
                'amount' => $price*100,
                'user_package_id' => '0',
                'payment_details_id' => '0',
                'session' => $session,
            ];
            $payment_session = new PaymentSession;
            $payment_session->savePaymentSession($session_rs);
            return [
                'status'=>'success',
                'data'=>$session,
            ];
        }else{
            return [
                'status'=>'error',
                'data'=>$exp
            ];
        }
    }

    public function payment_charging($user_id=null, $users=null, $billing_details=null){
        if(!empty($users)){
            $billing_details = BillingDetail::where('paid_status','=',0)->get();
        }elseif(!empty($user_id)){
            $billing_details = BillingDetail::where('paid_status','=',0)->
            where('user_id','=',$user_id)->orderBy('id','desc')->limit(1)->get();
        }elseif(!empty($billing_details)){
            $billing_details = BillingDetail::where('id','=',$billing_details)->get();
        }
        $package_info = null;
        $errorMessage = "";
        $user_package = new UserPackage;
        foreach( $billing_details as $billing){
            $user = User::where('id','=',$billing->user_id)->first();
            $payment_session = PaymentSession::where('payment_session_id','like',
                                $billing->stripe_session_id)->first();
            if(empty($payment_session)){
                return [
                    'status'=>'fail',
                    'code'=>'008',
                    'message'=>'payment session intent not found!'
                ];
            }
           
            $setup_intent = $payment_session->setup_intent;
            $intent = \Stripe\SetupIntent::retrieve($setup_intent);
            $customer = $intent->customer;
            $payment_method = $intent->payment_method;
            $amount = number_format($billing->package_cost*100,0,",","");
            $payment_status = "pending";
            try {
                $payment_intent = \Stripe\PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',
                'customer' =>  $customer,
                'payment_method' => $payment_method,
                'off_session' => true,
                'confirm' => true,
                ]);
               
                $cron_payment_charging = new CronPaymentCharging;
                $cron_payment_charging->user_id = $billing->user_id;
                $cron_payment_charging->billing_detail_id = $billing->id;
                $billing->payment_process_times = $billing->payment_process_times+1;
                
                if($payment_intent->amount_received==$amount && 
                    $payment_intent->charges->data[0]->amount_refunded===0 &&
                    $payment_intent->status==="succeeded"){
                        $cron_payment_charging->payment_status = 'paid';
                        $cron_payment_charging->save();

                        $billing->cron_payment_charging_id = $cron_payment_charging->id;
                        $billing->process_stauts = 'success';
                        $billing->paid_status = 1;
                        $billing->process_date = date('Y-m-d');
                        $billing->save();

                        $user_package_update = $user_package->cronPaymentProcessSuccess([
                                                'id'=>$billing->stripe_session_id,
                                                'user_id'=>$billing->user_id,
                                                ]);
                        $payment_status = "paid";
                        $status = "paid";
                        
                    }else{
                        $cron_payment_charging->payment_status = 'fail';
                        $cron_payment_charging->save();
                        $payment_status = "fail";
                        $errorMessage = "Payment failed!";
                        $status = "failed";
                        if($billing->payment_process_times>1){
                           //do something to block
                           $user_billing = UserBilling::where('user_id','=',$billing->user_id)->first();
                           $user_billing->subscribe_status = 0;
                           $user_billing->notes = "Payment failed";
                           $user_billing->save();
                           $user_package_block = UserPackage::where('user_id','=',$billing->user_id)->first();
                           $user_package_block->subscription_status = 0;
                           $user_package_block->save();
                        }else{
                            $package_rs = [
                                'user_id'=>$billing->user_id,
                                'package_id'=>$billing->package_id,
                                'payment_type'=>$billing->payment_type,
                                'billing_type'=>$billing->recurring_type,
                                'paid'=>'unpaid',
                            ];
                            $user_pkg = $user_package->saveUserPackage($package_rs);
                        }
                    }
            } catch (\Stripe\Exception\CardException $e) {
                $cron_payment_charging = new CronPaymentCharging;
                $cron_payment_charging->user_id = $billing->user_id;
                $cron_payment_charging->billing_detail_id = $billing->id;
                $cron_payment_charging->payment_status = 'fail';
                $cron_payment_charging->notes = $e->getMessage();
                $cron_payment_charging->save();

                $billing->payment_process_times = $billing->payment_process_times+1;
                $billing->cron_payment_charging_id = $cron_payment_charging->id;
                $billing->process_stauts = 'fail';
                $billing->paid_status = 0;
                $billing->process_date = date('Y-m-d');
                $billing->save();
                
                if($billing->payment_process_times>1){
                   //do something to block
                   $user_billing = UserBilling::where('user_id','=',$billing->user_id)->first();
                   $user_billing->subscribe_status = 0;
                   $user_billing->notes = "Payment failed";
                   $user_billing->save();
                   $user_package_block = UserPackage::where('user_id','=',$billing->user_id)->first();
                   $user_package_block->subscription_status = 0;
                   $user_package_block->save();
                }else{
                    $package_rs = [
                        'user_id'=>$billing->user_id,
                        'package_id'=>$billing->package_id,
                        'payment_type'=>$billing->payment_type,
                        'billing_type'=>$billing->recurring_type,
                        'paid'=>'unpaid',
                    ];
                    $user_pkg = $user_package->saveUserPackage($package_rs);
                }
                // echo 'Error code is:' . $e->getError()->code;
                $errorMessage = $e->getMessage();
                $payment_intent_id = $e->getError()->payment_intent->id;
                $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
                $payment_status = "fail";
                $status = "error";
            }
            $package_info = $billing->package_info;
            if($payment_status === "paid"){
                Mail::to($user->email)->send(new PaymentChargingPaidMail($user,$billing));
                return [
                    'status'=>'success',
                    'payment_intent'=>$payment_intent,
                ];
            }elseif($payment_status === "fail"){
                Mail::to($user->email)->send(new PaymentChargingFailMail($user,$billing));
                return [
                    'status'=>'fail',
                    'payment_intent'=>$payment_intent,
                    'message'=>$errorMessage,
                  
                ];
            }//end paid-unpaid 
        }//end foreach loop
    }//end function payment charging



    public function payment_charging_process($billing){
            $user = User::where('id','=',$billing->user_id)->first();
            $payment_session = PaymentSession::where('payment_session_id','=',
                                $billing->stripe_session_id)->first();
            if(empty($payment_session)){
                return [
                    'status'=>'error',
                    'code'=>'009',
                    'message'=>'payment session intent not found!'
                ];
            }
            $setup_intent = $payment_session->setup_intent;
            $intent = \Stripe\SetupIntent::retrieve($setup_intent);
            $customer = $intent->customer;
            $payment_method = $intent->payment_method;
            $amount = $billing->package_cost*100;
            $payment_status = "pending";
            try {
                $payment_intent = \Stripe\PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',
                'customer' =>  $customer,
                'payment_method' => $payment_method,
                'off_session' => true,
                'confirm' => true,
                ]);
               
                $cron_payment_charging = new CronPaymentCharging;
                $cron_payment_charging->user_id = $billing->id;
                $cron_payment_charging->billing_detail_id = $billing->id;
                $billing->payment_process_times = $billing->payment_process_times+1;
                if($payment_intent->amount_received==$amount && 
                    $payment_intent->charges->data[0]->amount_refunded===0 &&
                    $payment_intent->status==="succeeded"){
                        $cron_payment_charging->payment_status = 'paid';
                        $cron_payment_charging->save();

                        $billing->cron_payment_charging_id = $cron_payment_charging->id;
                        $billing->process_stauts = 'success';
                        $billing->paid_status = 1;
                        $billing->process_date = date('Y-m-d');
                        $billing->save();

                        $user_package = new UserPackage;
                        $user_package_update = $user_package->cronPaymentProcessSuccess([
                                                'id'=>$billing->stripe_session_id,
                                                'user_id'=>$billing->user_id,
                                                ]);
                        $payment_status = "paid";
                        $status = "paid";
                        
                    }else{
                        $cron_payment_charging->payment_status = 'fail';
                        $cron_payment_charging->save();
                        $payment_status = "fail";
                        $status = "failed";
                        if($billing->payment_process_times>1){
                           //do something to block
                           $user_billing = UserBilling::where('user_id','=',$billing->user_id)->first();
                           $user_billing->subscribe_status = 0;
                           $user_billing->notes = "Payment failed";
                           $user_billing->save();
                        }
                    }
            } catch (\Stripe\Exception\CardException $e) {
                $cron_payment_charging = new CronPaymentCharging;
                $cron_payment_charging->user_id = $billing->user_id;
                $cron_payment_charging->billing_detail_id = $billing->id;
                $cron_payment_charging->payment_status = 'fail';
                $cron_payment_charging->notes = $e->getMessage();
                $cron_payment_charging->save();

                $billing->payment_process_times = $billing->payment_process_times+1;
                $billing->cron_payment_charging_id = $cron_payment_charging->id;
                $billing->process_stauts = 'fail';
                $billing->paid_status = 0;
                $billing->process_date = date('Y-m-d');
                $billing->save();
                
                if($billing->payment_process_times>1){
                   //do something to block
                   $user_billing = UserBilling::where('user_id','=',$billing->user_id)->first();
                   $user_billing->subscribe_status = 0;
                   $user_billing->notes = "Payment failed";
                   $user_billing->save();
                }

                echo 'Error code is:' . $e->getError()->code;
                $errorMessage = $e->getMessage();
                $payment_intent_id = $e->getError()->payment_intent->id;
                $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
                $payment_status = "fail";
                $status = "error";
            }
            $package_info = $billing->package_info;
            if($payment_status === "paid"){
                Mail::to($user->email)->send(new PaymentChargingPaidMail($user,$billing));
                return [
                    'status'=>'success',
                    'package_info'=>$package_info,
                    'payment_intent'=>$payment_intent,
                    'user_package_update'=>$user_package_update,
                ];
            }elseif($payment_status === "fail"){
                Mail::to($user->email)->send(new PaymentChargingFailMail($user,$billing));
                return [
                    'status'=>'fail',
                    'payment_intent'=>$payment_intent,
                    'message'=>$errorMessage
                ];
            }//end paid-unpaid 
    }//end function payment charging


    //generate bill for next coming month
    public function make_next_billing($session_id, $session_type=null){
        $user_package_billing = new UserPackage;
        $session_status_billing = $user_package_billing->retriveSessionInfo($session_id);
        if(empty($session_status_billing)){
            return [
                'status'=>'error',
                'code'=>'004',
                'message'=> 'Invalid payment requests with session info!',
            ];
        } 

        $meta_data = $session_status_billing->metadata;
        $user_billing = UserBilling::where('user_id','=',$meta_data->user_id)->first();
        if(empty($user_billing)){
            $user_billing = new UserBilling;
        }
        $user_billing->user_id = $meta_data->user_id;
        $user_billing->package_id = $meta_data->package_id;
        $user_billing->package_cost = $meta_data->amount/100;
        $user_billing->subscribe_date = date('Y-m-d');
        // $user_billing->expire_date;
        $user_billing->payment_type = $meta_data->payment_type;
        $user_billing->recurring_type = $meta_data->billing_type;
        $user_billing->subscribe_status=1;
        $user_billing->package_changed=$user_billing->package_changed+1;
        $user_billing->package_changed_date=date("Y-m-d");
        $user_billing->subscribe_status=1;
        $user_billing->save();

        $user_package_data = UserPackage::where('user_id','=',$meta_data->user_id)->first();
        $user_expired = date('Y-m-d',strtotime($user_package_data->subscription_expire_date));
        $billing_start_date = $user_package_data->subscription_date;
        
        $today_date = date("d");
        $dt = date('Y-m-d');
        $billing_month="";
        $billing_month = date("F-Y");
        if($session_type==="expired"){
            if( $meta_data->payment_type==="monthly" &&  $meta_data->billing_type==="yes"){
                $billing_end_date = $user_package_data->subscription_expire_date;
                $billing_date = $dt;
                $next_billing_date = date("Y-m-d",strtotime("$billing_date + 1 Month"));
            }elseif($meta_data->payment_type==="yearly" ){
                $billing_end_date = $user_package_data->subscription_expire_date;
                $billing_date = $dt;
                $next_billing_date = date("Y-m-d",strtotime("$billing_date + 12 Month"));
            }else{
                $billing_end_date = $user_package_data->subscription_expire_date;
                $billing_date = $dt;
                $next_billing_date = date("Y-m-d",strtotime("$billing_date + 1 Month"));
            }
           
        }else{
            if( $meta_data->payment_type==="monthly" &&  $meta_data->billing_type==="yes"){
                $billing_date = date("Y-m-d",strtotime($user_expired));
                $next_billing_date = date("Y-m-d",strtotime("$billing_date + 1 Month"));
                $billing_end_date = $billing_date;
            }elseif($meta_data->payment_type==="yearly" ){
                $billing_date = date("Y-m-d",strtotime($user_expired));
                $next_billing_date = date("Y-m-d",strtotime("$billing_date + 12 Month"));
                $billing_end_date = $billing_date;
            }else{
                $billing_date = date("Y-m-d",strtotime($user_expired));
                $next_billing_date = date("Y-m-d",strtotime("$billing_date + 1 Month"));
                $billing_end_date = $billing_date;
            }
           
        }
        // $billing_details = BillingDetail::where('user_id','=',$meta_data->user_id)->first();
        if(empty($billing_details)){
            $billing_details = new BillingDetail;
        }
        $billing_details->user_id = $meta_data->user_id;
        $billing_details->package_id = $meta_data->package_id;
        $billing_details->billing_month =  $billing_month;
        $billing_details->billing_start_date =  $billing_start_date;
        $billing_details->billing_end_date =  $billing_end_date;
        $billing_details->package_cost = $meta_data->amount/100;
        $billing_details->payment_type = $meta_data->payment_type;
        $billing_details->recurring_type = $meta_data->billing_type;
        $billing_details->stripe_session_id = $session_id;
        $billing_details->billing_date = $billing_date  ;
        $billing_details->next_billing_date = $next_billing_date;
        $billing_details->paid_status=0;
        if($billing_details->save()){
            return ['status'=>'success','billing_details'=>$billing_details];
        }else{
            return ['status'=>'error','code'=>'005', 'message'=>'failed on billing details'];
        }
    }


    public function user_billing(){
        return $this->hasOne(UserBilling::class,'user_id','user_id');
    }

}
