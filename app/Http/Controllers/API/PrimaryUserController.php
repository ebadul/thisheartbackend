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
use Validator;
use Auth;
use Mail;
use Carbon\Carbon;
use App\Mail\InactiveUserMail;
use Illuminate\Support\Facades\Crypt;
use Session;


class PrimaryUserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
            $message->from('thisheartmailer@gmail.com','This-Heart');
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
}