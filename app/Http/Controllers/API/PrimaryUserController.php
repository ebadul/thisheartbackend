<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;



use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\User;
use App\UserActivity;
use App\InactiveUserNotify;
use App\BeneficiaryUser;
use App\Memories;
use App\Account;
use App\PackageInfo;
use App\UserPackage;
use App\FreeAccount;
use App\UserBilling;
use App\BillingDetail;
use Validator;
use Auth;
use Mail;
use Artisan;
use Carbon\Carbon;
use App\Mail\InactiveUserMail;
use App\Mail\FreeAccountMail;
use Illuminate\Support\Facades\Crypt;
use Session;


class PrimaryUserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $access_url = "";
    public function __construct()
    {
        $this->access_url = Request()->headers->get('origin').'/';
        //$this->middleware('auth');
    }

    public function loginAdmin (Request $request) {
       
        return view('admin.login');
    }

    public function adminLogout () {
        if(Auth::check()){
            Auth::logout();
        }
        Session::flush();
        return redirect('/login');
    }

    public function adminUser (Request $request) {
      
        if(Auth::check()){
            return redirect('/dashboard');
        }else{
            return redirect('login');
        }
    }

    
    public function dashboard (Request $request) {
        if(Auth::check()){
            $user= Auth::user();
            $primary_accounts = User:: where('user_type','2')-> count () ;
            $beneficiary_accounts = User:: where('user_type','3')-> count () ;
         
            return view('admin.dashboard')->with(['user'=>$user,'primary_users'=>$primary_accounts ,'beneficiary_count'=>$beneficiary_accounts]);
        }else{
            return redirect('login');
        }
    }

    public function primary_user_login (Request $request) {
         
        $validate = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'
        ]);
        
        if($validate->fails()){
            return redirect('/login')->with(
                'warning',"User or password doesn't match"
            );
        }
        $userTmp = new User;
        $user_type_id = $userTmp->getUserTypeID('admin');
        try{
             if(Auth::attempt([
                    'email'=>$request->email, 
                    'password'=>$request->password,
                    'user_type'=>$user_type_id]) ){
                $user = Auth::user();
                return redirect('/dashboard');
             }else{
                Auth::logout();
                return redirect('/login')->with(
                    'warning',"User or password doesn't match"
                );
             }
           
        }catch(Exception $ex){
            //throw new \Exception($ex->getMessage());
            return redirect('/login')->with(
                'warning',$ex->getMessage()
            );
        }
        
    }

    public function emailTest(){
        $user = Auth::user();
        $url_token= str_random(16);
        $email_str = Crypt::encryptString($user->email);
        
        // 'user_id','verified_token','email_verified'
        $emailVerifiedData = [
            'name'=>Crypt::encryptString("John Dollar"),
            'user_id'=>$user->id,
            'verified_token'=> $url_token,
            'email_verified'=> 0,
            'login_url' => 'email_verification/'.$url_token.'/'.$email_str,
            'email_str' => $email_str,
        ];
        
        $to_name = "Gold Smith";
        $to_email = "shahin2k5@gmail.com";
     
        Mail::send('emails.register-primary-user', $emailVerifiedData, 
            function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                    ->subject('[thisheart.co] Activate your account');
            $message->from('thisheartmailer@gmail.com','ThisHeart');
        });



    }

    public function primary_user () {
        $primary_accounts = User:: where('user_type','2')-> get ()->toArray();
        $user = Auth::user();
        return view ('admin.primary_user', ['primary_accounts'=>$primary_accounts,'user'=>$user]);
    }


    public function user_activities () {
        $user_activities = UserActivity:: all();
        $user = Auth::user();
        return view ('admin.user_activities', ['user_activities'=>$user_activities,'user'=>$user]);
    }

   

    public function updateUserById(Request $request)
    {

        $data = User::find($request->user_id);

            $data->name = $request->user_name;
            $data->email = $request->email;
            $data ->mobile = $request->mobile;

            $data->save();
        
        return response()->json([
            'status'=>'success',
            'message' => 'Data Updated successfully!',
            'data' => $request->all()
        ],200);
    }
    public function changeStatus(Request $request)
    {
        $user = User::where('id','=',$request->user_id)->first();
        $user->active = $request->active;
        if($user->save()){
            return response()->json([
                'status'=>'success',
                'success'=>'Status change successfully.'
                ]);
        }else{
            return response()->json([
                'status'=>'error',
                'data'=>$request->all(),
                'message'=>'sorry, user status is not changed!.'
                ]);
        }
        
        
    }

    public function inactive_primary_users () {
        $days=isset($_GET['days'])?$_GET['days']:0;
        $user_activities = User::where( 'last_login', '<', Carbon::now()->subDays($days))
        ->get();
         
        $user = Auth::user();
        return view ('admin.inactive_primary_users', ['user_activities'=>$user_activities,'user'=>$user]);
    }
    
    public function inactive_beneficiary_users () {
        $user = Auth::user();
        $user_type_id = $user->getUserTypeID("primary");
        $days=isset($_GET['days'])?$_GET['days']:0;
        $user_activities = User::where( 'last_login', '<', Carbon::now()->subDays($days))
        ->where('user_type',$user_type_id)
        ->get();
         
        return view ('admin.inactive_beneficiary_users', ['user_activities'=>$user_activities,'user'=>$user]);
    }

    public function inactive_user_send_email(Request $request){
       
        $user_id_list = $request->userList;
        $action_type = $request->actionType;
        $sendStatus = "";
        if(!empty($user_id_list)){
            $inactive_user_notify = new InactiveUserNotify;
            if($action_type==="first_send_email"){
                $inactive_user_notify->sendFirstEmailPrimary($user_id_list);
            }elseif($action_type==="second_send_email"){
                $inactive_user_notify->sendSecondEmailPrimary($user_id_list);
            }elseif($action_type==="send_sms"){
                $sendStatus =  $inactive_user_notify->sendSMSPrimary($user_id_list);
            }elseif($action_type==="send_email_beneficiary_user"){
                $sendStatus = $inactive_user_notify->sendEmailBeneficiary($user_id_list);
            }elseif($action_type==="send_sms_beneficiary_user"){
                $sendStatus = $inactive_user_notify->sendSMSBeneficiary($user_id_list);
            }elseif($action_type==="final_make_call"){
                $inactive_user_notify->finalMakeCallPrimary($user_id_list);
            }
        } else{
            return response()->json([
                'status'=>'error',
                'message'=>'Please select at least one primary user from the list!',
                'data'=>$user_id_list
            ],400);
        }
       
        return response()->json([
            'status'=>'success',
            'data'=>$sendStatus , 
            'action_type'=>$action_type
        ]);
    }

    public function inactive_user_send_email_automation(Request $request){
        $user_id_list = $request->userList;
        $action_type = $request->actionType;
        $smsStatus = "";
        if(!empty($user_id_list)){
            $inactive_user_notify = new InactiveUserNotify;
            $sendStatus = $inactive_user_notify->sendEmailAutomation($user_id_list);
            
        } else{
            return response()->json([
                'status'=>'error',
                'message'=>'Please select at least one primary user from the list!',
                'data'=>$user_id_list
            ],400);
        }
       
        return response()->json([
            'status'=>'success',
            'data'=>$sendStatus , 
            'action_type'=>$action_type
        ]);
    }


    public function delete_primary_user($user_id){
        try{
            if(!empty($user_id))  {
                $user = User::where('id','=',$user_id)->first();
                if(!empty($user)){
                    $memories_count = Memories::where('user_id','=',$user->id)->count();
                    $account_count = Account::where('user_id','=',$user->id)->count();
                    $beneficiary_count = User::where('beneficiary_id','=',$user->id)->count();
                    if($memories_count>0 || $account_count>0|| $beneficiary_count>0){
                        return redirect('/primary_user')->with("warning","Sorry, you can't delete this user because 
                                                            user has memories, accounts or beneficiaries data!");  
                    }
                    if($user->delete()){
                        return redirect('/primary_user')->with('success','User has been deleted successfully!');
                    }else{
                        return redirect('/primary_user')->with('warning','Sorry, user not deleted!');
                    }
                }
            }
        }catch(Exception $ex){
            return redirect('/primary_user')->with('warning','Sorry, user not deleted!');
        }
     
    }

    public function delete_beneficiary_user($user_id=null){
  
            $deleted = false;
            if(!empty($user_id))  {
                $user = User::where('id','=',$user_id)->first();
                if(!empty($user)){
                    $user_email = $user->email;
                    if($user->delete()){
                        $beneficiary_user = BeneficiaryUser::where('email','=',$user_email
                        )->first();
                        if(!empty($beneficiary_user)){
                            $beneficiary_user->delete();
                            $deleted = true;
                        } 
                        $deleted = true;
                        
                    }else{
                        $deleted = false;
                    }
                }else{
                    $deleted = false;
                }
            }else{
                $deleted = false;
            }
       

        if($deleted){
            return redirect()->back()->with('success','User has been deleted successfully!');
        }else{
            return redirect()->back()->with('warning','Sorry, user not deleted!');
        }
        
    }

    public function user_activities_delete($activities_id){
        $user_activity = UserActivity::where('id','=',$activities_id)->first();
        if(!empty($user_activity)){
            $user_activity->delete();
        }

        return redirect('/user_activities');
    }
    
    public function inactive_user_notify_edit(Request $rs){
         
        $user = Auth::user();
        if($rs->isMethod('post')){
            try{
                $id = $rs->id;
                $user_id = $rs->user_id;
                $last_login = $rs->last_login;
                $notes = $rs->notes;
                $inactive_user_notify = InactiveUserNotify::where('id','=',$id)->first();
                if(!empty($inactive_user_notify)){
                    $inactive_user_notify->last_login = $last_login;
                    $inactive_user_notify->notes = $notes;
                    $save_inactive_user_notify = $inactive_user_notify->save();
                    if($save_inactive_user_notify){
                        return response()->json([
                            'status'=>'success',
                            'message'=>'Data saved successfully!'
                        ]);
                    }else{
                        
                        return response()->json([
                            'status'=>'error',
                            'message'=>'Sorry, data saved fail!',
                            'data'=>$rs->all(),
                        ],500);
                    }
                }else{
                    $inactive_user_notify = new InactiveUserNotify;
                    $inactive_user_notify->user_id = $user_id;
                    $inactive_user_notify->last_login = $last_login;
                    $inactive_user_notify->notes = $notes;
                    $save_inactive_user_notify = $inactive_user_notify->save();
                    if($save_inactive_user_notify){
                        return response()->json([
                            'status'=>'success',
                            'message'=>'Data saved successfully!'
                        ]);
                    }else{
                        
                        return response()->json([
                            'status'=>'error',
                            'message'=>'Sorry, data saved fail!',
                            'data'=>$rs->all(),
                        ],500);
                    }
                }
            }catch(Exception $ex){
                return response()->json([
                    'status'=>'error',
                    'message'=>$ex->getMessage()
                ],500);  
            }
            
        } 
    }


    
    public function free_account($free_account_status=null){
        $user = Auth::user();
        $package_list = PackageInfo::all();
        $user_package = UserPackage::all();
        if(!empty($free_account_status)){
            $free_account = FreeAccount::where('status','=',$free_account_status)->get();
        }else{
             $free_account = FreeAccount::all();
        }
        
        return view('admin.free_account',[
                    'user'=>$user,
                    'user_package'=>$user_package,
                    'package_list'=>$package_list,
                    ]);
    }


    public function free_user_package_edit(Request $rs){
        $admin_user = Auth::user();
        $user_id = $rs->user_id;
        $user = User::where('id','=',$user_id)->first();
        $user_package_id = $rs->user_package_id;
        $package_id = $rs->package_id;
        $subscription_date = $rs->subscription_date;
        $subscription_expire_date = $rs->subscription_expire_date;
        //$subscription_status = $rs->subscription_status;

        $user_package = UserPackage::where('id','=',$user_package_id)->first();
        $free_account = FreeAccount::where('user_id','=', $user_id)->first();

        $user_email = $user->email;
        $activation_code = Crypt::encryptString($user_email);
        $login_url = "https://thisheart.co/freeadminlogin/";
        $data = ['user'=>$user,
                'user_package'=>$user_package,
                'activation_code'=>$activation_code, 
                'login_url'=>$login_url];
        if(empty($free_account)){
            $free_account = new FreeAccount;
        }
            $free_account->user_id = $user_id;
            $free_account->activation_code = $activation_code;
            $free_account->requested_by = $admin_user->id;
            $free_account->status = 'pending';
            if($free_account->save()){
                Mail::to("thisheartmailer@gmail.com")->send(new FreeAccountMail($data));
                return response()->json([
                    'status'=>'success',
                    'user_package'=>$user_package,
                    'user_package'=>$rs->all(),
                ], 200);
            }else{
                return response()->json([
                    'status'=>'error',
                    'message'=>'Error was occured',
                    'user_package'=>$rs->all(),
                ], 500);
            }
       
        return response()->json([
            'status'=>'error',
            'message'=>'Errors was occured',
            'user_package'=>$rs->all(),
        ], 500);  
    }

    public function approvedFreeAccount(Request $rs){
        $validator = Validator::make($rs->all(), [
            'freeAccountRequested' => 'required',
            'activationCode' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=>'fail',
                'message' => 'Sorry, free account setup is failed!',
                'data'=>$validator->errors(),
                'tmp'=>$rs->all()
            ], 200);    
        }

        $free_requested = $rs->freeAccountRequested;
        $activation_code = $rs->activationCode;
        $free_account = FreeAccount::where('activation_code','=', $activation_code)->
                        where('verified','=','0')->
                        where('status','=','pending')->first();
        if(empty($free_account)){
            return response()->json([
                'status'=>'error',
                'message'=>'Sorry, free account setup is already processed',
                'data'=>$rs->all(),
            ], 500); 
        }
        $user_id = $free_account->user_id;
        $package_info = PackageInfo::where('package','free account')->first();
        $user_package = UserPackage::where('user_id','=',$user_id)->first();
      
        $free_account->verified = 1;
        if($free_requested ==="approved"){
            $free_account->status = 'activated';
            $user_package->package_id = $package_info->id;
        }elseif($free_requested ==="rejected"){
            $free_account->status = 'denied';
        }
       
        if($free_account->save()){
            
            $user_package->save();
            return response()->json([
                'status'=>'success',
                'data'=>$rs->all(),
            ], 200);

        }else{
            return response()->json([
                'status'=>'error',
                'message'=>'Errors was occured',
                'data'=>$rs->all(),
            ], 500); 
        }
        
    }


    public function unsubscribed_user($subscribed_status=0){
        $user = Auth::user();
        if($subscribed_status==="1"){
            $user_list = UserBilling::where('subscribe_status','=','1')->get();
        }else{
            $user_list = UserBilling::where('subscribe_status','=','0')->get();
        }
        
        $package_list = PackageInfo::all();
        $user_package = "";
        
        return view('admin.unsubscribed_user',[
                    'user'=>$user,
                    'user_list'=>$user_list,
                    'user_package'=>$user_package,
                    'package_list'=>$package_list,
                    ]);
    }

    public function billing_details($month=null){
        $user = Auth::user();
        if(empty($subscribed_status)){
            $billing_list = BillingDetail::all();
        }else{
            $billing_list = BillingDetail::where('subscribe_status','=','0')->get();
        }
        
        $package_list = PackageInfo::all();
        $user_package = "";
        
        return view('admin.billing_details',[
                    'user'=>$user,
                    'billing_list'=>$billing_list,
                    'user_package'=>$user_package,
                    'package_list'=>$package_list,
                    ]);
    }


    public function admin_payment_charging(Request $rs){
        $billing_details = BillingDetail::where('id','=',$rs->billing_details_id)->first();
        $user_package = new UserPackage;
        $payment_charging = $user_package->payment_charging($billing_details->user_id);
        if($payment_charging['status']==="success"){
            return response()->json([
                'status'=>'success',
            ],200);
        }else{
            return response()->json([
                'status'=>'error',
                'code'=>empty($payment_charging['code'])?null:$payment_charging['code'],
                'message'=>$payment_charging['message'],
            ],500);
        }
        
    }


    public function payment_fail_notification_command(){
        $exitCode = Artisan::call('payment_fail:notification');
    }
    public function payment_charging_command(){
        $exitCode = Artisan::call('payment:charging');
    }

}