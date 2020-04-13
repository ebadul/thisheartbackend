<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Mail;
use App\Mail\PaymentSuccessMail;
use App\Services\OTPService;
use App\WizardStep;
use App\PackageInfo;
use App\PackageEntity;
use App\UserPackage;
use App\PaymentDetails;
use App\PackageEntitiesInfo;
use App\PaymentSession;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Crypt;
 

class PackagesController extends Controller
{
    protected $access_url = "";
    public function __construct()
    {
        $this->access_url = Request()->headers->get('origin').'/';
    }

    public function getPackages(){
        $packages = PackageInfo::all();
        return response()->json([
            'status'=>'success',
            'data'=>$packages
        ]);
    }

    public function getPackageByUser(){
        $user = Auth::user();
        $packages;
        $expire_days;
        if(!empty($user->user_package)){
            $user_package = $user->user_package;
            $packages = $user->user_package->package_info;
            $now = Carbon::now();
            $expireDate = Carbon::parse($user_package->subscription_expire_date);
            if($expireDate<$now ){
                $expire_days=0;
            }else{
                $expire_days=$expireDate->diffInDays($now);
            }
        }
     
        $all_packages = PackageInfo::all();

        return response()->json([
            'status'=>'success',
            'user_package'=> $user_package,
            'package_info'=> $packages,
            'data'=> $packages,
            'remaining_days'=>$expire_days,
            'all_package_list'=>$all_packages,
        ]);
    }

    public function savePackageInfo(Request $rs){
        $sub_plan = $rs->sub_plan;
        $user = Auth::user();
        if(!empty($sub_plan)){
            $user_id = $user->id;
            $pkgData = [
                'user_id'=>$user_id,
                'package_id'=>$sub_plan
            ];
            $user_package = new UserPackage;
            $user_pkg = $user_package->saveUserPackage($pkgData);
            $user_pkg->push('package_info',$user_pkg->package_info);
            return response()->json([
                'status'=>'success',
                'package_info'=>$user_pkg->package_info,
                'sub_plan'=>$user_pkg,
            ], 200);
        }
    }

    public function savePaymentInfo(Request $rs){
            $payment_detials = new PaymentDetails;
            $res = $payment_detials->savePaymentDetails($rs);
            if($res){
                return response()->json([
                    'status'=>'success',
                    'package_info'=>$res,
                ], 200);
            }else{
                return response()->json([
                    'status'=>'error',
                    'package_info'=>$rs->all(),
                ], 400);
            }
           
      
    }

    public function package_info(){
        $package_info = PackageInfo::all();
        $user = Auth::user();
        return view('admin.package_info',['user'=>$user,'package_info'=>$package_info]);
    }

    public function package_entities_info(){
        $package_entities_info = PackageEntitiesInfo::all();
        $user = Auth::user();
        return view('admin.package_entities_info',['user'=>$user,'package_entities_info'=>$package_entities_info]);
    }

    public function package_entities_info_add(Request $rs){
        //$package_info = PackageEntitiesInfo::all();
        $user = Auth::user();
        if($rs->isMethod('post')){
             $package_entities_info = new PackageEntitiesInfo;
             $package_entities_info->package_entity_title = $rs->entity_title;
             $package_entities_info->package_entity_description = $rs->entity_description;
             $save_pkg_entity = $package_entities_info->save();
             if( $save_pkg_entity){
                $package_entities_info = PackageEntitiesInfo::all();
                return view('admin.package_entities_info',['user'=>$user,'package_entities_info'=>$package_entities_info]);
            }
        }
        
            return view('admin.package_entities_info_add',['user'=>$user]);
        
    }

    public function package_entities_add(Request $rs){
        //$package_info = PackageEntitiesInfo::all();
        $user = Auth::user();
        $package_list = PackageInfo::all();
        $entity_list = PackageEntitiesInfo::all();
        if($rs->isMethod('post')){
             $package_entity = new PackageEntity;
             $package_entity->package_id = $rs->txtPackageID;
             $package_entity->package_entities_id = $rs->txtEntityID;
             $package_entity->entity_value = $rs->entity_value;
             $save_pkg_entity = $package_entity->save();
             if( $save_pkg_entity){
                return redirect('/package_entities');
            }
        }
         
            return view('admin.package_entities_add',['user'=>$user,'package_list'=>$package_list, 'entity_list'=>$entity_list]);
         
    }

    public function package_entities_info_delete($entity_id){
        //$package_info = PackageEntitiesInfo::all();
        $user = Auth::user();
        if(!empty($entity_id)){
             $package_entities_info = PackageEntitiesInfo::where('id','=',$entity_id)->delete();
        }
        if( $package_entities_info){
            return redirect('/package_entities_info');
        }else{
            return redirect('/package_entities_info');
        }
    }

    public function package_entities(){
        $package_entity = PackageEntity::all();
        $user = Auth::user();
        return view('admin.package_entity',['user'=>$user,'package_entity'=>$package_entity]);
    }

    public function user_package(){
        $user = Auth::user();
        $user_package = UserPackage::all();
        return view('admin.user_package',['user'=>$user,'user_package'=>$user_package]);
    }

    public function paymentInit(Request $rs){
        $user = Auth::user();
        $user_package = UserPackage::all();
       

        \Stripe\Stripe::setApiKey("sk_test_9DkPWEVGZrgEo6q9EeZBDXlC00rgoKMYML");
        $customer = \Stripe\Customer::create([
            'email'=>$rs->email,
            'source'=>$rs->source,
        ]);

        $charges = \Stripe\Charge::create([
            'customer'=>$customer->id,
            'amount'=>$rs->amounts,
            'currency'=>'usd',
            'description'=>'shahin2k5@gmail.com',
            'metadata'=>[
                'order_id'=>$rs->id
            ]
        ]);

        $chargesJson = $charges->jsonSerialize();
        return response()->json([
            'status'=>'success',
            'data'=>$chargesJson,
        ], 200);
    }

    public function paymentCreateSession(Request $rs){
        $user = Auth::user();
        $userPackage = new UserPackage;
        $session_info = $userPackage->paymentCreateSession($rs);
        if($session_info['status']==="success"){
            return response()->json([
                'status'=>'success',
                'session'=>$session_info['data'] ,
            ], 200);
        }else{
            return response()->json([
                'status'=>'error',
                 'message'=>$session_info['data']
            ], 500);
        }
    }

    public function paymentSessionSuccess(Request $rs){
        $this->validate($rs,[
            'id'=>'required',
            'session_token'=>'required'
        ]);
        $session_token = $rs->session_token;
        $token = explode('&*^',$session_token);
        if(empty($token[0]) || empty($token[1])){
            return response()->json([
                'status'=>'error',
                'message'=> 'Invalid payment request!',
            ], 500);
        }

        $session_id = $rs->id;
        $user = Auth::user();

        $payment_session = PaymentSession::where('user_id','=',$user->id)
                            ->where('payment_session_id','=',$session_id)->first();
        if(empty($payment_session ) || $payment_session->paid===1){
            return response()->json([
                'status'=>'error',
                'message'=> 'Invalid payment request!',
            ], 500);
        }   

        $user_package = new UserPackage;
        $session_status = $user_package->retriveSessionInfo($session_id);
        $payment_status = $user_package->retrivePaymentInfo($session_status->payment_intent);
        if($payment_status->amount_received>0 && 
            $payment_status->status==="succeeded" &&
            $payment_status->charges->data[0]->amount_refunded === 0
            ){
               
        }else{
            return response()->json([
                'status'=>'error',
                'message'=> 'Invalid payment request!',
            ], 500);
        }
        $session_status->date = date('Y-m-d');
        $meta_data = $session_status->metadata;
        $user_id = $meta_data->user_id;
        $package_id = $meta_data->package_id;
        $amount = $meta_data->amount;
    
        $package_rs = [
            'user_id'=>$user->id,
            'package_id'=>$package_id
        ];
        $user_pkg = $user_package->saveUserPackage($package_rs);

        $payment_session->paid = 1;
        $payment_session->save();
        Mail::to($user->email)->send(new PaymentSuccessMail($user, $session_status));
        return response()->json([
            'status'=>'success',
            'data'=> $payment_session,
            'session_status'=> $session_status,
            'payment_status'=> $payment_status,
            'package_info'=> $user_pkg,
        ], 200);
    }

    // public function setSteps(Request $rs){
    //     $user = Auth::user();
    //     $step = $rs->step;
    //     $info = $rs->info;
    //     $wizStep = WizardStep::where('user_id','=',$user->id)->where('steps',$rs->step)->first();
    //     if(empty($wizStep)){
    //         $wizStep = new WizardStep;
    //     }
    //     $wizStep->user_id = $user->id;
    //     $wizStep->steps = $rs->step;
    //     $wizStep->status = 1;
    //     $wizStep->info = $rs->info;
    //     $wizStep->save();

    //     return response()->json([
    //         'status'=>'success',
    //         'data'=>$wizStep
    //     ]);
    // }
}
