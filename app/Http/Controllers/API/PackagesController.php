<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Mail;
use App\Mail\PaymentSuccessMail;
use App\Mail\PaymentChargingMail;
use App\Mail\UnsubscribePackageMail;
use App\Mail\UserUnblockedMail;
use App\Services\OTPService;
use App\WizardStep;
use App\PackageInfo;
use App\PackageEntity;
use App\UserPackage;
use App\PaymentDetails;
use App\PackageEntitiesInfo;
use App\PaymentSession;
use App\UserBilling;
use App\BillingDetail;
use App\User;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Crypt;
 

class PackagesController extends Controller
{
    protected $access_url = "";
    public function __construct()
    {
        $this->access_url = Request()->headers->get('origin').'/';
        $this->middleware('auth');
    }

    public function getPackages(){
        $packages = PackageInfo::orderBy('price')->get();
        return response()->json([
            'status'=>'success',
            'data'=>$packages
        ]);
    }

    public function getPackageByUser(){
        $user = Auth::user();
        $packages;
        $expire_days;
        $user_package;
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
     
        $all_packages = PackageInfo::orderBy('price')->get();
        $billing_details = BillingDetail::where(['user_id'=>$user->id, 'paid_status'=>0])->
                                          whereNotNull('stripe_session_id')->
                                          whereNotNull('cron_payment_charging_id')->
                                          where('payment_process_times','>',0)->
                                          first();

        return response()->json([
            'status'=>'success',
            'user_package'=> $user_package,
            'package_info'=> $packages,
            'billing_details'=> $billing_details,
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
            if(empty($rs->trial_end)){
                $pkgData = [
                    'user_id'=>$user_id,
                    'package_id'=>$sub_plan
                ];
            }else{
                $pkgData = [
                    'user_id'=>$user_id,
                    'package_id'=>$sub_plan,
                    'trial_end'=>$rs->trial_end
                ];
            }
           
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
    public function unsubscribePackage(Request $rs){
            $user = Auth::user();
            $dt = date("Y-m-d");
            $expire_date  = date("Y-m-d",strtotime("$dt + 30 Day"));
            $user_billing = UserBilling::where('user_id','=',$user->id)->
                            where('subscribe_status','=',1)->first();
            if(empty($user_billing)){
                return response()->json([
                    'status'=>'error',
                    'code'=>'201',
                    'package_info'=>"Unable to unsubscribe the package!",
                ], 500);
            }
            $user_billing->expire_date =  $expire_date ;
            $user_billing->subscribe_status = 0;
            $user_billing->notes = "unsubscribed";
            $user_billing->save();

           
     
    
            $today_date = date("d");
            $billing_month="";
      
            $billing_month = date("F-Y");
            $billing_date = date("Y-m",strtotime("$dt + 1 Month"))."-01";
            //$next_billing_date = date("Y-m",strtotime("$dt + 2 Month"))."-01";
            $user_package = UserPackage::where('user_id','=',$user->id)->first();
    
            $billing_details = new BillingDetail;
            $billing_details->user_id = $user->id;
            $billing_details->package_id = $user_billing->package_id;
            $billing_details->billing_month =  $billing_month;
            $billing_details->billing_start_date=  $user_package->subscription_date;
            $billing_details->billing_end_date=  $expire_date;
            $billing_details->package_cost = $user_billing->package_cost;
            $billing_details->payment_type = $user_billing->payment_type;
            $billing_details->recurring_type = $user_billing->recurring_type;
            $billing_details->stripe_session_id = null;
            $billing_details->billing_date = $billing_date  ;
            $billing_details->paid_status=0;
            $billing_details->save();
            

            
           
            $user_package->subscription_expire_date = $expire_date;
            $user_package->save();
          
            Mail::to($user->email)->send(new UnsubscribePackageMail($user,null));

            return response()->json([
                'status'=>'success',
                'user_billing'=>$user_billing,
            ], 200);
 
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
        $package_info = PackageInfo::orderBy('price')->get();
        $user = Auth::user();
        return view('admin.package_info',['user'=>$user,'package_info'=>$package_info]);
    }

    public function package_info_edit(Request $rs){
        $user = Auth::user();
        $id = $rs->id;
        $package = $rs->package;
        $description = $rs->description;
        $price = $rs->price;
        $days = $rs->days;

        $package_info = PackageInfo::where('id','=',$id)->first();
        if(!empty($package_info)){
            $package_info->package = $package;
            $package_info->description = $description;
            $package_info->price = $price;
            $package_info->days = $days;
            if($package_info->save()){
                return response()->json([
                    'status'=>'success',
                    'package_info'=>$package_info,
                ], 200);
            }else{
                return response()->json([
                    'status'=>'error',
                    'package_info'=>$rs->all(),
                ], 200);
            }
        }
       
        //return view('admin.package_info',['user'=>$user,'package_info'=>$package_info]);
    }

    public function package_entities_edit(Request $rs){
        $user = Auth::user();
        $package_entities_id = $rs->package_entities_id;
        $package_id = $rs->package_id;
        $entities_id = $rs->entities_id;
        $entity_unit = $rs->entity_unit;
        $entity_value = $rs->entity_value;
        $entity_status = $rs->entity_status;

        $package_entity = PackageEntity::where('id','=',$package_entities_id)->first();
        if(!empty($package_entity)){
            $package_entity->package_id = $package_id;
            $package_entity->package_entities_id = $entities_id;
            $package_entity->entity_unit = $entity_unit;
            $package_entity->entity_value = $entity_value;
            $package_entity->entity_status = $entity_status;
            if($package_entity->save()){
                return response()->json([
                    'status'=>'success',
                    'package_entity'=>$package_entity,
                ], 200);
            }else{
                return response()->json([
                    'status'=>'error',
                    'package_entity'=>$rs->all(),
                ], 200);
            }
        }
       
        
    }

    public function user_package_edit(Request $rs){
        $user = User::where('id','=',$rs->user_id)->first();
        $user_package_id = $rs->user_package_id;
        $user_id = $rs->user_id;
        $package_id = $rs->package_id;
        $subscription_date = $rs->subscription_date;
        $subscription_expire_date = $rs->subscription_expire_date;
        $subscription_status = $rs->subscription_status;

        $user_package = UserPackage::where('id','=',$user_package_id)->first();
        if(!empty($user_package)){
            $user_package->package_id = $package_id;
            $user_package->subscription_date = $subscription_date;
            $user_package->subscription_expire_date = $subscription_expire_date;
            $user_package->subscription_status = $subscription_status;
            if($user_package->save()){
                if($subscription_status>0){
                    Mail::to($user->email)->send(new UserUnblockedMail($user,$user_package));
                }
                return response()->json([
                    'status'=>'success',
                    'user_package'=>$user_package,
                    'data'=>$rs->all(),
                ], 200);
            }else{
                return response()->json([
                    'status'=>'error',
                    'user_package'=>$rs->all(),
                ], 500);
            }
        }
       
        return response()->json([
            'status'=>'error',
            'user_package'=>$rs->all(),
        ], 500);  
    }

    public function delete_package_info($package_id){
        $package_info = PackageInfo::where('id','=',$package_id)->first();
        if(!empty($package_info)){
            if($package_info->delete()){
                return redirect('/package_info')->with('success','Package deleted successfully');
            }else{
                return redirect('/package_info')->with('warning','Sorry, package not deleted'); 
            }
        }else{
            return redirect('/package_info')->with('warning','Sorry, package not deleted');
        }
    
        
    }

    public function user_package_delete($package_id){
        $user_package = UserPackage::where('id','=',$package_id)->first();
        $deleted = false;
        if(!empty($user_package)){
            $deleted = $user_package->delete();
        }
        if( $deleted){
            return redirect('/user_package')->with('success','user package deleted successfully!');
        }else{
            return redirect('/user_package')->with('warning','Sorry, user package not deleted!');
        }
        
    }

    public function package_entities_delete($package_entity_id){
        $package_entity = PackageEntity::where('id','=',$package_entity_id)->first();
        $user = Auth::user();
        $deleted = false;
        if(!empty($package_entity)){
            $deleted = $package_entity->delete();
        }
        if($deleted){
            return redirect('/package_entity'.'/'.$package_entity->package_id)->with('success','Package entity deleted successfully!');
        }else{
            return redirect('/package_entity'.'/'.$package_entity->package_id)->with('warning','Sorry, package entity not deleted!');
        }
        
    }

    public function package_entities_info(){
        $package_entities_info = PackageEntitiesInfo::all();
        $user = Auth::user();
        return view('admin.package_entities_info',['user'=>$user,'package_entities_info'=>$package_entities_info]);
    }

    public function package_info_add(Request $rs){
        //$package_info = PackageEntitiesInfo::all();
        $user = Auth::user();
        if($rs->isMethod('post')){
             $package_info = new PackageInfo;
             $package_info->package = $rs->package;
             $package_info->description = $rs->description;
             $package_info->price = $rs->price;
             $package_info->days = $rs->days;
             $save_pkg_info = $package_info->save();
             if( $save_pkg_info){
                $package_info = PackageInfo::all();
                //return view('admin.package_info',['user'=>$user,'package_info'=>$package_info]);
                return redirect('/package_info');
            }
        }
        
            return view('admin.package_info_add',['user'=>$user]);
        
    }
    public function package_entities_info_add(Request $rs){
        $user = Auth::user();
        if($rs->isMethod('post')){
             $package_entities_info = new PackageEntitiesInfo;
             $package_entities_info->package_entity_title = $rs->entity_title;
             $package_entities_info->package_entity_description = $rs->entity_description;
             $save_pkg_entity = $package_entities_info->save();
             if( $save_pkg_entity){
                $package_entities_info = PackageEntitiesInfo::all();
                //return view('admin.package_entities_info',['user'=>$user,'package_entities_info'=>$package_entities_info]);
                return redirect("/package_entities_info");
            }
        }
        
            return view('admin.package_entities_info_add',['user'=>$user]);
        
    }

    public function package_entities_info_edit(Request $rs){
        //$package_info = PackageEntitiesInfo::all();
        $user = Auth::user();
        if($rs->isMethod('post')){
             $package_entities_info = PackageEntitiesInfo::where('id','=',$rs->entity_id)->first();
             if(!empty($package_entities_info)){
                $package_entities_info->package_entity_title = $rs->entity_title;
                $package_entities_info->package_entity_description = $rs->description;
                $save_pkg_entity = $package_entities_info->save();
                if( $save_pkg_entity){
                    return response()->json([
                        'status'=>'success',
                        'message'=>'Entity info saved successfully!'
                    ]);
                }else{
                    return response()->json([
                        'status'=>'error',
                        'message'=>'Sorry, entity info saved fail!'
                    ],500);
                }
             }else{
                return response()->json([
                    'status'=>'error',
                    'message'=>'Sorry, entity info saved fail!'
                ],500);
            }
            
        }
        
             
        
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
             $package_entity->entity_unit = $rs->txtUnit;
             $package_entity->entity_value = $rs->entity_value;
             $save_pkg_entity = $package_entity->save();
             if( $save_pkg_entity){
                return redirect('/package_entity'.'/'.$package_entity->package_id);

            }
        }
         
            return view('admin.package_entities_add',['user'=>$user,'package_list'=>$package_list, 'entity_list'=>$entity_list]);
         return redirect('/package_entities_info');
    }

    public function package_entities_info_delete($entity_id){
        //$package_info = PackageEntitiesInfo::all();
        $user = Auth::user();
        if(!empty($entity_id)){
             $package_entities_info = PackageEntitiesInfo::where('id','=',$entity_id)->delete();
        }
        if( $package_entities_info){
            return redirect('/package_entities_info')->with('success','Entity info deleted successfully!');
        }else{
            return redirect('/package_entities_info')->with('warning','Sorry, entity info not deleted!');
        }
    }

    public function package_entities($package_id=null){
        if($package_id>0){
            $package_entity = PackageEntity::where('package_id',$package_id)->get();
        }else{
            $package_entity = PackageEntity::all();
        }
        $package_list= PackageInfo::all();
        $entity_list = PackageEntitiesInfo::all();
        $user = Auth::user();
        return view('admin.package_entity',[
                    'user'=>$user,
                    'package_entity'=>$package_entity,
                    'package_list'=>$package_list,
                    'entity_list'=>$entity_list
                    ]);
    }

    public function user_package(){
        $user = Auth::user();
        $package_list = PackageInfo::all();
        $user_package = UserPackage::all();
        return view('admin.user_package',['user'=>$user,
                    'user_package'=>$user_package,
                    'package_list'=>$package_list]);
    }

    public function paymentCreateSession(Request $rs){
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
            'session_token'=>'required',
        ]);
        $user_package = new UserPackage;
        $payment_session = $user_package->paymentSessionSuccess($rs);
        
        if($payment_session['status']==="success"){
            return response()->json($payment_session, 200);
        }else{
            return response()->json($payment_session, 500);
        }
    }


    public function paymentCreateSessionProfile(Request $rs){
        $user = Auth::user();
        $userPackage = new UserPackage;
        $package_id = $rs->item_id;
        $today = date('Y-m-d');
         
        $user_pkg = UserPackage::where('user_id','=',$user->id)->first();
        if(!empty($user_pkg) && 
            $user_pkg->subscription_expire_date >$today &&  
            $user_pkg->package_id===$package_id){
            return response()->json([
                'status'=>'error',
                 'message'=>$rs->item. ", package is subscribed already!"
            ], 500);
        }

        $user_billing = UserBilling::where([
            ['user_id','=',$user->id],
            ['package_changed','>',0]])->first();

        if(!empty($user_billing)){
            $package_changed_date  = $user_billing->package_changed_date;  
            if($package_changed_date>=$user_pkg->subscription_date && 
            $package_changed_date<=$user_pkg->subscription_expire_date){
                return response()->json([
                    'status'=>'error',
                     'message'=>"You're not allowed to change packages right now!"
                ], 500);
            }
        }

        $session_info = $userPackage->paymentCreateSessionProfile($rs);
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


    public function paymentCreateSessionPayment(Request $rs){
        $user = Auth::user();
        $userPackage = new UserPackage;
        $session_info = $userPackage->paymentCreateSessionPayment($rs);
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

    public function paymentSessionSuccessProfile(Request $rs){
        $this->validate($rs,[
            'id'=>'required',
            'session_token'=>'required',
        ]);
        $user_package = new UserPackage;
        $payment_session = $user_package->paymentSessionSuccess($rs);
        
        if($payment_session['status']==="success"){
            return response()->json($payment_session, 200);
        }else{
            return response()->json($payment_session, 500);
        }
    } 

    public function paymentSessionSuccessPayment(Request $rs){
        $this->validate($rs,[
            'id'=>'required',
            'session_token'=>'required',
        ]);
        $user_package = new UserPackage;
        $payment_session = $user_package->paymentSessionSuccessPayment($rs);
        
        if($payment_session['status']==="success"){
            return response()->json($payment_session, 200);
        }else{
            return response()->json($payment_session, 500);
        }
    }     
}
