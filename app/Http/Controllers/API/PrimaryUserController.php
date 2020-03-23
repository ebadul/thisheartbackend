<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;



use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\User;
use App\UserActivity;
use App\InactiveUserNotify;
use Validator;
use Auth;
use Mail;
use Carbon\Carbon;
use App\Mail\InactiveUserMail;

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

    public function adminLogout (Request $request) {
        Auth::logout();
        return view('admin.login');
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
            return redirect('/login')->withErrors([
                'message'=>"User or password doesn't match"
            ]);
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
                return redirect('/login')->withErrors([
                    'message'=>"User or password doesn't match"
                ]);
             }
           
        }catch(Exception $ex){
            throw new \Exception($ex->getMessage());
        }
        
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
        $data = User::find($request->user_id);
        $data->active = $request->active;
        $data->save();
        
        return response()->json(['success'=>'Status change successfully.']);
    }

    public function inactive_primary_users () {
        $days=isset($_GET['days'])?$_GET['days']:0;
        $user_activities = User::where( 'last_login', '<', Carbon::now()->subDays($days))
        ->get();
         
        $user = Auth::user();
        return view ('admin.inactive_primary_users', ['user_activities'=>$user_activities,'user'=>$user]);
    }
    
    public function inactive_beneficiary_users () {
        $days=isset($_GET['days'])?$_GET['days']:0;
        $user_activities = User::where( 'last_login', '<', Carbon::now()->subDays($days))
        ->get();
         
        $user = Auth::user();
        return view ('admin.inactive_beneficiary_users', ['user_activities'=>$user_activities,'user'=>$user]);
    }

    public function inactive_user_send_email(Request $request){
        // $table->dateTime('first_send_email');
        // $table->dateTime('second_send_email');
        // $table->dateTime('send_sms');
        // $table->dateTime('send_email_beneficiary_user');
        // $table->dateTime('send_sms_beneficiary_user');
        // $table->dateTime('final_make_call');
        // InactiveUserNotify
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
                    if($user->delete()){
                        return redirect('/primary_user')->with(['deleteMsg'=>'User has been deleted successfully!']);
                    }else{
                        echo "User not deleted";
                    }
                }
            }
        }catch(Exception $ex){
            echo "User not deleted" + $ex.getMessage();
        }
     
    }

    public function delete_beneficiary_user($user_id){
        try{
            if(!empty($user_id))  {
                $user = User::where('id','=',$user_id)->first();
                if(!empty($user)){
                    $user_email = $user->email;
                    if($user->delete()){
                        $beneficiary_user = BeneficiaryUser::where('email','=',$user_email
                        )->first();
                        if(!empty($beneficiary_user)){
                            $beneficiary_user->delete();
                        }else{
                            echo "User not deleted!";
                        }
                        
                        return redirect('/beneficiary_user')->with(['deleteMsg'=>'User has been deleted successfully!']);
                    }
                }
            }
        }catch(Exception $ex){
            echo "User not deleted" + $ex.getMessage();
        }
    }

}